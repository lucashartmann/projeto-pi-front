import { listarImoveis } from "./modules/imoveis.js";
import { usuarioLogado, carregarUser } from "./modules/usuario.js";
import { listarPessoas } from "./modules/pessoas.js";
import { listarVisitas, cadastrarVisita, excluirVisita } from "./modules/visitas.js";
import { listarVistorias, cadastrarVistoria, excluirVistoria } from "./modules/vistorias.js";

window.listarImoveis = listarImoveis;
let imoveisCache = [];
let proprietariosCache = [];
let usuariosCache = [];
let dataSelecionada = null;
let eventosCalendario = [];

window.listarVisitas = listarVisitas;
window.listarVistorias = listarVistorias;

function formatarTelefone(numero) {
  if (!numero) return '';

  let telefone = String(numero).replace(/\D/g, '');

  if (telefone.startsWith('55')) {
    const nacional = telefone.slice(2);

    if (nacional.length === 11) {
      return nacional.replace(
        /(\d{2})(\d{5})(\d{4})/,
        '+55 ($1) $2-$3'
      );
    }

    if (nacional.length === 10) {
      return nacional.replace(
        /(\d{2})(\d{4})(\d{4})/,
        '+55 ($1) $2-$3'
      );
    }
  }

  if (telefone.length === 11) {
    return telefone.replace(
      /(\d{2})(\d{5})(\d{4})/,
      '($1) $2-$3'
    );
  }

  if (telefone.length === 10) {
    return telefone.replace(
      /(\d{2})(\d{4})(\d{4})/,
      '($1) $2-$3'
    );
  }

  if (telefone.length > 11) {
    return '+' + telefone;
  }

  return telefone;
}

async function calendar() {
  $('#calendar').fullCalendar({
    locale: 'pt-br',

    timeFormat: 'HH:mm',

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

    eventClick: function (event, jsEvent, view) {
      jsEvent.preventDefault();

      const elementoEvento = $(jsEvent.currentTarget);

      abrirDetalhesEvento(event, elementoEvento);
      document.querySelector('#container-dados h2').textContent = "Agendar visita para" + " " + event.start.format('YYYY-MM-DD');
      document.querySelector('form input[name="nome"]').value = event.title || '';
      document.querySelector('form input[name="hora"]').value = event.start.format('HH:mm');
      document.querySelector('form input[name="data"]').value = event.start.format('YYYY-MM-DD');
      document.querySelector('form select[name="imovel"]').value = event.imovel ? event.imovel.id : "";
      document.querySelector('form select[name="cliente"]').value = event.cliente ? event.cliente.id : "";
    },

    events: eventosCalendario,

    dayClick: function (date) {
      dataSelecionada = date.format('YYYY-MM-DD');

      document.querySelector('#container-dados h2').textContent =
        `Agendar visita para ${dataSelecionada}`;
    }
  });
}

window.excluir = excluir;

function abrirDetalhesEvento(evento, elementoEvento) {



  const detalhesExistente = elementoEvento.next('.detalhes-evento');

  if (detalhesExistente.length) {
    detalhesExistente.slideUp(200, function () {
      $(this).remove();
    });

    return;
  }

  $('.detalhes-evento').slideUp(200, function () {
    $(this).remove();
  });

  const cliente = evento.cliente || {};
  const imovel = evento.imovel || {};

  const proprietarios = evento.imovel.proprietarios || [];

  const telefonesCliente = cliente.telefones
    ?.flat()
    .map(formatarTelefone)
    .join(', ') || 'Não informado';

  const proprietariosHTML = proprietarios.length
    ? proprietarios.map(proprietario => {

      const telefones = proprietario.telefones
        ?.flat()
        .map(formatarTelefone)
        .join(', ') || 'Não informado';

      return `
                <div class="proprietario-evento">
                    <strong>${proprietario.nome ?? 'Proprietário'}</strong>

                    <span>
                        <i class="fa-solid fa-phone"></i>
                        ${telefones}
                    </span>

                    <span>
                        <i class="fa-solid fa-envelope"></i>
                        ${proprietario.email ?? 'Não informado'}
                    </span>
                </div>
            `;
    }).join('')
    : '<span>Não informado</span>';

  const endereco = [
    imovel.endereco?.logradouro,
    imovel.endereco?.numero,
    imovel.endereco?.complemento,
    imovel.endereco?.bairro
  ]
    .filter(Boolean)
    .join(', ');

  const detalhes = $(`
        <div class="detalhes-evento">

            <div class="detalhes-evento-coluna">

                <h4>
                    <i class="fa-solid fa-house"></i>
                    Imóvel
                </h4>

                <p>Rua: ${imovel.endereco?.rua || 'Não informado'}</p>
                <p>Número: ${imovel.endereco?.numero || 'Não informado'}</p>
                <p>Complemento: ${imovel.endereco?.complemento || 'Não informado'}</p>
                <p>Bairro: ${imovel.endereco?.bairro || 'Não informado'}</p> 
                <p>CEP: ${imovel.endereco?.cep || 'Não informado'}</p>

            </div>

            <div class="detalhes-evento-coluna">

                <h4>
                    <i class="fa-solid fa-user"></i>
                    Cliente
                </h4>

                <p>
                    <strong>Nome:</strong>
                    ${cliente.nome ?? 'Não informado'}
                </p>

                <p>
                    <strong>Telefone:</strong>
                    ${telefonesCliente}
                </p>

                <p>
                    <strong>E-mail:</strong>
                    ${cliente.email ?? 'Não informado'}
                </p>

            </div>

            <div class="detalhes-evento-coluna">

                <h4>
                    <i class="fa-solid fa-key"></i>
                    Proprietário(s)
                </h4>

                ${proprietariosHTML}

            </div>

        </div>
    `);



  const view = $('#calendar').fullCalendar('getView');

  const detalhesAberto = $('.detalhes-evento-mes');
  const eventoAbertoId = detalhesAberto.attr('data-evento-id');

  if (
    view.name === 'month' &&
    detalhesAberto.length &&
    String(eventoAbertoId) === String(evento.id)
  ) {
    detalhesAberto.slideUp(200, function () {
      $(this).remove();
    });

    return;
  }

  if (detalhesAberto.length) {
    detalhesAberto.remove();
  }

  if (view.name === 'month') {

    const rect = elementoEvento[0].getBoundingClientRect();

    const calendarRect = document
      .querySelector('#calendar')
      .getBoundingClientRect();

    detalhes
      .addClass('detalhes-evento-mes')
      .attr('data-evento-id', evento.id);

    $('#calendar').append(detalhes);

    detalhes.css({
      top: rect.bottom - calendarRect.top + 5,
      left: rect.left - calendarRect.left
    });

    detalhes.hide().slideDown(200);

  } else {

    elementoEvento.after(detalhes);

    detalhes.hide().slideDown(200);
  }
}

