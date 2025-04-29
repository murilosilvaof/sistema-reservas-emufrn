document.addEventListener("DOMContentLoaded", function () {
  const calendar = document.getElementById("calendar");
  const salaSelect = document.getElementById("salaSelect");
  const horarios = ["08:00", "10:00", "14:00", "16:00", "18:00", "20:00"];

  function gerarDadosFake() {
    const dias = {};
    for (let i = 1; i <= 30; i++) {
      dias[i] = {};
      horarios.forEach(h => {
        dias[i][h] = Math.random() > 0.5 ? "livre" : "ocupado";
      });
    }
    return dias;
  }

  function renderCalendar(sala) {
    calendar.innerHTML = "";
    const diasMes = gerarDadosFake();
    for (let dia = 1; dia <= 30; dia++) {
      const divDia = document.createElement("div");
      divDia.classList.add("day");
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