import { getCaminhoRelativo } from "./utils.js";


export async function listarVistorias() {
  try {
    let caminho = getCaminhoRelativo("/php/api/vistorias.php?acao=listar_por_vistoriador");
    const resposta = await fetch(caminho)
      .then(async (res) => {
        const contentType = res.headers.get("content-type");
        if (res.erro) {
          console.error("Erro ao listar vistorias: " + res.erro);
          return null;
        }
        if (contentType && contentType.includes("application/json")) {
          return await res.json();
        } else {
          const texto = await res.text();
          console.error("Resposta não é JSON:", texto);
          return null;
        }
      })
      .then(async (data) => {
        if (data.status == "erro") {
          console.error(data.mensagem);
          return null;
        }
        return data;
      })
      .catch(erro => {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
      });

    return resposta;
  } catch (erro) {
    console.error("Falha ao conectar com o backend:", erro);
    return null;
  }
}