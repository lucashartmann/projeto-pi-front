function calendar() {
  $('#calendar').fullCalendar({
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'month,basicWeek,basicDay'
    },
    defaultDate: '2026-04-12',
    navLinks: true,
    editable: true,
    eventLimit: true,
    events: [
      {
        title: 'All Day Event',
        start: '2026-04-01'
      },
      {
        title: 'Long Event',
        start: '2026-04-07',
        end: '2026-04-10'
      },
      {
        id: 999,
        title: 'Repeating Event',
        start: '2026-04-09T16:00:00'
      },
      {
        id: 999,
        title: 'Repeating Event',
        start: '2026-04-16T16:00:00'
      },
      {
        title: 'Conference',
        start: '2026-04-11',
        end: '2026-04-13'
      },
      {
        title: 'Meeting',
        start: '2026-04-12T10:30:00',
        end: '2026-04-12T12:30:00'
      },
      {
        title: 'Lunch',
        start: '2026-04-12T12:00:00'
      },
      {
        title: 'Meeting',
        start: '2026-04-12T14:30:00'
      },
      {
        title: 'Happy Hour',
        start: '2026-04-12T17:30:00'
      },
      {
        title: 'Dinner',
        start: '2026-04-12T20:00:00'
      },
      {
        title: 'Birthday Party',
        start: '2026-04-13T07:00:00'
      },
      {
        title: 'Click for Google',
        url: 'https://google.com/',
        start: '2026-04-28'
      }
    ]
  });
};

async function salvarEvento(data) {
  const usuario = await carregarUser();

  if (!usuario) {
    alert("Usuário não encontrado. Faça login novamente.");
    return;
  }

  let caminho_php = NULL;

  switch (usuario) {
    case "CORRETOR:":
      caminho_php = "/php/api/visitas.php?acao=cadastrarVisita";
      break;
    case "VISTORIADOR:":
      caminho_php = "/php/api/visitas.php?acao=cadastrarVistoria";
      break;
    default:
      return;
  }

  let caminho = window.location.pathname;
  substring = "";
  try {
    if (caminho.includes("/html/")) {
      caminho = caminho.replace("/html/", "/");
    }
    caminho = caminho.replace(caminho.substring(caminho.lastIndexOf("/")), caminho_php);
    fetch(caminho, {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(data)
    })
      .then(response => {
        const contentType = response.headers.get("content-type");
        if (res.erro) {
            alert("Erro ao listar atendimentos: " + res.erro);
            return null;
        }
        if (contentType && contentType.includes("application/json")) {
          return response.json();
        } else {
          const texto = response.text();
          alert("Resposta inesperada do servidor");
          console.error("Resposta não é JSON:", texto);
          return null;
        }
      })
      .then(data => {
        if (data.erro) {
          alert("Erro ao cadastrar imóvel: " + data.erro);
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

document.addEventListener('DOMContentLoaded', function () {
  calendar();

  var datas = document.querySelectorAll('.fc-day');

  datas.forEach(data => {
    data.style.cursor = 'pointer';

    data.addEventListener('click', function () {
      var dataSelecionada = this.getAttribute('data-date');

      const div = document.createElement('div');

      if (document.querySelector('.form-container')) {
        document.querySelector('.form-container').remove();
      }

      div.classList.add('form-container');

      //TODO:         <select id="imovel" name="imovel" required> </select> depois
      //TODO: adicionar hora que acaba (talvez)

      div.innerHTML = `
            <div class="form-header"><h2>Agendar visita para ${dataSelecionada}</h2>
            <button id="close-btn" onclick="this.parentElement.parentElement.remove()">X</button></div>
            <form>
            <input type="hidden" name="data" value="${dataSelecionada}">
              <label for="nome">Nome do evento:</label>
              <input type="text" id="nome" name="nome" required>
              <label for="hora">Hora do evento:</label>
              <input type="time" id="hora" name="hora" required>
              
              <button type="submit">Agendar</button>
            </form>
          `;

      div.classList.add('form-container');

      document.body.appendChild(div);
    });
  });
});