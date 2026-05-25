# OSCD Recrutement — Documentation technique complète

## Vue d'ensemble

Application de gestion du recrutement développée en **Laravel 13 + Inertia.js + Vue 3 + Tailwind CSS**.

**Branche GitHub** : `OSCD-devmagalie` sur `magalielastella-app/prod`

---

## Table des matières

1. [Architecture & Stack](#1-architecture--stack)
2. [Modules fonctionnels](#2-modules-fonctionnels)
3. [Modèle de données](#3-modèle-de-données)
4. [Routes API / Web](#4-routes)
5. [Structure des fichiers](#5-structure-des-fichiers)
6. [Installation & Déploiement](#6-installation--déploiement)
7. [Configuration requise](#7-configuration-requise)
8. [Intégration Claude IA](#8-intégration-claude-ia)
9. [Code source complet](#9-code-source-complet)

---

## 1. Architecture & Stack

| Composant | Technologie |
|---|---|
| Backend | Laravel 13 (PHP 8.3+) |
| Frontend | Vue 3 + Inertia.js v2 |
| CSS | Tailwind CSS 3 (palette pastel custom) |
| Base de données | PostgreSQL (ou SQLite en dev) |
| Authentification | Laravel Breeze |
| IA | API Claude (Anthropic) via HTTP |
| Hébergement | Docker → Render / Railway / Fly.io |
| PDF | dompdf (barryvdh/laravel-dompdf) |

### Contrainte technique importante

**Toutes les mutations HTTP utilisent POST** (jamais PUT / DELETE / PATCH).
Raison : le proxy Render (et d'autres reverse proxies) bloque ou réécrit
ces verbes, causant des erreurs 405 silencieuses. Tous les formulaires
Inertia utilisent `form.post()`.

---

## 2. Modules fonctionnels

### 2.1 Campagnes de recrutement (`/campagnes`)
- Créer, modifier, clôturer des campagnes
- Associer une campagne à une offre d'emploi
- Grouper les candidats par campagne
- Compteur de candidats par campagne

### 2.2 CVthèque (`/candidats`)
- Ajouter un candidat avec : prénom, nom, email, téléphone, ville, source, notes
- **Upload de CV** (PDF/Word) à la création ou depuis la fiche
- Rattacher à une **campagne** et un **métier** (job position)
- 3 statuts : `à analyser` (défaut) → `sélectionné` → `rejeté`
- Recherche full-text + filtres (statut, campagne)
- Fiche candidat détaillée avec : infos, CVs, analyses IA, comptes-rendus, événements agenda

### 2.3 Analyses IA (`/analyses`)
- **Analyse individuelle** : envoyer le profil d'un candidat à Claude pour obtenir un résumé forces/faiblesses
- **Comparaison multi-CV** : sélectionner 2-10 candidats → Claude produit une analyse comparative avec classement
- **Assistant IA conversationnel** : poser des questions sur le pool de candidats (ex : « qui habite le plus proche de La Tronche ? »)
- Sauvegarde des analyses en base (historique)
- Nécessite `ANTHROPIC_API_KEY`

### 2.4 Offres d'emploi (`/offres`)
- Créer, modifier, archiver des offres
- Champs : titre, département, lieu, type de contrat (CDI/CDD/Stage/Alternance/Intérim), description, exigences, fourchette salariale
- Statuts : brouillon → active → archivée
- Voir les candidats liés (via comptes-rendus)

### 2.5 Comptes-rendus d'entretien (`/comptes-rendus`)
- Créer un compte-rendu lié à un candidat + une offre
- Champs : date, note /5, forces, faiblesses, notes libres, recommandation (embaucher / peut-être / rejeter)
- Filtrable par candidat
- Historique du suivi au fil des entretiens

### 2.6 Scripts d'entretien (`/scripts`)
- Créer des trames de questions réutilisables
- Structure en sections dynamiques (JSON) : titre + contenu par section
- Ajouter / retirer des sections librement
- Consultable pendant l'entretien

### 2.7 Emails (`/emails`)
- **Bibliothèque de mails types** : nom, objet, corps (avec variables `{prenom}`, `{nom}`, `{poste}`, `{date}`, `{heure}`, `{lieu}`)
- **Envoi personnalisé** à un candidat depuis sa fiche
- **Historique** des emails envoyés (date, candidat, objet, statut envoyé/échec)
- Nécessite configuration SMTP (Mailgun, Resend, ou SMTP standard)

### 2.8 Agenda (`/agenda`)
- Planifier des entretiens : candidat, offre, date/heure, durée, lieu, intervieweur
- Vue liste groupée par jour
- Statuts : planifié / terminé / annulé

### 2.9 Métiers (`job_positions`)
- 5 métiers par défaut (non supprimables) :
  - Chirurgien-Dentiste
  - Assistant(e) Dentaire
  - Assistant(e) Administratif
  - Office Manager
  - Community Manager
- Possibilité d'ajouter des métiers personnalisés
- Chaque candidat peut être rattaché à un métier

### 2.10 Dashboard (`/`)
- 4 cartes statistiques : candidats total, offres actives, entretiens cette semaine, analyses IA
- 5 derniers candidats ajoutés
- 5 prochains entretiens planifiés

---

## 3. Modèle de données

### Diagramme des relations

```
recruitment_campaigns
  │
  ├── 1:N → candidates
  │           ├── 1:N → cv_documents
  │           ├── 1:N → cv_analyses
  │           ├── 1:N → interview_reports
  │           ├── 1:N → interview_events
  │           └── 1:N → sent_emails
  │
job_offers
  │
  ├── 1:N → interview_reports
  ├── 1:N → interview_events
  └── 1:1 ← recruitment_campaigns (optionnel)

job_positions
  └── 1:N → candidates

interview_scripts (autonome)
email_templates (autonome)
users (auth Laravel standard + must_change_password)
```

### Tables

#### `candidates`
| Colonne | Type | Notes |
|---|---|---|
| id | bigint PK | |
| campaign_id | FK nullable | → recruitment_campaigns |
| job_position_id | FK nullable | → job_positions |
| first_name | string | |
| last_name | string | |
| email | string nullable | |
| phone | string nullable | |
| city | string nullable | |
| status | string(20) | `a_analyser` (défaut) / `selectionne` / `rejete` |
| source | string nullable | D'où vient la candidature |
| notes | text nullable | |
| timestamps | | |

#### `cv_documents`
| Colonne | Type | Notes |
|---|---|---|
| id | bigint PK | |
| candidate_id | FK cascade | → candidates |
| original_name | string | Nom du fichier original |
| file_path | string | Chemin dans storage/app/cvs/ |
| file_size | int | En octets |
| mime_type | string | application/pdf, etc. |
| timestamps | | |

#### `cv_analyses`
| Colonne | Type | Notes |
|---|---|---|
| id | bigint PK | |
| candidate_id | FK cascade | → candidates |
| cv_document_id | FK nullable | → cv_documents |
| analysis | longText | Résultat de l'analyse Claude |
| prompt_used | text nullable | Le prompt envoyé à Claude |
| model_used | string nullable | Ex: claude-sonnet-4-20250514 |
| timestamps | | |

#### `job_offers`
| Colonne | Type | Notes |
|---|---|---|
| id | bigint PK | |
| title | string | |
| department | string nullable | |
| location | string nullable | |
| contract_type | string nullable | CDI/CDD/Stage/Alternance/Interim |
| description | longText | |
| requirements | longText nullable | |
| salary_range | string nullable | |
| status | string(20) | `draft` / `active` / `archived` |
| published_at | timestamp nullable | |
| archived_at | timestamp nullable | |
| created_by | FK nullable | → users |
| timestamps | | |

#### `interview_reports`
| Colonne | Type | Notes |
|---|---|---|
| id | bigint PK | |
| candidate_id | FK cascade | → candidates |
| job_offer_id | FK nullable | → job_offers |
| interviewer_id | FK nullable | → users |
| interview_date | date | |
| rating | tinyInt nullable | 1 à 5 |
| strengths | text nullable | Points forts |
| weaknesses | text nullable | Points faibles |
| notes | longText nullable | |
| recommendation | string nullable | `hire` / `maybe` / `reject` |
| timestamps | | |

#### `interview_scripts`
| Colonne | Type | Notes |
|---|---|---|
| id | bigint PK | |
| title | string | |
| description | text nullable | |
| sections | JSON | `[{title, content}, ...]` |
| created_by | FK nullable | → users |
| timestamps | | |

#### `interview_events`
| Colonne | Type | Notes |
|---|---|---|
| id | bigint PK | |
| candidate_id | FK cascade | → candidates |
| job_offer_id | FK nullable | → job_offers |
| interviewer_id | FK nullable | → users |
| title | string | |
| scheduled_at | datetime | |
| duration_minutes | int | Défaut 60 |
| location | string nullable | |
| notes | text nullable | |
| status | string(20) | `scheduled` / `completed` / `cancelled` |
| timestamps | | |

#### `recruitment_campaigns`
| Colonne | Type | Notes |
|---|---|---|
| id | bigint PK | |
| title | string | |
| description | text nullable | |
| job_offer_id | FK nullable | → job_offers |
| status | string(20) | `active` / `closed` |
| timestamps | | |

#### `email_templates`
| Colonne | Type | Notes |
|---|---|---|
| id | bigint PK | |
| name | string | Nom interne (ex: "Convocation entretien") |
| subject | string | Objet de l'email |
| body | longText | Corps avec variables {prenom}, {nom}... |
| description | text nullable | |
| timestamps | | |

#### `sent_emails`
| Colonne | Type | Notes |
|---|---|---|
| id | bigint PK | |
| candidate_id | FK cascade | → candidates |
| email_template_id | FK nullable | → email_templates |
| to_email | string | |
| subject | string | |
| body | longText | Corps final (variables remplacées) |
| status | string | `sent` / `failed` |
| timestamps | | |

#### `job_positions`
| Colonne | Type | Notes |
|---|---|---|
| id | bigint PK | |
| name | string unique | Ex: "Chirurgien-Dentiste" |
| is_default | boolean | Les défauts ne peuvent pas être supprimés |
| timestamps | | |

---

## 4. Routes

Toutes les routes sont dans `routes/web.php`. **Toutes les mutations utilisent POST.**

```
GET  /                                  → Dashboard
GET  /candidats                         → Liste candidats (search, status, campaign filters)
POST /candidats                         → Créer candidat (+ upload CV optionnel)
GET  /candidats/{candidate}             → Fiche candidat
POST /candidats/{candidate}             → Modifier candidat
POST /candidats/{candidate}/supprimer   → Supprimer candidat
POST /candidats/{candidate}/cv          → Upload CV
GET  /cv/{cvDocument}/telecharger       → Télécharger CV
POST /cv/{cvDocument}/supprimer         → Supprimer CV
POST /candidats/assistant               → Question à l'assistant IA (JSON response)

GET  /analyses                          → Liste analyses IA
POST /analyses                          → Lancer analyse individuelle
POST /analyses/comparer                 → Lancer analyse comparative (2-10 candidats)
POST /analyses/{analysis}/supprimer     → Supprimer analyse

GET  /campagnes                         → Liste campagnes
POST /campagnes                         → Créer campagne
POST /campagnes/{campaign}              → Modifier campagne
POST /campagnes/{campaign}/supprimer    → Supprimer campagne

GET  /offres                            → Liste offres
POST /offres                            → Créer offre
GET  /offres/{jobOffer}                 → Détail offre
POST /offres/{jobOffer}                 → Modifier offre
POST /offres/{jobOffer}/archiver        → Archiver offre
POST /offres/{jobOffer}/supprimer       → Supprimer offre

GET  /comptes-rendus                    → Liste comptes-rendus
POST /comptes-rendus                    → Créer compte-rendu
POST /comptes-rendus/{report}           → Modifier compte-rendu
POST /comptes-rendus/{report}/supprimer → Supprimer

GET  /scripts                           → Liste scripts
POST /scripts                           → Créer script
GET  /scripts/{script}                  → Détail script
POST /scripts/{script}                  → Modifier script
POST /scripts/{script}/supprimer        → Supprimer

GET  /emails                            → Modèles + historique
POST /emails/modeles                    → Créer modèle email
POST /emails/modeles/{template}         → Modifier modèle
POST /emails/modeles/{template}/supprimer → Supprimer modèle
POST /emails/envoyer                    → Envoyer email à un candidat

GET  /agenda                            → Liste événements
POST /agenda                            → Créer événement
POST /agenda/{event}                    → Modifier événement
POST /agenda/{event}/supprimer          → Supprimer événement

POST /metiers                           → Ajouter un métier
POST /metiers/{jobPosition}/supprimer   → Supprimer un métier (non-défaut)
```

---

## 5. Structure des fichiers

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── ForcePasswordChangeController.php
│   │   │   ├── RegisteredUserController.php
│   │   │   └── ... (Breeze standard)
│   │   ├── CandidateAssistantController.php    ← Assistant IA
│   │   ├── CandidateController.php
│   │   ├── CvAnalysisController.php            ← Analyse + comparaison IA
│   │   ├── CvDocumentController.php
│   │   ├── EmailTemplateController.php
│   │   ├── InterviewEventController.php
│   │   ├── InterviewReportController.php
│   │   ├── InterviewScriptController.php
│   │   ├── JobOfferController.php
│   │   ├── JobPositionController.php
│   │   ├── ProfileController.php
│   │   └── RecruitmentDashboardController.php
│   └── Middleware/
│       ├── EnsurePasswordChanged.php           ← Force changement mdp 1re connexion
│       └── HandleInertiaRequests.php
├── Models/
│   ├── Candidate.php
│   ├── CvAnalysis.php
│   ├── CvDocument.php
│   ├── EmailTemplate.php
│   ├── InterviewEvent.php
│   ├── InterviewReport.php
│   ├── InterviewScript.php
│   ├── JobOffer.php
│   ├── JobPosition.php
│   ├── RecruitmentCampaign.php
│   ├── SentEmail.php
│   └── User.php

resources/js/
├── Components/
│   ├── BrandLogo.vue
│   ├── StatusBadge.vue
│   └── ... (Breeze components)
├── Layouts/
│   ├── AuthenticatedLayout.vue                 ← Nav 9 onglets
│   └── GuestLayout.vue
├── Pages/
│   ├── Agenda/Index.vue
│   ├── Analyses/Index.vue
│   ├── Auth/ (login, register, password...)
│   ├── Campaigns/Index.vue
│   ├── Candidates/Index.vue
│   ├── Candidates/Show.vue
│   ├── Dashboard.vue
│   ├── Emails/Index.vue
│   ├── Offers/Index.vue
│   ├── Offers/Show.vue
│   ├── Reports/Index.vue
│   ├── Scripts/Index.vue
│   ├── Scripts/Show.vue
│   └── Welcome.vue

database/migrations/
├── 0001_01_01_000000_create_users_table.php     ← +must_change_password
├── 0001_01_01_000001_create_cache_table.php
├── 0001_01_01_000002_create_jobs_table.php
├── 2026_05_25_100000_create_recruitment_tables.php  ← 7 tables principales
├── 2026_05_25_100100_create_email_tables.php        ← email_templates + sent_emails
├── 2026_05_25_100200_create_campaigns_and_update_candidates.php
└── 2026_05_25_100300_create_job_positions_table.php  ← 5 métiers par défaut
```

---

## 6. Installation & Déploiement

### Développement local

```bash
git clone <repo> && cd prod
git checkout OSCD-devmagalie

composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm install && npm run dev
php artisan serve
```

### Production (Docker)

Le `Dockerfile` multi-étapes installe PHP 8.4, Node 22, Composer, build Vite, configure Apache.

```bash
docker build -t oscd-recrutement .
docker run -p 8080:8080 \
  -e APP_ENV=production \
  -e DB_CONNECTION=pgsql \
  -e DB_HOST=... \
  -e DB_DATABASE=... \
  -e DB_USERNAME=... \
  -e DB_PASSWORD=... \
  -e ANTHROPIC_API_KEY=sk-ant-... \
  oscd-recrutement
```

### Render

Le `render.yaml` est inclus (Blueprint automatique). Variables d'env à configurer :
- `APP_NAME`, `APP_ENV`, `APP_DEBUG`, `APP_URL`, `APP_LOCALE`
- `DB_*` (depuis la base PostgreSQL Render)
- `ANTHROPIC_API_KEY` (optionnel, pour l'IA)
- SMTP si envoi d'emails : `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`

---

## 7. Configuration requise

| Variable | Obligatoire | Description |
|---|---|---|
| `DB_CONNECTION` | Oui | `pgsql` ou `sqlite` |
| `DB_HOST` / `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD` | Oui (pgsql) | Connexion PostgreSQL |
| `ANTHROPIC_API_KEY` | Non | Clé API Anthropic pour les analyses IA et l'assistant. Sans cette clé, les fonctions IA affichent un message d'erreur mais l'app reste fonctionnelle. |
| `MAIL_MAILER` | Non | `smtp`, `mailgun`, `resend`... Pour l'envoi d'emails. Sans config SMTP, les emails sont en échec avec message. |
| `SESSION_LIFETIME` | Non | Défaut 240 min (4h). |

---

## 8. Intégration Claude IA

### 8.1 Analyse individuelle (`CvAnalysisController@store`)

```
POST /analyses
Body: { candidate_id, cv_document_id?, prompt? }
```

- Récupère le profil du candidat
- Envoie à Claude (claude-sonnet-4-20250514) avec un prompt personnalisable
- Sauvegarde le résultat en base (cv_analyses)

### 8.2 Comparaison multi-candidats (`CvAnalysisController@compare`)

```
POST /analyses/comparer
Body: { candidate_ids: [1, 3, 7], job_context?: "Poste d'assistante dentaire..." }
```

- Récupère les profils des 2-10 candidats sélectionnés
- Envoie à Claude avec un prompt de comparaison
- Résultat préfixé `[COMPARAISON]` et sauvegardé

### 8.3 Assistant conversationnel (`CandidateAssistantController@ask`)

```
POST /candidats/assistant
Body: { question: "Qui habite le plus proche de Grenoble ?", campaign_id?: 1 }
Response: { answer: "D'après les données..." }
```

- Récupère TOUS les candidats (ou filtrés par campagne)
- Les formate en contexte structuré (nom, email, ville, statut, source, notes...)
- Envoie à Claude comme `system` prompt + la question utilisateur
- Retourne la réponse en JSON (appel AJAX, pas de redirection Inertia)

### Configuration API

Dans `config/services.php` :
```php
'anthropic' => [
    'api_key' => env('ANTHROPIC_API_KEY'),
],
```

Toutes les requêtes passent par `Http::withHeaders(...)` (pas de SDK — appels HTTP directs).

---

## 9. Intégration dans une solution globale

### Points d'attention pour Antoine

1. **Routes POST uniquement** — ne jamais utiliser PUT/DELETE/PATCH (bloqués par certains proxies)

2. **Middleware `EnsurePasswordChanged`** — enregistré globalement dans `bootstrap/app.php`. Si la solution globale a son propre système d'auth, ce middleware peut être retiré.

3. **Tailwind custom** — la palette pastel `brand-*` est définie dans `tailwind.config.js`. Peut nécessiter un merge si la solution globale a sa propre config Tailwind.

4. **Ziggy (routes JS)** — les routes Laravel sont exposées au frontend via le package `tightenco/ziggy`. Si la solution globale utilise un routeur différent, adapter les appels `route('...')` dans les composants Vue.

5. **Inertia.js** — le frontend est en mode SPA via Inertia (pas d'API REST séparée). Si Antoine a besoin d'endpoints JSON purs, il faut créer des routes API dédiées en parallèle.

6. **Upload de fichiers** — les CVs sont stockés dans `storage/app/cvs/` (disque local). En production multi-serveur, passer à S3 ou un disque partagé (configurer `FILESYSTEM_DISK`).

7. **Claude API** — l'intégration est faite via HTTP direct. Si la solution globale utilise le SDK Anthropic PHP/JS, remplacer les appels dans `CvAnalysisController` et `CandidateAssistantController`.

---

*Document généré le 25/05/2026 — Branche `OSCD-devmagalie`*
