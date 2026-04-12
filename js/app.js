/**
 * Contrôleur principal : initialise chaque module, gère la navigation
 * entre vues, et amorce un jeu de données de démonstration au premier
 * lancement pour que l'application ne soit pas vide.
 */
(function () {
  function switchView(name) {
    document.querySelectorAll('.nav-btn').forEach(b => {
      b.classList.toggle('active', b.getAttribute('data-view') === name);
    });
    document.querySelectorAll('.view').forEach(v => {
      v.classList.toggle('active', v.id === 'view-' + name);
    });
    // Rafraîchit la vue affichée
    if (name === 'dashboard') Dashboard.refresh();
    if (name === 'inventory') Inventory.render();
    if (name === 'planning') Planning.render();
    if (name === 'hygiene') Hygiene.render();
  }

  function bindNav() {
    document.querySelectorAll('.nav-btn').forEach(btn => {
      btn.addEventListener('click', () => switchView(btn.getAttribute('data-view')));
    });
  }

  function seedDemoDataIfEmpty() {
    const s = Storage.get();
    const isFresh = s.products.length === 0 && s.employees.length === 0 &&
      s.cleaningTasks.length === 0 && s.temperatures.length === 0;
    if (!isFresh) return;

    // Produits démo
    const today = new Date();
    const inDays = (n) => {
      const d = new Date(today); d.setDate(d.getDate() + n);
      return d.toISOString().slice(0, 10);
    };
    [
      { name: 'Tomates cerises', category: 'Légumes', quantity: 8, unit: 'kg', minThreshold: 5, expiration: inDays(4), price: 3.50, supplier: 'Rungis Primeurs' },
      { name: 'Filet de boeuf', category: 'Viandes', quantity: 2, unit: 'kg', minThreshold: 3, expiration: inDays(2), price: 42.00, supplier: 'Boucherie Martin' },
      { name: 'Saumon frais', category: 'Poissons', quantity: 4, unit: 'kg', minThreshold: 2, expiration: inDays(1), price: 28.00, supplier: 'Océan Primeur' },
      { name: 'Beurre doux', category: 'Produits laitiers', quantity: 12, unit: 'kg', minThreshold: 4, expiration: inDays(20), price: 8.50, supplier: 'Laiterie Normande' },
      { name: 'Farine T55', category: 'Épicerie', quantity: 25, unit: 'kg', minThreshold: 10, expiration: inDays(120), price: 1.20, supplier: 'Moulin Dupont' },
      { name: 'Vin rouge maison', category: 'Boissons', quantity: 18, unit: 'btl', minThreshold: 12, price: 14.00, supplier: 'Domaine Leclerc' },
    ].forEach(p => Storage.add('products', p));

    // Employés démo
    [
      { name: 'Julie Durand', role: 'Chef de cuisine', email: 'julie@resto.fr' },
      { name: 'Marc Bernard', role: 'Second', email: 'marc@resto.fr' },
      { name: 'Sophie Leroy', role: 'Serveuse', email: 'sophie@resto.fr' },
      { name: 'Ahmed Khalil', role: 'Plongeur', email: 'ahmed@resto.fr' },
    ].forEach(e => Storage.add('employees', e));

    // Tâches de nettoyage démo
    [
      { zone: 'Plan de travail cuisine', frequency: 'Quotidien', lastDone: inDays(-1), agent: 'Marc Bernard' },
      { zone: 'Hotte aspirante', frequency: 'Hebdomadaire', lastDone: inDays(-10), agent: 'Ahmed Khalil' },
      { zone: 'Chambre froide', frequency: 'Hebdomadaire', lastDone: inDays(-5), agent: 'Julie Durand' },
      { zone: 'Sols salle', frequency: 'Quotidien', agent: 'Ahmed Khalil' },
      { zone: 'Vitrines', frequency: 'Bi-mensuel', lastDone: inDays(-20), agent: 'Sophie Leroy' },
    ].forEach(t => Storage.add('cleaningTasks', t));

    // Quelques relevés de température du jour
    [
      { date: Utils.todayISO(), zone: 'Réfrigérateur', temp: 3.2, time: '08:30', agent: 'Julie Durand', compliant: true },
      { date: Utils.todayISO(), zone: 'Congélateur', temp: -19.5, time: '08:32', agent: 'Julie Durand', compliant: true },
      { date: Utils.todayISO(), zone: 'Chambre froide viandes', temp: 1.8, time: '08:35', agent: 'Julie Durand', compliant: true },
    ].forEach(t => Storage.add('temperatures', t));

    Utils.toast('Données de démo chargées. Bon service !', 'success');
  }

  document.addEventListener('DOMContentLoaded', () => {
    seedDemoDataIfEmpty();
    bindNav();
    Inventory.init();
    Planning.init();
    Hygiene.init();
    Dashboard.refresh();
    switchView('dashboard');
  });
})();
