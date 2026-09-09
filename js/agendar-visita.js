import { listarImoveis } from "./modules/imoveis.js";
import { usuarioLogado, carregarUser } from "./modules/usuario.js";
import { listarPessoas } from "./modules/usuarios.js";
import { getCaminhoRelativo } from "./modules/utils.js";

window.listarImoveis = listarImoveis;
let imoveisCache = [];
let proprietariosCache = [];
let usuariosCache = [];
let dataSelecionada = null;

async function calendar() {
  await $('#calendar').fullCalendar({
    locale: 'pt-br',
    buttonText: {
      today: 'Hoje',
      month: 'Mês',
      week: 'Semana',
      day: 'Dia'
    },
    dayNames: [
      'Domingo', 'Segunda', 'Terça', 'Quarta',
      'Quinta', 'Sexta', 'Sábado'
    ],
    dayNamesShort: [
      'Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'
    ],
    monthNames: [
      'Janeiro', 'Fevereiro', 'Março', 'Abril',
      'Maio', 'Junho', 'Julho', 'Agosto',
      'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ],
    monthNamesShort: [
      'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun',
      'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'
    ],
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'month,basicWeek,basicDay'
    },
    defaultDate: new Date().toISOString().slice(0, 10),
    navLinks: true,
    editable: true,
    eventLimit: true,
    height: $('#pai-calendario').height(),
    handleWindowResize: true,
    width: $('#pai-calendario').width(),
    dayClick: function (date) {
      dataSelecionada = date.format('YYYY-MM-DD');
      document.querySelector('#container-dados h2').textContent = `Agendar visita para ${dataSelecionada}`;
    }
  });
};

async function salvarEvento(dataRecebida) {

  let div = document.querySelector(".mensagem");
  let mensagem = "";

  if (!div) {
    div = document.createElement("div");
    div.classList.add("mensagem");
    document.body.appendChild(div);
  }

  const usuario = usuarioLogado || await carregarUser();

  if (!usuario) {
    div.classList.add("erro");
    div.classList.remove("sucesso");
    mensagem = "Usuário não encontrado. Faça login novamente.";
    div.innerText = mensagem;
    div.style.display = "flex";

    setTimeout(() => {
      div.style.display = "none";
    }, 3000);
    return;
  }

  let caminhoPhp = '';

  switch (usuario.tipo) {
    case "CORRETOR":
      console.log("Caminho PHP para corretor");
      caminhoPhp = "/php/api/visitas.php?acao=cadastrar";
      break;
    case "VISTORIADOR":
      caminhoPhp = "/php/api/vistorias.php?acao=cadastrar";
      break;
    default:
      div.classList.add("erro");
      div.classList.remove("sucesso");
      mensagem = "Usuário não autorizado para cadastrar eventos.";
      div.innerText = mensagem;
      div.style.display = "flex";
      setTimeout(() => {
        div.style.display = "none";
      }, 3000);
      return;
  }

  let caminho = getCaminhoRelativo(caminhoPhp);
  try {
    fetch(caminho, {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(dataRecebida)
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

  div.innerText = mensagem;
  div.style.display = "flex";

  setTimeout(() => {
    div.style.display = "none";
  }, 3000);
}

function adicionarEventoAoCalendario(evento) {
  $('#calendar').fullCalendar('renderEvent', {
    title: evento.nome,
    start: evento.data + 'T' + evento.hora
  });
  this.event.preventDefault();
}

document.querySelector('form')?.addEventListener('submit', function (e) {
  event.preventDefault();
  let formData = new FormData(e.target);
  let data = {
    nome: formData.get("nome"),
    data: formData.get("data"),
    hora: formData.get("hora"),
    imovel: formData.get("imovel")?.split('-')[0].trim(),
    cliente: formData.get("cliente")?.split('-')[0].trim(),
    enviarEmail: formData.get("confirmar") === "on" ? true : false
  };
  salvarEvento(data);
});

// document.addEventListener("submit", function (e) {
//   if (!e.target.matches(".form-container form")) return;
//   if (!dataSelecionada) {
//     alert("Selecione uma data no calendário antes de agendar a visita.");
//     return;
//   }

//   e.preventDefault();

//   const formData = new FormData(e.target);
//   const data = {
//     nome: formData.get("nome"),
//     data: formData.get("data"),
//     hora: formData.get("hora"),
//     imovel: formData.get("imovel")
//   };

//   adicionarEventoAoCalendario(data);
//   // salvarEvento(data); // quando quiser salvar no backend
//   e.target.closest(".form-container")?.remove();
//   document.querySelector('.overlay')?.remove();
// });

document.addEventListener('DOMContentLoaded', async function () {
  calendar();
  let dados = [];
  let dadosUsuarios = await listarPessoas();
  dadosUsuarios = dadosUsuarios?.filter(usuario => usuario.tipo === "CLIENTE");
  dados = [...dadosUsuarios];
  if (dados.length === 0 || !dados) {
    const section = document.getElementById("container-pai");
    const divVazio = document.createElement("div");
    divVazio.id = "vazio";
    divVazio.textContent = "Nenhum usuário encontrado.";
    section.innerHTML = "";
    section.appendChild(divVazio);
    return;
  }
  dados.sort((a, b) => new Date(b.data_cadastro?.date) - new Date(a.data_cadastro?.date));
  usuariosCache.push(...dados);

  dados = [];
  dados = await listarImoveis();
  if (dados.length === 0 || !dados) {
    const section = document.getElementById("container-pai");
    const divVazio = document.createElement("div");
    divVazio.id = "vazio";
    divVazio.textContent = "Nenhum imóvel encontrado.";
    section.innerHTML = "";
    section.appendChild(divVazio);
    return;
  }
  dados.sort((a, b) => new Date(b.data_cadastro?.date) - new Date(a.data_cadastro?.date));
  imoveisCache.push(...dados);

  document.querySelector('select[name="cliente"]').innerHTML = `
   <option value="">Selecione uma opção...</option>
            ${usuariosCache.map(usuario =>
    `<option value="${usuario.id}">${usuario.id} - ${usuario.nome}</option>`
  ).join('')}
  `;

  document.querySelector('select[name="imovel"]').innerHTML = `
       <option value="">Selecione uma opção...</option>
            ${imoveisCache.map(imovel =>
    `<option value="${imovel.id}">${imovel.id} - ${imovel.endereco?.rua}, ${imovel.endereco?.numero}/${imovel.endereco?.complemento}</option>`
  ).join('')}
  `;

  let usuario = usuarioLogado || await carregarUser();

  if (usuario.tipo == "VISTORIADOR") {
    document.querySelector('.cliente-separator').style.display = "none";
    document.querySelector('.checkbox-container label').innerHTML = "Mandar email para proprietário?";
  }

});