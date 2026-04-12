/**
 * Module Hygiène (HACCP) :
 * - Relevés de températures (chambres froides, congélateurs, vitrines)
 * - Plan de nettoyage (fréquence + dernier nettoyage)
 * - Réception marchandises (contrôles à réception)
 *
 * Les seuils de conformité suivent les recommandations HACCP de base :
 *   Réfrigérateur : ≤ 4°C
 *   Vitrine froide : ≤ 4°C
 *   Congélateur : ≤ -18°C
 *   Chambre froide viandes : ≤ 2°C
 */
const Hygiene = (() => {
  const ZONE_LIMITS = {
    'Réfrigérateur': { max: 4 },
    'Vitrine froide': { max: 4 },
    'Chambre froide viandes': { max: 2 },
    'Chambre froide légumes': { max: 8 },
    'Congélateur': { max: -18 },
    'Chaud (maintien)': { min: 63 },
  };
  const ZONES = Object.keys(ZONE_LIMITS);

  const FREQUENCIES = ['Quotidien', 'Hebdomadaire', 'Bi-mensuel', 'Mensuel', 'Trimestriel'];

  let currentDate = Utils.todayISO();

  function isTempCompliant(zone, temp) {
    const limit = ZONE_LIMITS[zone];
    if (!limit) return true;
    if (limit.max !== undefined && temp > limit.max) return false;
    if (limit.min !== undefined && temp < limit.min) return false;
    return true;
  }

  /* ---------------- Températures ---------------- */
  function renderTemps() {
    const body = document.getElementById('temps-body');
    const temps = Storage.getCollection('temperatures').filter(t => t.date === currentDate);
    if (temps.length === 0) {
      body.innerHTML = `<tr><td colspan="6" class="empty-row">Aucun relevé pour cette date.</td></tr>`;
      return;
    }
    body.innerHTML = temps.map(t => `
      <tr>
        <td>${escapeHtml(t.zone)}</td>
        <td><strong>${t.temp}°C</strong></td>
        <td>${escapeHtml(t.time)}</td>
        <td>${t.compliant
          ? '<span class="badge ok">Conforme</span>'
          : '<span class="badge danger">Non conforme</span>'}</td>
        <td>${escapeHtml(t.agent || '—')}</td>
        <td><button class="btn btn-sm danger" data-id="${t.id}">Suppr</button></td>
      </tr>
    `).join('');
    body.querySelectorAll('button[data-id]').forEach(btn =>
      btn.addEventListener('click', () => {
        if (Utils.confirm('Supprimer ce relevé ?')) {
          Storage.remove('temperatures', btn.getAttribute('data-id'));
          renderTemps();
          Dashboard.refresh();
        }
      })
    );
  }

  function addTemp() {
    Modal.open({
      title: 'Relevé de température',
      fields: [
        { name: 'zone', label: 'Zone', type: 'select', required: true, options: ZONES },
        { name: 'temp', label: 'Température (°C)', type: 'number', step: '0.1', required: true },
        { name: 'time', label: 'Heure', type: 'time', required: true },
        { name: 'agent', label: 'Agent (qui a relevé)', required: true },
      ],
      initial: { time: new Date().toTimeString().slice(0, 5) },
      onSubmit: (data) => {
        data.date = currentDate;
        data.compliant = isTempCompliant(data.zone, data.temp);
        Storage.add('temperatures', data);
        if (!data.compliant) {
          Utils.toast(`⚠ Température hors seuil pour ${data.zone} !`, 'error');
        } else {
          Utils.toast('Relevé enregistré', 'success');
        }
        renderTemps();
        Dashboard.refresh();
      }
    });
  }

  /* ---------------- Nettoyage ---------------- */
  function cleaningStatus(task) {
    if (!task.lastDone) return { label: 'À faire', cls: 'warn' };
    const daysSince = Utils.daysBetween(task.lastDone, Utils.todayISO());
    const limits = { 'Quotidien': 1, 'Hebdomadaire': 7, 'Bi-mensuel': 15, 'Mensuel': 30, 'Trimestriel': 90 };
    const limit = limits[task.frequency] || 7;
    if (daysSince > limit) return { label: 'En retard', cls: 'danger' };
    if (daysSince >= limit) return { label: 'À faire aujourd\'hui', cls: 'warn' };
    return { label: 'À jour', cls: 'ok' };
  }

  function renderCleaning() {
    const body = document.getElementById('cleaning-body');
    const tasks = Storage.getCollection('cleaningTasks');
    if (tasks.length === 0) {
      body.innerHTML = `<tr><td colspan="6" class="empty-row">Aucune tâche. Cliquez sur "Tâche nettoyage" pour en ajouter.</td></tr>`;
      return;
    }
    body.innerHTML = tasks.map(t => {
      const st = cleaningStatus(t);
      return `
        <tr>
          <td><strong>${escapeHtml(t.zone)}</strong></td>
          <td>${escapeHtml(t.frequency)}</td>
          <td>${Utils.formatDate(t.lastDone)}</td>
          <td>${escapeHtml(t.agent || '—')}</td>
          <td><span class="badge ${st.cls}">${st.label}</span></td>
          <td>
            <button class="btn btn-sm" data-action="done" data-id="${t.id}">Marquer fait</button>
            <button class="btn btn-sm danger" data-action="delete" data-id="${t.id}">Suppr</button>
          </td>
        </tr>
      `;
    }).join('');
    body.querySelectorAll('button[data-action]').forEach(btn =>
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const action = btn.getAttribute('data-action');
        if (action === 'done') {
          const agent = prompt('Nom de l\'agent ayant effectué le nettoyage :');
          if (agent === null) return;
          Storage.update('cleaningTasks', id, { lastDone: Utils.todayISO(), agent });
          Utils.toast('Nettoyage enregistré', 'success');
          renderCleaning();
          Dashboard.refresh();
        }
        if (action === 'delete' && Utils.confirm('Supprimer cette tâche ?')) {
          Storage.remove('cleaningTasks', id);
          renderCleaning();
          Dashboard.refresh();
        }
      })
    );
  }

  function addCleaning() {
    Modal.open({
      title: 'Tâche de nettoyage',
      fields: [
        { name: 'zone', label: 'Zone / Équipement', required: true, placeholder: 'Ex: Plan de travail cuisine' },
        { name: 'frequency', label: 'Fréquence', type: 'select', required: true, options: FREQUENCIES },
        { name: 'agent', label: 'Responsable', placeholder: 'Nom (optionnel)' },
      ],
      onSubmit: (data) => {
        Storage.add('cleaningTasks', data);
        Utils.toast('Tâche ajoutée', 'success');
        renderCleaning();
        Dashboard.refresh();
      }
    });
  }

  /* ---------------- Réception ---------------- */
  function renderDeliveries() {
    const body = document.getElementById('delivery-body');
    const list = Storage.getCollection('deliveries').filter(d => d.date === currentDate);
    if (list.length === 0) {
      body.innerHTML = `<tr><td colspan="7" class="empty-row">Aucune réception enregistrée pour cette date.</td></tr>`;
      return;
    }
    body.innerHTML = list.map(d => `
      <tr>
        <td>${escapeHtml(d.supplier)}</td>
        <td>${escapeHtml(d.product)}</td>
        <td>${d.quantity} ${escapeHtml(d.unit || '')}</td>
        <td>${d.tempDelivery != null ? d.tempDelivery + '°C' : '—'}</td>
        <td>${Utils.formatDate(d.dlc)}</td>
        <td>${d.compliant
          ? '<span class="badge ok">Conforme</span>'
          : '<span class="badge danger">Non conforme</span>'}</td>
        <td><button class="btn btn-sm danger" data-id="${d.id}">Suppr</button></td>
      </tr>
    `).join('');
    body.querySelectorAll('button[data-id]').forEach(btn =>
      btn.addEventListener('click', () => {
        if (Utils.confirm('Supprimer cette réception ?')) {
          Storage.remove('deliveries', btn.getAttribute('data-id'));
          renderDeliveries();
          Dashboard.refresh();
        }
      })
    );
  }

  function addDelivery() {
    Modal.open({
      title: 'Réception marchandise',
      fields: [
        { name: 'supplier', label: 'Fournisseur', required: true },
        { name: 'product', label: 'Produit', required: true },
        { name: 'quantity', label: 'Quantité', type: 'number', step: '0.01', required: true },
        { name: 'unit', label: 'Unité', placeholder: 'kg, L, pcs...' },
        { name: 'tempDelivery', label: 'Température livraison (°C)', type: 'number', step: '0.1' },
        { name: 'dlc', label: 'DLC / DDM', type: 'date' },
        {
          name: 'compliant',
          label: 'Conformité (emballage, DLC, température)',
          type: 'select',
          options: [{ value: 'true', label: 'Conforme' }, { value: 'false', label: 'Non conforme' }]
        },
      ],
      onSubmit: (data) => {
        data.date = currentDate;
        data.compliant = data.compliant === 'true' || data.compliant === true;
        Storage.add('deliveries', data);
        Utils.toast('Réception enregistrée', 'success');
        renderDeliveries();
        Dashboard.refresh();
      }
    });
  }

  function init() {
    const dateInput = document.getElementById('hygiene-date');
    dateInput.value = currentDate;
    dateInput.addEventListener('change', (e) => {
      currentDate = e.target.value || Utils.todayISO();
      renderTemps();
      renderDeliveries();
    });
    document.getElementById('btn-add-temp').addEventListener('click', addTemp);
    document.getElementById('btn-add-cleaning').addEventListener('click', addCleaning);
    document.getElementById('btn-add-delivery').addEventListener('click', addDelivery);
    renderTemps();
    renderCleaning();
    renderDeliveries();
  }

  function escapeHtml(s) {
    if (s == null) return '';
    return String(s).replace(/[&<>"']/g, c => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));
  }

  return { init, render: () => { renderTemps(); renderCleaning(); renderDeliveries(); }, cleaningStatus };
})();
