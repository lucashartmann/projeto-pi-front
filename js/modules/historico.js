import { getCaminhoRelativo } from "./utils.js";

export async function listarHistoricoPorIdImovel(id) {
    const url = getCaminhoRelativo(`/php/api/historico.php?acao=listarPorIdImovel&id=${id}`);
    const response = await fetch(url);

    if (!response.ok) {
        console.error(`Erro na requisição: ${response.status}`);
    }

    if (response.headers.get("Content-Type")?.includes("application/json")) {
        const data = await response.json();
        return data;
    } else {
        console.error("Resposta não é JSON");
    }
}

export async function listarHistoricoPorIdCliente(id) {
    const url = getCaminhoRelativo(`/php/api/historico.php?acao=listarPorIdCliente&id=${id}`);
    const response = await fetch(url);

    if (!response.ok) {
        console.error(`Erro na requisição: ${response.status}`);
    }

    if (response.headers.get("Content-Type")?.includes("application/json")) {
        const data = await response.json();
        return data;
    } else {
        console.error("Resposta não é JSON");
    }
}

