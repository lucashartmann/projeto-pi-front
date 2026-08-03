import { getCaminhoRelativo } from "./utils.js";

export async function listarImoveis() {
    try {
        let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=listar");
        const resposta = await fetch(caminho)
            .then(async (res) => {
                const contentType = res.headers.get("content-type");
                if (res.erro) {
                    console.error("Erro ao listar atendimentos: " + res.erro);
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

export async function listarImoveisDisponiveis() {
    try {
        let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=listar_disponiveis");
        const resposta = await fetch(caminho)
            .then(async (res) => {
                if (res.erro) {
                    console.error("Erro ao listar atendimentos: " + res.erro);
                    return null;
                }
                const contentType = res.headers.get("content-type");
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
                return await data;
            })
            .catch(erro => {
                console.error("Falha ao conectar com o backend:", erro);
                return null;
            });

        if (!resposta || !Array.isArray(resposta)) {
            console.error("Resposta inválida ao listar imóveis disponíveis:", resposta);
            return null;
        }

        resposta.forEach(imovel => {
            switch (imovel.status) {
                case "Venda":
                    imovel.valor_aluguel = null;
                    break;
                case "Aluguel":
                    imovel.valor_venda = null;
                    break;
                default:
                    break;
            }
        });

        let imoveis = [];
        imoveis = resposta.filter(imovel => imovel.anuncio && imovel.anuncio.imagens && imovel.anuncio.imagens.length > 0);

        return imoveis;
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}

export async function listarImoveisDestacados() {
    try {
        let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=listar_destacados");
        const resposta = await fetch(caminho)
            .then(async (res) => {
                if (res.erro) {
                    console.error("Erro ao listar imoveis destacados: " + res.erro);
                    return null;
                }
                const contentType = res.headers.get("content-type");
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
                return await data;
            })
            .catch(erro => {
                console.error("Falha ao conectar com o backend:", erro);
                return null;
            });

        if (!resposta || !Array.isArray(resposta)) {
            console.error("Resposta inválida ao listar imóveis destacados:", resposta);
            return null;
        }

        resposta.forEach(imovel => {
            switch (imovel.status) {
                case "Venda":
                    imovel.valor_aluguel = null;
                    break;
                case "Aluguel":
                    imovel.valor_venda = null;
                    break;
                default:
                    break;
            }
        });

        let imoveis = [];
        imoveis = resposta.filter(imovel => imovel.anuncio && imovel.anuncio.imagens && imovel.anuncio.imagens.length > 0);

        return imoveis;
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}

export async function getDadosImovel(id) {
    try {
        let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=get_imovel&id=" + id);
        const resposta = await fetch(caminho)
            .then(async (res) => {
                if (res.erro) {
                    console.error("Erro ao listar atendimentos: " + res.erro);
                    return null;
                }
                const contentType = res.headers.get("content-type");
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
                return await data;
            })
            .catch(erro => {
                console.error("Falha ao conectar com o backend:", erro);
                return null;
            });
        if (resposta && Array.isArray(resposta) && resposta.length > 0) {
            return resposta[0];
        } else if (resposta && typeof resposta === "object") {
            return resposta;
        } else {
            console.error("Resposta inválida ao obter dados do imóvel:", resposta);
            return null;
        }
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}