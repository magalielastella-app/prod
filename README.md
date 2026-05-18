# Cabinet Dentaire — Entretiens annuels

Application web pour planifier, réaliser et signer les entretiens annuels
professionnels d'un cabinet dentaire.

## Profils métiers

- **Dentiste** — peut être désigné manager d'une équipe d'assistants.
- **Assistant dentaire** — remplit son auto-évaluation, signe son entretien.
- **Assistant administratif** — remplit son auto-évaluation, signe son entretien.
- **Directrice d'exploitation** — administratrice : voit tout, gère l'équipe.

## Rôles applicatifs

Chaque utilisateur a un poste (l'un des 4 ci-dessus) et un rôle applicatif :

| Rôle           | Peut faire                                              |
|----------------|---------------------------------------------------------|
| `employee`     | Remplit et signe SON entretien uniquement.              |
| `manager`      | Planifie et conduit les entretiens de ses subordonnés.  |
| `admin`        | Voit tous les entretiens, gère l'équipe, a tous droits. |

## Cycle d'un entretien

`scheduled` → `employee_draft` → `ready_for_manager` → `manager_draft`
→ `completed` → `signed`

1. Le manager **planifie** un entretien pour un salarié.
2. Le **salarié** complète son auto-évaluation, puis l'envoie au manager.
3. Le **manager** complète son appréciation, fixe les nouveaux objectifs,
   attribue une note et finalise.
4. Salarié et manager **signent** électroniquement. Une fois les deux
   signatures posées, l'entretien devient **immuable**.

## Stack

- Laravel 13 + Breeze (auth) + Inertia.js + Vue 3 + Tailwind.
- Base PostgreSQL (ou SQLite en local).

## Démarrage local

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install && npm run dev
php artisan serve
```

## Comptes de démo (seeder)

Mot de passe : `password`

- `directrice@cabinet.fr` — Directrice d'exploitation (admin)
- `dentiste@cabinet.fr` — Dentiste (manager)
- `amelie.assistante@cabinet.fr` — Assistante dentaire
- `karim.assistant@cabinet.fr` — Assistant dentaire
- `sophie.admin@cabinet.fr` — Assistante administrative

## Déploiement

Le repo contient un `Dockerfile` multi-étapes et un `render.yaml` prêt à
l'emploi.

### Render (recommandé, gratuit pour démarrer)

1. Poussez le repo sur GitHub.
2. Sur https://dashboard.render.com → **New** → **Blueprint** → pointez sur
   ce dépôt.
3. Render provisionne le service web + Postgres. Renseignez `APP_URL` avec
   l'URL publique fournie.
4. Dans le shell Render : `php artisan migrate --seed` pour créer les
   comptes de démo.

### Autres plateformes

- **Railway / Fly.io** : même principe (Dockerfile fourni), ajoutez une
  base Postgres managée et les variables `DB_*`.
- **VPS** : `docker build` + `docker run` derrière Caddy/Traefik pour le
  TLS Let's Encrypt.
