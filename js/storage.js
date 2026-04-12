/**
 * Couche de stockage persistante basée sur localStorage.
 * Toutes les données de l'app (inventaire, employés, planning, hygiène)
 * passent par ce module pour rester synchronisées et versionnables.
 */
const Storage = (() => {
  const KEY = 'restomanager_v1';

  const defaults = {
    products: [],
    categories: ['Viandes', 'Poissons', 'Légumes', 'Fruits', 'Produits laitiers', 'Épicerie', 'Boissons', 'Surgelés'],
    employees: [],
    shifts: [], // {id, employeeId, date (YYYY-MM-DD), start, end, role}
    temperatures: [], // {id, date, zone, temp, time, compliant, agent}
    cleaningTasks: [], // {id, zone, frequency, lastDone, agent, status}
    deliveries: [], // {id, date, supplier, product, quantity, tempDelivery, dlc, compliant}
  };

  function load() {
    try {
      const raw = localStorage.getItem(KEY);
      if (!raw) return structuredClone(defaults);
      const data = JSON.parse(raw);
      // Merge avec les defaults pour assurer la compatibilité ascendante
      return { ...structuredClone(defaults), ...data };
    } catch (e) {
      console.error('Erreur de chargement:', e);
      return structuredClone(defaults);
    }
  }

  function save(state) {
    localStorage.setItem(KEY, JSON.stringify(state));
  }

  let state = load();

  return {
    get() { return state; },
    getCollection(name) { return state[name] || []; },
    setCollection(name, items) {
      state[name] = items;
      save(state);
    },
    add(collection, item) {
      const id = item.id || ('id_' + Date.now() + '_' + Math.random().toString(36).slice(2, 7));
      const newItem = { ...item, id };
      state[collection] = [...(state[collection] || []), newItem];
      save(state);
      return newItem;
    },
    update(collection, id, patch) {
      state[collection] = (state[collection] || []).map(it =>
        it.id === id ? { ...it, ...patch } : it
      );
      save(state);
    },
    remove(collection, id) {
      state[collection] = (state[collection] || []).filter(it => it.id !== id);
      save(state);
    },
    reset() {
      state = structuredClone(defaults);
      save(state);
    }
  };
})();

/** Utilitaires transverses */
const Utils = {
  formatDate(d) {
    if (!d) return '—';
    const date = typeof d === 'string' ? new Date(d) : d;
    if (isNaN(date.getTime())) return d;
    return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
  },
  todayISO() {
    return new Date().toISOString().slice(0, 10);
  },
  daysBetween(d1, d2) {
    const ms = (new Date(d2)) - (new Date(d1));
    return Math.floor(ms / 86400000);
  },
  toast(message, type = '') {
    const t = document.getElementById('toast');
    t.textContent = message;
    t.className = 'toast ' + type;
    setTimeout(() => t.classList.add('hidden'), 2500);
  },
  confirm(message) {
    return window.confirm(message);
  }
};

/** Module modal simple piloté par JS */
const Modal = (() => {
  const root = document.getElementById('modal');
  const titleEl = document.getElementById('modal-title');
  const formEl = document.getElementById('modal-form');
  let currentSubmit = null;

  document.getElementById('modal-close').addEventListener('click', close);
  root.addEventListener('click', (e) => { if (e.target === root) close(); });

  function open({ title, fields, onSubmit, submitLabel = 'Enregistrer', initial = {} }) {
    // Nettoie un éventuel handler pendant (sécurité si open() ré-appelé)
    if (currentSubmit) formEl.removeEventListener('submit', currentSubmit);
    titleEl.textContent = title;
    formEl.innerHTML = '';

    // Construire les champs
    fields.forEach(field => {
      const wrap = document.createElement('div');
      wrap.className = 'form-group';
      const label = document.createElement('label');
      label.textContent = field.label;
      wrap.appendChild(label);

      let input;
      if (field.type === 'select') {
        input = document.createElement('select');
        (field.options || []).forEach(opt => {
          const o = document.createElement('option');
          o.value = typeof opt === 'string' ? opt : opt.value;
          o.textContent = typeof opt === 'string' ? opt : opt.label;
          input.appendChild(o);
        });
      } else if (field.type === 'textarea') {
        input = document.createElement('textarea');
        input.rows = 3;
      } else {
        input = document.createElement('input');
        input.type = field.type || 'text';
        if (field.step) input.step = field.step;
      }
      input.name = field.name;
      input.required = !!field.required;
      if (field.placeholder) input.placeholder = field.placeholder;
      const val = initial[field.name];
      if (val !== undefined && val !== null) input.value = val;
      wrap.appendChild(input);
      formEl.appendChild(wrap);
    });

    const actions = document.createElement('div');
    actions.className = 'modal-actions';
    const cancel = document.createElement('button');
    cancel.type = 'button';
    cancel.className = 'btn';
    cancel.textContent = 'Annuler';
    cancel.onclick = close;
    const submit = document.createElement('button');
    submit.type = 'submit';
    submit.className = 'btn primary';
    submit.textContent = submitLabel;
    actions.appendChild(cancel);
    actions.appendChild(submit);
    formEl.appendChild(actions);

    currentSubmit = (e) => {
      e.preventDefault();
      const data = {};
      fields.forEach(f => {
        const el = formEl.querySelector(`[name="${f.name}"]`);
        let v = el.value;
        if (f.type === 'number') v = v === '' ? null : parseFloat(v);
        if (f.type === 'checkbox') v = el.checked;
        data[f.name] = v;
      });
      onSubmit(data);
      close();
    };
    formEl.addEventListener('submit', currentSubmit);
    root.classList.remove('hidden');
  }

  function close() {
    root.classList.add('hidden');
    if (currentSubmit) formEl.removeEventListener('submit', currentSubmit);
    currentSubmit = null;
  }

  return { open, close };
})();
