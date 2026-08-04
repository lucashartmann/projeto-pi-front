import { listarImoveis } from "./modules/imoveis.js";
import { usuarioLogado, carregarUser } from "./modules/usuario.js";
import { listarPessoas, listarUsuarios } from "./modules/usuarios.js";
import { listarProprietarios } from "./modules/proprietarios.js";
import { getCaminhoRelativo } from "./modules/utils.js";

window.listarImoveis = listarImoveis;
let imoveisCache = [];
let proprietariosCache = [];
let usuariosCache = [];

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
});

document.addEventListener('DOMContentLoaded', async function () {
  calendar();

  var datas = document.querySelectorAll('.fc-day');

  let dados = [];
  let dadosUsuarios = await listarUsuarios();
  let dadosProprietarios = await listarProprietarios();
  dadosUsuarios = dadosUsuarios?.filter(usuario => usuario.tipo === "CLIENTE");
  dados = [...dadosUsuarios, ...dadosProprietarios];
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

  datas.forEach(data => {
    data.style.cursor = 'pointer';

    data.addEventListener('click', function () {
      var dataSelecionada = this.getAttribute('data-date');

      const div = document.createElement('div');

      if (document.querySelector('.form-container')) {
        document.querySelector('.form-container').remove();
      }

      div.classList.add('form-container');

      div.innerHTML = `
            <div class="form-header"><h2>Agendar visita para ${dataSelecionada}</h2>
            <button id="close-btn" onclick="this.parentElement.parentElement.remove()">X</button></div>
            <form>
            <input type="hidden" name="data" value="${dataSelecionada}" placeholder="">
              <label for="nome">Nome do evento:</label>
              <input type="text" id="nome" name="nome" required placeholder="">
              <label for="hora">Hora do evento:</label>
              <input type="time" id="hora" name="hora" required placeholder="">
              <label for="cliente">Cliente:</label>
              <select id="cliente" name="cliente" required>
                <option value="" selected>Selecione uma opção...</option>
                ${usuariosCache.map(usuario => `<option value="${usuario.id}">${usuario.nome}</option>`).join('')}          
              </select>
              <label for="imovel">Imóvel:</label>
              <select id="imovel" name="imovel" required>
                <option value="" selected>Selecione uma opção...</option>
                ${imoveisCache.map(imovel => `<option value="${imovel.id}">${imovel.endereco}</option>`).join('')}
              </select>
              <div class="checkbox-container">
                <input type="checkbox" id="confirmar" name="confirmar" placeholder="">
                <label for="">Mandar email para cliente?</label>
              </div>
              <button type="submit">Agendar</button>
            </form>
          `;

      div.classList.add('form-container');

      document.body.appendChild(div);
    });
  });
});