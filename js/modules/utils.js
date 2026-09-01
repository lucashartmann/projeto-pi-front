export function getCaminhoRelativo(destino) {
    let caminho = window.location.pathname;
    let host = window.location.hostname;
    let substring = "";

    if (destino.includes("/php/")) {
        if (
            host !== "localhost" &&
            host !== "127.0.0.1"
        ) {
            return destino;
        }
    }
    if (caminho.includes("/html/")) {
        caminho = caminho.replace(caminho.substring(caminho.lastIndexOf("/html/")), "/");
    }
    if (caminho.includes("/html")) {
        caminho = caminho.replace(caminho.substring(caminho.lastIndexOf("/html")), "/");
    }
    if (caminho.includes("/index.html")) {
        caminho = caminho.replace(caminho.substring(caminho.lastIndexOf("/index.html")), "/");
    }
    if (caminho.slice(-2) != "//") {
        caminho += "/";
    }
    const regex = new RegExp("/" + "$");
    caminho = caminho.replace(regex, destino);
    return caminho;
}

export function formatarValor(valor) {
    const formatoMoeda = new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(valor);
    return formatoMoeda;
}

export function getNumeroTelefone() {
    const numero = process.env.NUMERO;
    return numero;
}