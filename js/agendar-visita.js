import { listarImoveis } from "./modules/imoveis.js";
import { usuarioLogado, carregarUser } from "./modules/usuario.js";
import { listarPessoas, listarUsuarios } from "./modules/usuarios.js";
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

async function salvarEvento(data) {
  const usuario = usuarioLogado || await carregarUser();

  if (!usuario) {
    alert("Usuário não encontrado. Faça login novamente.");
    return;
  }

  let caminhoPhp = NULL;

  switch (usuario) {
    case "CORRETOR:":
      caminhoPhp = "/php/api/visitas.php?acao=cadastrar_visita";
      break;
    case "VISTORIADOR:":
      caminhoPhp = "/php/api/visitas.php?acao=cadastrar_vistoria";
      break;
    default:
      return;
  }

  let caminho = getCaminhoRelativo(caminhoPhp);
  try {
    fetch(caminho, {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(data)
    })
      .then(async response => {
        const contentType = response.headers.get("content-type");
        if (res.erro) {
          alert("Erro ao listar atendimentos: " + res.erro);
          return null;
        }
        if (contentType && contentType.includes("application/json")) {
          return await response.json();
        } else {
          const texto = await response.text();
          alert("Resposta inesperada do servidor");
          console.error("Resposta não é JSON:", texto);
          return null;
        }
      })
      .then(async data => {
        if (data.status == "erro") {
          alert("Erro ao cadastrar imóvel: " + data.mensagem);
          return;
        }
        else if (data.mensagem) {
          alert("Imóvel cadastrado com sucesso: " + data.mensagem);
        }

      })
      .catch(error => {
        alert("Erro ao cadastrar imóvel:", error);
      });

  } catch (error) {
    console.error("Erro ao enviar dados do imóvel:", error);
  }
}

function adicionarEventoAoCalendario(evento) {
  $('#calendar').fullCalendar('renderEvent', {
    title: evento.nome,
    start: evento.data + 'T' + evento.hora
  });
  this.event.preventDefault();
}

document.addEventListener("submit", function (e) {
  if (!e.target.matches(".form-container form")) return;
  if (!dataSelecionada) {
    alert("Selecione uma data no calendário antes de agendar a visita.");
    return;
  }

  e.preventDefault();

  const formData = new FormData(e.target);
  const data = {
    nome: formData.get("nome"),
    data: formData.get("data"),
    hora: formData.get("hora"),
    imovel: formData.get("imovel")
  };

  adicionarEventoAoCalendario(data);
  // salvarEvento(data); // quando quiser salvar no backend
  e.target.closest(".form-container")?.remove();
  document.querySelector('.overlay')?.remove();
});

document.addEventListener('DOMContentLoaded', async function () {
  calendar();
  let dados = [];
  let dadosUsuarios = await listarUsuarios();
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
});