async function excluir() {

  const usuario = usuarioLogado || await carregarUser();

  if (!usuario) {
    div.classList.add("erro");
    div.classList.remove("sucesso");
    mensagem = "Usuário não encontrado. Faça login novamente.";
    div.innerText = mensagem;
    div.style.display = "flex";
    return;
  }


  switch (usuario.tipo) {
    case "CORRETOR":
      excluirVisita();
      break;
    case "VISTORIADOR":
      excluirVistoria();
      break;
    default:
      div.classList.add("erro");
      div.classList.remove("sucesso");
      mensagem = "Usuário não autorizado para cadastrar eventos.";
      div.innerText = mensagem;
      div.style.display = "flex";
      return;
  }


}
async function salvarEvento(dataRecebida) {

  let div = document.querySelector(".mensagem");
  let mensagem = "";


  div = document.createElement("div");
  div.classList.add("mensagem");
  document.body.appendChild(div);


  const usuario = usuarioLogado || await carregarUser();

  if (!usuario) {
    div.classList.add("erro");
    div.classList.remove("sucesso");
    mensagem = "Usuário não encontrado. Faça login novamente.";
    div.innerText = mensagem;
    div.style.display = "flex";
    return;
  }


  switch (usuario.tipo) {
    case "CORRETOR":
      cadastrarVisita(dataRecebida);
      break;
    case "VISTORIADOR":
      cadastrarVistoria(dataRecebida);
      break;
    default:
      div.classList.add("erro");
      div.classList.remove("sucesso");
      mensagem = "Usuário não autorizado para cadastrar eventos.";
      div.innerText = mensagem;
      div.style.display = "flex";
      return;
  }

}

function adicionarEventoAoCalendario(evento) {
  const novoEvento = {
    title: evento.nome,
    start: `${evento.data}T${evento.hora}`,
    imovel: evento.imovel || '',
    cliente: evento.cliente || ''
  };
  eventosCalendario.push(novoEvento);
  console.log("Evento adicionado ao calendário:", novoEvento);
  $('#calendar').fullCalendar('renderEvent', novoEvento, true);
}

document.querySelector('form')?.addEventListener('submit', function (e) {
  event.preventDefault();
  let formData = new FormData(e.target);
  console.log("Data selecionada:", dataSelecionada);
  if (!dataSelecionada) {
    alert("Selecione uma data no calendário antes de agendar a visita.");
    return;
  }
  let data = {
    nome: formData.get("nome"),
    data: dataSelecionada,
    hora: formData.get("hora"),
    imovel: formData.get("imovel")?.split('-')[0].trim(),
    cliente: formData.get("cliente")?.split('-')[0].trim(),
    enviarEmail: formData.get("confirmar") === "on" ? true : false
  };
  salvarEvento(data);
});

function montarEventos(tipoUsuario) {
  if (tipoUsuario === "CORRETOR") {
    listarVisitas().then(visitas => {
      if (visitas && visitas.length > 0) {
        visitas.forEach(visita => {
          adicionarEventoAoCalendario({
            nome: visita.nome,
            data: visita.data?.split(' ')[0],
            hora: visita.data?.split(' ')[1],
            imovel: visita.imovel,
            cliente: visita.cliente,
            id: visita.id
          });
        });
      }
    });
  } else if (tipoUsuario === "VISTORIADOR") {
    listarVistorias().then(vistorias => {
      if (vistorias && vistorias.length > 0) {
        vistorias.forEach(vistoria => {
          adicionarEventoAoCalendario({
            nome: vistoria.nome,
            data: vistoria.data?.split(' ')[0],
            hora: vistoria.data?.split(' ')[1],
            imovel: visita.imovel,
            cliente: visita.cliente,
            id: visita.id
          });
        });
      }
    });
  }
}



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

  montarEventos(usuario.tipo);

  const idCliente = new URLSearchParams(window.location.search).get('idCliente');
  const idImovel = new URLSearchParams(window.location.search).get('idImovel');

  if (idCliente) {
    document.querySelector('form select[name="cliente"]').value = idCliente || '';
  }
  if (idImovel) {
    document.querySelector('form select[name="imovel"]').value = idImovel || '';
  }


});