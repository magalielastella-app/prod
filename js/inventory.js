/**
 * Module Inventaire : gestion des produits en stock, seuils, péremptions,
 * fournisseurs. Affiche le stock sous forme de tableau filtrable.
 */
const Inventory = (() => {
  let searchTerm = '';
  let categoryFilter = '';

  function productStatus(p) {
    const today = Utils.todayISO();
    if (p.expiration && p.expiration < today) return { label: 'Périmé', cls: 'danger' };
    if (p.expiration && Utils.daysBetween(today, p.expiration) <= 3) return { label: 'Bientôt périmé', cls: 'warn' };
    if (p.quantity <= 0) return { label: 'Rupture', cls: 'danger' };
    if (p.minThreshold != null && p.quantity <= p.minThreshold) return { label: 'Stock bas', cls: 'warn' };
    return { label: 'OK', cls: 'ok' };
  }

  function filtered() {
    const products = Storage.getCollection('products');
    return products.filter(p => {
      const okSearch = !searchTerm ||
        (p.name || '').toLowerCase().includes(searchTerm) ||
        (p.supplier || '').toLowerCase().includes(searchTerm);
      const okCat = !categoryFilter || p.category === categoryFilter;
      return okSearch && okCat;
    });
  }

  function render() {
    const body = document.getElementById('inventory-body');
    const items = filtered();
    if (items.length === 0) {
      body.innerHTML = `<tr><td colspan="9" class="empty-row">Aucun produit. Cliquez sur "Nouveau produit" pour commencer.</td></tr>`;
      return;
    }
    body.innerHTML = items.map(p => {
      const st = productStatus(p);
      return `
        <tr>
          <td><strong>${escapeHtml(p.name)}</strong></td>
          <td>${escapeHtml(p.category || '—')}</td>
          <td>${p.quantity} ${escapeHtml(p.unit || '')}</td>
          <td>${p.minThreshold ?? '—'}</td>
          <td>${Utils.formatDate(p.expiration)}</td>
          <td>${p.price != null ? p.price.toFixed(2) + ' €' : '—'}</td>
          <td>${escapeHtml(p.supplier || '—')}</td>
          <td><span class="badge ${st.cls}">${st.label}</span></td>
          <td>
            <button class="btn btn-sm" data-action="edit" data-id="${p.id}">Modifier</button>
            <button class="btn btn-sm danger" data-action="delete" data-id="${p.id}">Suppr</button>
          </td>
        </tr>
      `;
    }).join('');

    // Attacher les événements
    body.querySelectorAll('button[data-action]').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const action = btn.getAttribute('data-action');
        if (action === 'edit') editProduct(id);
        if (action === 'delete') deleteProduct(id);
      });
    });
  }

  function refreshCategoryFilter() {
    const sel = document.getElementById('inv-filter-category');
    const current = sel.value;
    const cats = Storage.get().categories;
    sel.innerHTML = '<option value="">Toutes catégories</option>' +
      cats.map(c => `<option value="${escapeHtml(c)}">${escapeHtml(c)}</option>`).join('');
    sel.value = current;
  }

  function productFields(initial = {}) {
    return [
      { name: 'name', label: 'Nom du produit', required: true, placeholder: 'Ex: Tomates cerises' },
      { name: 'category', label: 'Catégorie', type: 'select', options: Storage.get().categories },
      { name: 'quantity', label: 'Quantité', type: 'number', step: '0.01', required: true },
      { name: 'unit', label: 'Unité', placeholder: 'kg, L, pcs...' },
      { name: 'minThreshold', label: 'Seuil d\'alerte (mini)', type: 'number', step: '0.01' },
      { name: 'expiration', label: 'Date de péremption', type: 'date' },
      { name: 'price', label: 'Prix unitaire (€)', type: 'number', step: '0.01' },
      { name: 'supplier', label: 'Fournisseur' },
    ];
  }

  function addProduct() {
    Modal.open({
      title: 'Nouveau produit',
      fields: productFields(),
      onSubmit: (data) => {
        Storage.add('products', data);
        Utils.toast('Produit ajouté', 'success');
        render();
        Dashboard.refresh();
      }
    });
  }

  function editProduct(id) {
    const product = Storage.getCollection('products').find(p => p.id === id);
    if (!product) return;
    Modal.open({
      title: 'Modifier le produit',
      fields: productFields(product),
      initial: product,
      onSubmit: (data) => {
        Storage.update('products', id, data);
        Utils.toast('Produit mis à jour', 'success');
        render();
        Dashboard.refresh();
      }
    });
  }

  function deleteProduct(id) {
    const product = Storage.getCollection('products').find(p => p.id === id);
    if (!product) return;
    if (!Utils.confirm(`Supprimer "${product.name}" ?`)) return;
    Storage.remove('products', id);
    Utils.toast('Produit supprimé');
    render();
    Dashboard.refresh();
  }

  function init() {
    document.getElementById('btn-add-product').addEventListener('click', addProduct);
    document.getElementById('inv-search').addEventListener('input', (e) => {
      searchTerm = e.target.value.toLowerCase();
      render();
    });
    document.getElementById('inv-filter-category').addEventListener('change', (e) => {
      categoryFilter = e.target.value;
      render();
    });
    refreshCategoryFilter();
    render();
  }

  function escapeHtml(s) {
    if (s == null) return '';
    return String(s).replace(/[&<>"']/g, c => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));
  }

  return { init, render, productStatus };
})();
