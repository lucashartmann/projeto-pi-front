import { getCaminhoRelativo } from "./utils.js";

export async function listarHistoricoPorIdImovel(id) {
    try {
        const url = getCaminhoRelativo(`/php/api/historico.php?acao=listarPorIdImovel&id=${id}`);
        const response = await fetch(url);

        if (!response.ok) {
            console.error(`Erro na requisição: ${response.status}`);
            return [];
        }

        if (response.status == "erro") {
            console.error("Erro ao listar histórico: " + response.mensagem);
            return [];
        }

        if (response.headers.get("Content-Type")?.includes("application/json")) {
            return await response.json();
        } else {
            console.error("Resposta não é JSON");
            return [];
        }
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}

export async function listarHistoricoPorIdCliente(id) {
    try {
        const url = getCaminhoRelativo(`/php/api/historico.php?acao=listarPorIdCliente&id=${id}`);
        const response = await fetch(url);

        if (!response.ok) {
            console.error(`Erro na requisição: ${response.status}`);
            return [];
        }

        if (response.status == "erro") {
            console.error("Erro ao listar histórico: " + response.mensagem);
            return [];
        }

        if (response.headers.get("Content-Type")?.includes("application/json")) {
            return await response.json();
        } else {
            console.error("Resposta não é JSON");
            return [];
        }
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}

