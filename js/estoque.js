async function carregarAnuncios() {
    const dados = await listarImoveis();
    const section = document.getElementById("container-resultado");

    console.log(dados)

    if (!section || !dados) return;
    section.innerHTML = "";
    for (let imovel of dados) {
        const b64 = imovel.anuncio?.imagens?.[0] || null;
        section.innerHTML += `
            <div class="resultado" onclick="abrirCadastro(${imovel.id})">
                <img src="${b64}" alt="">
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
    const section = document.getElementById("container-resultado");
    filtro = document.getElementById("select-filtro").value;
    botao = document.getElementById("seta");
    botao.textContent = botao.textContent === "⬇️" ? "⬆️" : "⬇️";
    if (!section) return;
}

function filtrar() {
    filtro = document.getElementById("select-filtro").value;
    const section = document.getElementById("container-resultado");
    if (!section) return;
}

function abrirCadastro(imovel_id) {
    sessionStorage.setItem("imovel-id-estoque", imovel_id);
    window.location.href = "cadastro-imovel.html";
}

window.addEventListener("DOMContentLoaded", () => {
    carregarAnuncios();
});
