document.addEventListener("DOMContentLoaded", function () {
  const calendar    = document.getElementById("calendar");
  const salaSelect  = document.getElementById("salaSelect");

  // GERAÇÃO DE HORÁRIOS (de 2 em 2h, com :00 e :30)
const horarios = [];
const faixasValidas = [7, 9,11,14, 16, 19,20]; // horas que começam as faixas

for (let hora of faixasValidas) {
  horarios.push(`${hora.toString().padStart(2, '0')}:30`);
}


  // Estado atual do mês/ano exibidos
  let current = new Date();

  // Cria controles de navegação (‹ mês atrás | Mês ANO | mês à frente ›)
  const nav = document.createElement("div");
  nav.style.display = "flex";
  nav.style.justifyContent = "center";
  nav.style.alignItems = "center";
  nav.style.marginBottom = "1rem";
  const prev = document.createElement("button");
  prev.textContent = "‹";
  prev.style.fontSize = "1.2rem";
  prev.style.marginRight = "1rem";
  const label = document.createElement("span");
  label.style.fontWeight = "bold";
  label.style.minWidth = "8em";
  label.style.textAlign = "center";
  const next = document.createElement("button");
  next.textContent = "›";
  next.style.fontSize = "1.2rem";
  next.style.marginLeft = "1rem";
  nav.append(prev, label, next);
  calendar.parentNode.insertBefore(nav, calendar);

  function getDiasNoMes(year, month) {
    return new Date(year, month + 1, 0).getDate();
  }

  function formatMonthYear(date) {
    const meses = ["Janeiro","Fevereiro","Março","Abril","Maio","Junho",
                   "Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"];
    return `${meses[date.getMonth()]} de ${date.getFullYear()}`;
  }

  function renderCalendar(sala) {
    calendar.innerHTML = "";
    const year      = current.getFullYear();
    const month     = current.getMonth();
    const hoje      = new Date();
    const diaAtual  = (hoje.getFullYear() === year && hoje.getMonth() === month)
                      ? hoje.getDate()
                      : null;
    const totalDias = getDiasNoMes(year, month);
    label.textContent = formatMonthYear(current);

    for (let dia = 1; dia <= totalDias; dia++) {
      const divDia = document.createElement("div");
      divDia.classList.add("day");
      if (dia === diaAtual) divDia.classList.add("hoje");

      // título do dia
      const data = new Date(year, month, dia);
      const diasSemana = ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"];
      const titulo = document.createElement("h4");
      titulo.textContent = `${diasSemana[data.getDay()]}, ${dia}`;
      divDia.appendChild(titulo);

      // slots
      horarios.forEach(horario => {
        let status = "livre";
        // fds sempre ocupado
        if (data.getDay() === 0 || data.getDay() === 6) {
          status = "ocupado";
        }
        const slot = document.createElement("div");
        slot.classList.add("slot", status);
        slot.textContent = horario;
        if (status === "livre") {
          slot.onclick = () => {
            const params = new URLSearchParams({ dia, horario, sala });
            window.location.href = `formulario.html?${params}`;
          };
        }
        divDia.appendChild(slot);
      });

      calendar.appendChild(divDia);
    }
  }

  // eventos dos botões
  prev.addEventListener("click", () => {
    current.setMonth(current.getMonth() - 1);
    renderCalendar(salaSelect.value);
  });
  next.addEventListener("click", () => {
    current.setMonth(current.getMonth() + 1);
    renderCalendar(salaSelect.value);
  });

  salaSelect.addEventListener("change", () => {
    renderCalendar(salaSelect.value);
  });

  // inicializa
  renderCalendar(salaSelect.value);
});
