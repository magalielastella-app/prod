/**
 * Tableau de bord : agrège les informations des trois modules pour offrir
 * une vue synthétique (stocks critiques, équipe, contrôles hygiène).
 */
const Dashboard = (() => {
  function refresh() {
    const products = Storage.getCollection('products');
    const today = Utils.todayISO();

    // Stats produits
    document.getElementById('stat-products').textContent = products.length;

    const lowStock = products.filter(p => {
      const s = Inventory.productStatus(p);
      return s.cls === 'warn' && s.label.includes('Stock') || s.label === 'Rupture';
    });
    document.getElementById('stat-lowstock').textContent = lowStock.length;

    const expiring = products.filter(p => {
      if (!p.expiration) return false;
      const days = Utils.daysBetween(today, p.expiration);
      return days <= 3;
    });
    document.getElementById('stat-expiring').textContent = expiring.length;

    // Stats employés / planning
    const employees = Storage.getCollection('employees');
    document.getElementById('stat-employees').textContent = employees.length;
    document.getElementById('stat-hours').textContent = Planning.weeklyHours() + ' h';

    // Stats hygiène : contrôles du jour
    const todaysTemps = Storage.getCollection('temperatures').filter(t => t.date === today).length;
    const todaysDeliveries = Storage.getCollection('deliveries').filter(d => d.date === today).length;
    document.getElementById('stat-hygiene').textContent = todaysTemps + todaysDeliveries;

    // Alertes récentes
    const alertsEl = document.getElementById('alerts-list');
    const alerts = [];
    lowStock.forEach(p => alerts.push({
      label: `Stock bas : ${p.name} (${p.quantity} ${p.unit || ''})`,
      type: 'warn'
    }));
    expiring.forEach(p => {
      const days = Utils.daysBetween(today, p.expiration);
      alerts.push({
        label: days < 0
          ? `Périmé : ${p.name} (${Utils.formatDate(p.expiration)})`
          : `Péremption dans ${days}j : ${p.name}`,
        type: days < 0 ? 'danger' : 'warn'
      });
    });
    Storage.getCollection('temperatures')
      .filter(t => t.date === today && !t.compliant)
      .forEach(t => alerts.push({
        label: `Température non conforme : ${t.zone} (${t.temp}°C)`,
        type: 'danger'
      }));

    if (alerts.length === 0) {
      alertsEl.innerHTML = '<li class="empty">Aucune alerte — tout est en ordre ✓</li>';
    } else {
      alertsEl.innerHTML = alerts.slice(0, 10).map(a => `
        <li>
          <span>${escapeHtml(a.label)}</span>
          <span class="badge ${a.type}">${a.type === 'danger' ? 'Critique' : 'Attention'}</span>
        </li>
      `).join('');
    }

    // Prochaines tâches d'hygiène
    const nextEl = document.getElementById('next-hygiene');
    const pendingTasks = Storage.getCollection('cleaningTasks')
      .map(t => ({ ...t, status: Hygiene.cleaningStatus(t) }))
      .filter(t => t.status.cls !== 'ok')
      .slice(0, 10);
    if (pendingTasks.length === 0) {
      nextEl.innerHTML = '<li class="empty">Aucune tâche en attente ✓</li>';
    } else {
      nextEl.innerHTML = pendingTasks.map(t => `
        <li>
          <span>${escapeHtml(t.zone)} <small class="text-muted">(${escapeHtml(t.frequency)})</small></span>
          <span class="badge ${t.status.cls}">${t.status.label}</span>
        </li>
      `).join('');
    }
  }

  function escapeHtml(s) {
    if (s == null) return '';
    return String(s).replace(/[&<>"']/g, c => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));
  }

  return { refresh };
})();
