/**
 * Module Planning : gère l'équipe et les créneaux de travail sur une grille
 * hebdomadaire. Les créneaux sont rattachés à une date et à un employé.
 */
const Planning = (() => {
  const DAYS = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
  let currentMonday = mondayOf(new Date());

  function mondayOf(date) {
    const d = new Date(date);
    const day = d.getDay();
    const diff = (day === 0 ? -6 : 1) - day;
    d.setDate(d.getDate() + diff);
    d.setHours(0, 0, 0, 0);
    return d;
  }

  function addDays(date, n) {
    const d = new Date(date);
    d.setDate(d.getDate() + n);
    return d;
  }

  function isoDate(d) {
    return d.toISOString().slice(0, 10);
  }

  function weekDates() {
    return Array.from({ length: 7 }, (_, i) => addDays(currentMonday, i));
  }

  function renderWeekLabel() {
    const end = addDays(currentMonday, 6);
    document.getElementById('week-label').textContent =
      `${Utils.formatDate(currentMonday)} — ${Utils.formatDate(end)}`;
  }

  function renderEmployees() {
    const list = document.getElementById('employees-list');
    const employees = Storage.getCollection('employees');
    if (employees.length === 0) {
      list.innerHTML = '<span class="text-muted">Aucun employé. Ajoutez votre équipe pour créer des créneaux.</span>';
      return;
    }
    list.innerHTML = employees.map(e => `
      <span class="chip">
        <strong>${escapeHtml(e.name)}</strong>
        <span class="text-muted">${escapeHtml(e.role || '')}</span>
        <button class="chip-del" data-id="${e.id}" title="Supprimer">×</button>
      </span>
    `).join('');
    list.querySelectorAll('.chip-del').forEach(btn => {
      btn.addEventListener('click', () => deleteEmployee(btn.getAttribute('data-id')));
    });
  }

  function renderGrid() {
    const header = document.getElementById('planning-header');
    const body = document.getElementById('planning-body');
    const dates = weekDates();

    header.innerHTML = '<th>Employé</th>' +
      dates.map((d, i) => `<th>${DAYS[i]}<br><span class="text-muted">${Utils.formatDate(d)}</span></th>`).join('');

    const employees = Storage.getCollection('employees');
    const shifts = Storage.getCollection('shifts');

    if (employees.length === 0) {
      body.innerHTML = `<tr><td colspan="8" class="empty-row">Ajoutez au moins un employé pour générer le planning.</td></tr>`;
      return;
    }

    body.innerHTML = employees.map(emp => {
      const cells = dates.map(date => {
        const dayShifts = shifts.filter(s => s.employeeId === emp.id && s.date === isoDate(date));
        const html = dayShifts.map(s => `
          <span class="shift-item" title="${escapeHtml(s.role || '')}">
            ${escapeHtml(s.start)}–${escapeHtml(s.end)}
            ${s.role ? '<br><small>' + escapeHtml(s.role) + '</small>' : ''}
            <button class="shift-del" data-id="${s.id}" title="Supprimer">×</button>
          </span>
        `).join('');
        return `<td>${html}</td>`;
      }).join('');
      return `<tr><td class="employee-name">${escapeHtml(emp.name)}</td>${cells}</tr>`;
    }).join('');

    body.querySelectorAll('.shift-del').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        deleteShift(btn.getAttribute('data-id'));
      });
    });
  }

  function addEmployee() {
    Modal.open({
      title: 'Nouvel employé',
      fields: [
        { name: 'name', label: 'Nom complet', required: true, placeholder: 'Ex: Julie Durand' },
        { name: 'role', label: 'Poste', placeholder: 'Chef, Serveur, Plongeur...' },
        { name: 'email', label: 'Email', type: 'email' },
        { name: 'phone', label: 'Téléphone' },
      ],
      onSubmit: (data) => {
        Storage.add('employees', data);
        Utils.toast('Employé ajouté', 'success');
        renderEmployees();
        renderGrid();
        Dashboard.refresh();
      }
    });
  }

  function deleteEmployee(id) {
    const emp = Storage.getCollection('employees').find(e => e.id === id);
    if (!emp) return;
    if (!Utils.confirm(`Supprimer ${emp.name} ? Ses créneaux seront également supprimés.`)) return;
    Storage.remove('employees', id);
    // Supprime tous les créneaux associés
    const remaining = Storage.getCollection('shifts').filter(s => s.employeeId !== id);
    Storage.setCollection('shifts', remaining);
    Utils.toast('Employé supprimé');
    renderEmployees();
    renderGrid();
    Dashboard.refresh();
  }

  function addShift() {
    const employees = Storage.getCollection('employees');
    if (employees.length === 0) {
      Utils.toast('Ajoutez d\'abord un employé', 'error');
      return;
    }
    Modal.open({
      title: 'Nouveau créneau',
      fields: [
        {
          name: 'employeeId',
          label: 'Employé',
          type: 'select',
          required: true,
          options: employees.map(e => ({ value: e.id, label: `${e.name} (${e.role || '—'})` }))
        },
        { name: 'date', label: 'Date', type: 'date', required: true },
        { name: 'start', label: 'Début', type: 'time', required: true },
        { name: 'end', label: 'Fin', type: 'time', required: true },
        { name: 'role', label: 'Poste/service', placeholder: 'Service midi, Soir...' },
      ],
      initial: { date: isoDate(new Date()) },
      onSubmit: (data) => {
        if (data.end <= data.start) {
          Utils.toast('L\'heure de fin doit être après le début', 'error');
          return;
        }
        Storage.add('shifts', data);
        Utils.toast('Créneau ajouté', 'success');
        renderGrid();
        Dashboard.refresh();
      }
    });
  }

  function deleteShift(id) {
    Storage.remove('shifts', id);
    renderGrid();
    Dashboard.refresh();
  }

  function prevWeek() {
    currentMonday = addDays(currentMonday, -7);
    renderWeekLabel();
    renderGrid();
  }

  function nextWeek() {
    currentMonday = addDays(currentMonday, 7);
    renderWeekLabel();
    renderGrid();
  }

  function weeklyHours() {
    const dates = weekDates().map(isoDate);
    const shifts = Storage.getCollection('shifts').filter(s => dates.includes(s.date));
    let total = 0;
    shifts.forEach(s => {
      const [sh, sm] = (s.start || '0:0').split(':').map(Number);
      const [eh, em] = (s.end || '0:0').split(':').map(Number);
      total += ((eh * 60 + em) - (sh * 60 + sm)) / 60;
    });
    return Math.round(total * 10) / 10;
  }

  function init() {
    document.getElementById('btn-prev-week').addEventListener('click', prevWeek);
    document.getElementById('btn-next-week').addEventListener('click', nextWeek);
    document.getElementById('btn-add-employee').addEventListener('click', addEmployee);
    document.getElementById('btn-add-shift').addEventListener('click', addShift);
    renderWeekLabel();
    renderEmployees();
    renderGrid();
  }

  function escapeHtml(s) {
    if (s == null) return '';
    return String(s).replace(/[&<>"']/g, c => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));
  }

  return { init, render: () => { renderEmployees(); renderGrid(); }, weeklyHours };
})();
