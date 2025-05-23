document.addEventListener("DOMContentLoaded", function () {
  const calendar = document.getElementById("calendar");
  const salaSelect = document.getElementById("salaSelect");

  const horarios = [];
  for (let hora = 8; hora <= 21; hora++) {
    horarios.push(`${hora.toString().padStart(2, '0')}:00`);
    horarios.push(`${hora.toString().padStart(2, '0')}:30`);
  }

  function getDiasNoMesAtual() {
    const hoje = new Date();
    const ano = hoje.getFullYear();
    const mes = hoje.getMonth();
    return new Date(ano, mes + 1, 0).getDate();
  }

  function gerarDadosFake(totalDias) {
    const dias = {};
    for (let i = 1; i <= totalDias; i++) {
      dias[i] = {};
      horarios.forEach(h => {
        dias[i][h] = Math.random() > 0.5 ? "livre" : "ocupado";
      });
    }
    return dias;
  }

  function renderCalendar(sala) {
    calendar.innerHTML = "";

    const hoje = new Date();
    const diaAtual = hoje.getDate();
    const totalDias = getDiasNoMesAtual();
    const diasMes = gerarDadosFake(totalDias);

    for (let dia = 1; dia <= totalDias; dia++) {
      const divDia = document.createElement("div");
      divDia.classList.add("day");
      if (dia === diaAtual) {
        divDia.classList.add("hoje");
      }

      const titulo = document.createElement("h4");
      titulo.textContent = `Dia ${dia}`;
      divDia.appendChild(titulo);

      horarios.forEach(horario => {
        const status = diasMes[dia][horario];
        const slot = document.createElement("div");
        slot.classList.add("slot", status);
        slot.textContent = horario;

        if (status === "livre") {
          slot.onclick = () => {
            const params = new URLSearchParams({
              dia,
              horario,
              sala
            });
            window.location.href = `formulario.html?${params.toString()}`;
          };
        }

        divDia.appendChild(slot);
      });

      calendar.appendChild(divDia);
    }
  }

  salaSelect.addEventListener("change", (e) => {
    renderCalendar(e.target.value);
  });

  renderCalendar(salaSelect.value);
});
