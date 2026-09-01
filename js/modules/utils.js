export function getCaminhoRelativo(destino) {
    let caminho = window.location.pathname;
    let substring = "";
    if (
        caminho === "localhost" ||
        caminho === "127.0.0.1"
    ) {
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
    else {
        return destino;
    }

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