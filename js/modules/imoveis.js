import { getCaminhoRelativo } from "./utils.js";

export async function listarImoveis() {
    try {
        let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=listar");
        const resposta = await fetch(caminho)
            .then(async (res) => {
                const contentType = res.headers.get("content-type");
                if (res.erro) {
                    alert("Erro ao listar atendimentos: " + res.erro);
                    return null;
                }
                if (contentType && contentType.includes("application/json")) {
                    return await res.json();
                } else {
                    const texto = await res.text();
                    alert("Resposta inesperada do servidor");
                    console.error("Resposta não é JSON:", texto);
                    return;
                }
            })
            .then(async (data) => {
                if (data.status == "erro") {
                    alert("Erro ao listar imóveis: " + data.mensagem);
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
            // .then(res => console.log(res))
            .then(async (res) => {
                if (res.erro) {
                    alert("Erro ao listar atendimentos: " + res.erro);
                    return null;
                }
                const contentType = res.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    return await res.json();
                } else {
                    const texto = await res.text();
                    // alert("Resposta inesperada do servidor");
                    console.error("Resposta não é JSON:", texto);
                    return null;
                }
            })
            .then(async (data) => {
                // console.log(data);
                if (data.status == "erro") {
                    alert("Erro ao listar imóveis: " + data.mensagem);
                    return null;
                }
                return await data;
            })
            .catch(erro => {
                console.error("Falha ao conectar com o backend:", erro);
                return null;
            });

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

        return resposta;
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}

export async function getDadosImovel(id) {
    try {
        let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=get_imovel&id=" + id);
        const resposta = await fetch(caminho)
            // .then(res => console.log(res))
            .then(async (res) => {
                if (res.erro) {
                    alert("Erro ao listar atendimentos: " + res.erro);
                    return null;
                }
                const contentType = res.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    return await res.json();
                } else {
                    const texto = await res.text();
                    // alert("Resposta inesperada do servidor");
                    console.error("Resposta não é JSON:", texto);
                    return null;
                }
            })
            .then(async (data) => {
                // console.log(data);
                return await data;
            })
            .catch(erro => {
                console.error("Falha ao conectar com o backend:", erro);
                return null;
            });
        console.log("Dados do imóvel obtidos:", resposta);
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