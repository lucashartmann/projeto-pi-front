async function carregarAnuncios() {
    const dados = await listarImoveis();
    const section = document.getElementById("container_resultado");

    console.log(dados)

    if (!section || !dados) return;
    section.innerHTML = "";
    for (const imovel of dados) {
        const b64 = imovel.anuncio?.imagens?.[0] || "";

        section.innerHTML += `
            <div class="resultado" onclick="abrirCadastro(${imovel.id})">
                <img src="data:image/jpeg;base64,${b64}" alt="">
                <div class="dados">
                    <label>${imovel.id}</label>
                    <label for="">${imovel.endereco.rua}</label>
                    <label for="">${imovel.categoria}</label>
                    <label for="">${imovel.status}</label>
                </div>
            </div>
        `;
    }
}

function mudarOrdem() {
    const section = document.getElementById("container_resultado");
    filtro = document.getElementById("select_filtro").value;
    botao = document.getElementById("seta");
    botao.textContent = botao.textContent === "⬇️" ? "⬆️" : "⬇️";
    if (!section) return;
}

function filtrar() {
    filtro = document.getElementById("select_filtro").value;
    const section = document.getElementById("container_resultado");
    if (!section) return;
}

function abrirCadastro(imovel_id) {
    sessionStorage.setItem("imovel_id_estoque", imovel_id);
    window.location.href = "cadastro-imovel.html";
}

window.addEventListener("DOMContentLoaded", () => {
    carregarAnuncios();
});
