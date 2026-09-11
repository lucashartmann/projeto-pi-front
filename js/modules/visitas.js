import { getCaminhoRelativo } from "./utils.js";

export async function excluirVisita(imovelId) {
    let div = document.querySelector(".mensagem");
    let mensagem = "";

    if (!div) {
        div = document.createElement("div");
        div.classList.add("mensagem");
        document.body.appendChild(div);
    }

    if (!imovelId) {
        div.classList.add("erro");
        div.classList.remove("sucesso");
        mensagem = "Nenhuma visita selecionada para exclusão!";
    } else {
        try {
            let caminho = getCaminhoRelativo("/php/api/visitas.php?acao=apagar&id=" + imovelId);
            const response = await fetch(caminho, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },

            })
                .then(async (response) => {
                    if (response.erro) {
                        div.classList.add("erro");
                        div.classList.remove("sucesso");
                        mensagem = "Erro ao remover visita: " + response.erro;
                    }
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.includes("application/json")) {
                        return await response.json();
                    } else {
                        const texto = await response.text();
                        div.classList.add("erro");
                        div.classList.remove("sucesso");
                        mensagem = "Resposta inesperada do servidor";
                        console.error("Resposta não é JSON:", texto);
                    }
                })
                .then(async (data) => {
                    if (data.status == "erro") {
                        div.classList.add("erro");
                        div.classList.remove("sucesso");
                        mensagem = "Erro ao excluir visita: " + data.mensagem;
                    } else if (data.status == "sucesso") {
                        div.classList.add("sucesso");
                        div.classList.remove("erro");
                        mensagem = "Visita excluída com sucesso: " + data.mensagem;
                    } else {
                        div.classList.add("erro");
                        div.classList.remove("sucesso");
                        mensagem = "Erro ao excluir visita: " + data.mensagem;
                    }
                })
                .catch(error => {
                    div.classList.add("erro");
                    div.classList.remove("sucesso");
                    mensagem = "Erro ao excluir visita:" + error;
                });
        } catch (error) {
            div.classList.add("erro");
            div.classList.remove("sucesso");
            mensagem = "Erro ao enviar dados para exclusão da visita:" + error;
        }
    }

    div.innerText = mensagem;
    div.style.display = "flex";

    setTimeout(() => {
        div.style.display = "none";
    }, 3000);
}

export async function listarVisitas() {
  try {
    let caminho = getCaminhoRelativo("/php/api/visitas.php?acao=listar_por_corretor");
    const resposta = await fetch(caminho)
      .then(async (res) => {
        const contentType = res.headers.get("content-type");
        if (res.erro) {
          console.error("Erro ao listar visitas: " + res.erro);
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
