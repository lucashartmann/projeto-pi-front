from flask import Flask, jsonify, request
from PIL import Image, UnidentifiedImageError
from transformers import AutoImageProcessor, SegformerForSemanticSegmentation
import torch
import torch.nn.functional as F
import numpy as np
import os
import cv2
from io import BytesIO

app = Flask(__name__)

PASTA_RESULTADOS = os.path.join("static", "resultados")
os.makedirs(PASTA_RESULTADOS, exist_ok=True)

# MODELO = "nvidia/segformer-b0-finetuned-ade-512-512"
# MODELO = "nvidia/segformer-b1-finetuned-ade-512-512"
# MODELO = "nvidia/segformer-b2-finetuned-ade-512-512"
# MODELO = "nvidia/segformer-b3-finetuned-ade-512-512"
# MODELO = "nvidia/segformer-b4-finetuned-ade-512-512"
MODELO = "nvidia/segformer-b5-finetuned-ade-640-640"

device = torch.device("cuda" if torch.cuda.is_available() else "cpu")

print("Usando:", device)
print("Carregando modelo...")

processor = AutoImageProcessor.from_pretrained(MODELO)

model = SegformerForSemanticSegmentation.from_pretrained(MODELO)
model.to(device)
model.eval()

print("Modelo carregado.")

def hex_para_rgb(hex_cor):
    if not isinstance(hex_cor, str):
        raise ValueError("A cor deve ser um texto hexadecimal.")

    hex_cor = hex_cor.strip().lstrip("#")
    if len(hex_cor) != 6:
        raise ValueError("A cor deve estar no formato #RRGGBB.")

    try:
        componentes = tuple(int(hex_cor[i : i + 2], 16) for i in (0, 2, 4))
    except ValueError as erro:
        raise ValueError("A cor deve estar no formato #RRGGBB.") from erro

    return componentes


def rgb_para_hsl_pixel(r, g, b):
    r /= 255.0
    g /= 255.0
    b /= 255.0

    maxc = max(r, g, b)
    minc = min(r, g, b)
    l = (maxc + minc) / 2.0

    if maxc == minc:
        h = 0.0
        s = 0.0
    else:
        d = maxc - minc
        s = d / (2.0 - maxc - minc) if l > 0.5 else d / (maxc + minc)
        if maxc == r:
            h = ((g - b) / d + (6 if g < b else 0)) / 6.0
        elif maxc == g:
            h = ((b - r) / d + 2) / 6.0
        else:
            h = ((r - g) / d + 4) / 6.0

    return h, s, l


def hsl_para_rgb_pixel(h, s, l):
    def hue2rgb(p, q, t):
        if t < 0:
            t += 1
        if t > 1:
            t -= 1
        if t < 1 / 6:
            return p + (q - p) * 6 * t
        if t < 1 / 2:
            return q
        if t < 2 / 3:
            return p + (q - p) * (2 / 3 - t) * 6
        return p

    if s == 0:
        r = g = b = l
    else:
        q = l * (1 + s) if l < 0.5 else l + s - l * s
        p = 2 * l - q
        r = hue2rgb(p, q, h + 1 / 3)
        g = hue2rgb(p, q, h)
        b = hue2rgb(p, q, h - 1 / 3)

    return int(r * 255), int(g * 255), int(b * 255)


def recolorir_parede(imagem, mascara, cor):
    imagem_np = np.array(imagem).copy()
    mascara = mascara.astype(bool)

    if mascara.sum() == 0:
        return imagem

    r_alvo, g_alvo, b_alvo = cor
    h_alvo, s_alvo, l_alvo = rgb_para_hsl_pixel(r_alvo, g_alvo, b_alvo)

    intensidade = 0.85

    ys, xs = np.where(mascara)

    for y, x in zip(ys, xs):
        r, g, b = imagem_np[y, x]
        h_orig, s_orig, l_orig = rgb_para_hsl_pixel(int(r), int(g), int(b))
        h_novo = h_alvo
        s_novo = s_orig * 0.25 + s_alvo * 0.75
        l_novo = l_orig * 0.20 + l_alvo * 0.80
        r2, g2, b2 = hsl_para_rgb_pixel(h_novo, s_novo, l_novo)
        imagem_np[y, x, 0] = int(r * (1 - intensidade) + r2 * intensidade)
        imagem_np[y, x, 1] = int(g * (1 - intensidade) + g2 * intensidade)
        imagem_np[y, x, 2] = int(b * (1 - intensidade) + b2 * intensidade)

    return Image.fromarray(imagem_np)


