import { getCaminhoRelativo } from "./utils.js";

export async function excluirVistoria(imovelId) {
  let divPai = document.querySelector("#container-mensagens");
  let mensagem = "";
  let div = document.createElement("div");

  if (!divPai) {
    divPai = document.createElement("div");
    divPai.id = "container-mensagens";
    document.body.appendChild(divPai);
  }

  div.classList.add("mensagem");
  divPai.appendChild(div);

  if (!imovelId) {
    div.classList.add("erro");
    div.classList.remove("sucesso");
    mensagem = "Nenhuma vistoria selecionada para exclusão!";
  } else {
    try {
      let caminho = getCaminhoRelativo("/php/api/vistorias.php?acao=apagar&id=" + imovelId);
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
            mensagem = "Erro ao remover vistoria: " + response.erro;
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
            mensagem = "Erro ao excluir vistoria: " + data.mensagem;
          } else if (data.status == "sucesso") {
            div.classList.add("sucesso");
            div.classList.remove("erro");
            mensagem = "Vistoria excluída com sucesso: " + data.mensagem;
          } else {
            div.classList.add("erro");
            div.classList.remove("sucesso");
            mensagem = "Erro ao excluir vistoria: " + data.mensagem;
          }
        })
        .catch(error => {
          div.classList.add("erro");
          div.classList.remove("sucesso");
          mensagem = "Erro ao excluir vistoria:" + error;
        });
    } catch (error) {
      div.classList.add("erro");
      div.classList.remove("sucesso");
      mensagem = "Erro ao enviar dados para exclusão da vistoria:" + error;
    }
  }

  if (mensagem) {
    div.innerText = mensagem;
    div.style.display = "flex";
  }
}

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


export function cadastrarVistoria(formData) {
  let div = document.querySelector(".mensagem");
  let mensagem = "";


  div = document.createElement("div");
  div.classList.add("mensagem");
  document.body.appendChild(div);
  try {
    let caminho = getCaminhoRelativo("/php/api/vistorias.php?acao=cadastrar");
    fetch(caminho, {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(formData)
    })
      .then(async response => {
        const contentType = response.headers.get("content-type");
        if (response.erro) {
          div.classList.add("erro");
          div.classList.remove("sucesso");
          mensagem = "Erro ao cadastrar evento: " + response.erro;
        }
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
      .then(async data => {
        if (data.status == "erro") {
          div.classList.add("erro");
          div.classList.remove("sucesso");
          mensagem = "Erro ao cadastrar evento: " + data.mensagem;
        }
        else if (data.mensagem) {
          div.classList.add("sucesso");
          div.classList.remove("erro");
          mensagem = "Evento cadastrado com sucesso: " + data.mensagem;
          adicionarEventoAoCalendario(dataRecebida);
        }

      })
      .catch(error => {
        div.classList.add("erro");
        div.classList.remove("sucesso");
        mensagem = "Erro ao cadastrar evento:", error;
      });

  } catch (error) {
    div.classList.add("erro");
    div.classList.remove("sucesso");
    mensagem = "Erro ao enviar dados do imóvel:" + error;
  }
  if (mensagem) {
    div.innerText = mensagem;
    div.style.display = "flex";
  }
}