def refinar_mascara_parede(imagem_pil, wall_prob):
    img = np.array(imagem_pil)
    h, w = wall_prob.shape
    gc_mask = np.full((h, w), cv2.GC_PR_BGD, dtype=np.uint8)
    gc_mask[wall_prob < 0.10] = cv2.GC_BGD
    gc_mask[(wall_prob >= 0.10) & (wall_prob < 0.45)] = cv2.GC_PR_BGD
    gc_mask[(wall_prob >= 0.45) & (wall_prob < 0.75)] = cv2.GC_PR_FGD
    gc_mask[wall_prob >= 0.75] = cv2.GC_FGD
    gray = cv2.cvtColor(img, cv2.COLOR_RGB2GRAY)
    gc_mask[gray < 20] = cv2.GC_BGD
    bgdModel = np.zeros((1, 65), np.float64)
    fgdModel = np.zeros((1, 65), np.float64)
    try:
        cv2.grabCut(
            img, gc_mask, None, bgdModel, fgdModel, 5, cv2.GC_INIT_WITH_MASK
        )
    except cv2.error:
        pass

    mascara = np.where(
        (gc_mask == cv2.GC_FGD) | (gc_mask == cv2.GC_PR_FGD), 1, 0
    ).astype(np.uint8)
    kernel = np.ones((5, 5), np.uint8)
    mascara = cv2.morphologyEx(mascara, cv2.MORPH_OPEN, kernel)
    mascara = cv2.morphologyEx(mascara, cv2.MORPH_CLOSE, kernel)
    num_labels, labels, stats, _ = cv2.connectedComponentsWithStats(mascara, 8)
    mascara_final = np.zeros_like(mascara)
    area_total = h * w

    for i in range(1, num_labels):
        _, y, _, _, area = stats[i]
        toca_borda_superior = y <= int(h * 0.05)
        grande_o_bastante = area > area_total * 0.01
        if toca_borda_superior or grande_o_bastante:
            mascara_final[labels == i] = 1

    return mascara_final.astype(bool)


@app.route("/processar", methods=["POST"])
def processar():
    if "imagem" not in request.files:
        return jsonify({"erro": "Nenhuma imagem enviada."}), 400

    arquivo = request.files["imagem"]
    if not arquivo.filename:
        return jsonify({"erro": "Selecione uma imagem para enviar."}), 400

    cor_hex = request.form.get("cor", "#d4c7b5")
    try:
        cor_rgb = hex_para_rgb(cor_hex)
    except ValueError as erro:
        return jsonify({"erro": str(erro)}), 400

    try:
        imagem = Image.open(arquivo.stream)
        imagem.verify()
        arquivo.stream.seek(0)
        imagem = Image.open(arquivo.stream).convert("RGB")
    except (UnidentifiedImageError, OSError):
        return jsonify({"erro": "O arquivo enviado não é uma imagem válida."}), 400

    largura_original, altura_original = imagem.size
    inputs = processor(images=imagem, return_tensors="pt")
    inputs = {chave: valor.to(device) for chave, valor in inputs.items()}

    with torch.inference_mode():
        outputs = model(**inputs)

    logits = outputs.logits

    logits = F.interpolate(
        logits,
        size=(altura_original, largura_original),
        mode="bilinear",
        align_corners=False,
    )

    probs = torch.softmax(logits, dim=1)[0]
    wall_prob = probs[0].cpu().numpy()
    mascara_parede = refinar_mascara_parede(imagem, wall_prob)
    quantidade = int(mascara_parede.sum())

    if quantidade == 0:
        return jsonify({"erro": "Nenhuma parede foi detectada."}), 400

    resultado = recolorir_parede(imagem, mascara_parede, cor_rgb)
    porcentagem = (quantidade / (largura_original * altura_original)) * 100
    buffer = BytesIO()
    resultado.save(buffer, format="WEBP", quality=95)
    blob = buffer.getvalue()

    return jsonify(
        {
            "quantidade_pixels": quantidade,
            "porcentagem_parede": porcentagem,
            "blob": blob.hex(),
        }
    )


if __name__ == "__main__":
    app.run(debug=True, port=5000)
