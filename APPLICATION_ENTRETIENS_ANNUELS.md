# Application Entretiens Annuels — Cabinet Dentaire de l'Obiou

Code source complet pour réimplantation dans un autre projet.

**Stack** : Laravel 13 + Inertia.js + Vue 3 + Tailwind CSS + PostgreSQL

---

# Table des matières

1. Configuration (composer.json, package.json, tailwind, vite, Dockerfile)
2. Routes
3. Modèles
4. Contrôleurs
5. Policies & Middleware
6. Support (Positions, Templates)
7. Migrations
8. Seeders
9. Vues Blade
10. Composants Vue
11. Layouts Vue
12. Pages Vue
13. CSS
14. Docker & Déploiement

---

# 1. Configuration

## `composer.json`

```json
{
    "$schema": "https://getcomposer.org/schema.json",
    "name": "laravel/laravel",
    "type": "project",
    "description": "The skeleton application for the Laravel framework.",
    "keywords": ["laravel", "framework"],
    "license": "MIT",
    "require": {
        "php": "^8.3",
        "barryvdh/laravel-dompdf": "^3.0",
        "inertiajs/inertia-laravel": "^2.0",
        "laravel/framework": "^13.0",
        "laravel/sanctum": "^4.0",
        "laravel/tinker": "^3.0",
        "tightenco/ziggy": "^2.0"
    },
    "require-dev": {
        "fakerphp/faker": "^1.23",
        "laravel/breeze": "^2.4",
        "laravel/pail": "^1.2.5",
        "laravel/pint": "^1.27",
        "mockery/mockery": "^1.6",
        "nunomaduro/collision": "^8.6",
        "phpunit/phpunit": "^12.5.12"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "scripts": {
        "setup": [
            "composer install",
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\"",
            "@php artisan key:generate",
            "@php artisan migrate --force",
            "npm install --ignore-scripts",
            "npm run build"
        ],
        "dev": [
            "Composer\\Config::disableProcessTimeout",
            "npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74\" \"php artisan serve\" \"php artisan queue:listen --tries=1 --timeout=0\" \"php artisan pail --timeout=0\" \"npm run dev\" --names=server,queue,logs,vite --kill-others"
        ],
        "test": [
            "@php artisan config:clear --ansi",
            "@php artisan test"
        ],
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi"
        ],
        "post-update-cmd": [
            "@php artisan vendor:publish --tag=laravel-assets --ansi --force"
        ],
        "post-root-package-install": [
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ],
        "post-create-project-cmd": [
            "@php artisan key:generate --ansi",
            "@php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\"",
            "@php artisan migrate --graceful --ansi"
        ],
        "pre-package-uninstall": [
            "Illuminate\\Foundation\\ComposerScripts::prePackageUninstall"
        ]
    },
    "extra": {
        "laravel": {
            "dont-discover": []
        }
    },
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true,
        "allow-plugins": {
            "pestphp/pest-plugin": true,
            "php-http/discovery": true
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}

```

---

## `package.json`

```json
{
    "$schema": "https://www.schemastore.org/package.json",
    "private": true,
    "type": "module",
    "scripts": {
        "build": "vite build",
        "dev": "vite"
    },
    "devDependencies": {
        "@inertiajs/vue3": "^2.0.0",
        "@tailwindcss/forms": "^0.5.3",
        "@tailwindcss/vite": "^4.0.0",
        "@vitejs/plugin-vue": "^6.0.0",
        "autoprefixer": "^10.4.12",
        "axios": ">=1.11.0 <=1.14.0",
        "concurrently": "^9.0.1",
        "laravel-vite-plugin": "^3.0.0",
        "postcss": "^8.4.31",
        "tailwindcss": "^3.2.1",
        "vite": "^8.0.0",
        "vue": "^3.4.0"
    }
}

```

---

## `tailwind.config.js`

```js
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            // Palette pastelle — Cabinet Dentaire de l'Obiou.
            // Le teal du logo reste l'identité ; les fonds, statuts et
            // tuiles passent en pastel doux pour un rendu apaisant.
            colors: {
                brand: {
                    // Teal — actions, liens, boutons primaires
                    primary: '#14B8A6',
                    'primary-dark': '#0F766E',
                    'primary-light': '#5EEAD4',
                    'primary-bg': '#0F4C47',     // fond logo (sombre, conservé)

                    // Pastels d'accent — pour cartes, status, sections
                    mint: '#A7F3D0',
                    peach: '#FED7AA',
                    rose: '#FBCFE8',
                    lavender: '#E9D5FF',
                    butter: '#FEF3C7',
                    sky: '#BAE6FD',
                    coral: '#FECACA',
                    ice: '#CFFAFE',

                    // Neutres très clairs
                    cream: '#FAFAF9',            // fond général
                    beige: '#F1F5F9',            // séparateurs doux
                    tan: '#CBD5E1',              // bordures contrastées
                    tertiary: '#F0FDFA',         // teinte très douce mint
                    dark: '#0F172A',             // texte principal
                },
            },
            backgroundImage: {
                // Gradient principal — pastel mint → ciel → lavande, gardant un peu
                // de teal pour la signature visuelle. Texte foncé recommandé.
                'brand-gradient': 'linear-gradient(135deg, #5EEAD4 0%, #BAE6FD 50%, #DDD6FE 100%)',
                'brand-soft': 'linear-gradient(135deg, #F0FDFA 0%, #FAFAF9 100%)',
                'hero-splash': 'radial-gradient(ellipse at top left, rgba(94,234,212,0.25), transparent 60%), radial-gradient(ellipse at bottom right, rgba(251,207,232,0.22), transparent 60%)',
            },
            boxShadow: {
                'soft': '0 4px 20px -4px rgba(15, 23, 42, 0.06)',
                'glow-primary': '0 0 0 4px rgba(94, 234, 212, 0.20)',
                'glow-amber': '0 0 0 4px rgba(254, 215, 170, 0.30)',
            },
            animation: {
                'soft-pulse': 'soft-pulse 2.5s ease-in-out infinite',
                'fade-in': 'fade-in 0.4s ease-out',
            },
            keyframes: {
                'soft-pulse': {
                    '0%, 100%': { opacity: '1' },
                    '50%': { opacity: '0.75' },
                },
                'fade-in': {
                    '0%': { opacity: '0', transform: 'translateY(4px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },

    plugins: [forms],
};

```

---

## `vite.config.js`

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});

```

---

## `bootstrap/app.php`

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\EnsurePasswordChanged::class,
        ]);

        // Derrière le load-balancer HTTPS de Render/Railway/Fly : faire
        // confiance à tous les proxys pour que les URLs Laravel restent en https.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

```

---

## `.env.example`

```example
APP_NAME="Cabinet Dentaire"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

APP_LOCALE=fr
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=fr_FR

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

# PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
# CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

```

---

# 2. Routes

## `routes/web.php`

```php
<?php

use App\Http\Controllers\AnnualReviewController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profil (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/supprimer', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ---------- Entretiens annuels ----------
    // Toutes les mutations passent par POST : certains proxys (Render notamment)
    // bloquent ou réécrivent les verbes PUT / DELETE, ce qui se traduit par des
    // 405 côté navigateur. POST est universellement accepté.
    Route::get('/entretiens', [AnnualReviewController::class, 'index'])->name('reviews.index');
    Route::post('/entretiens', [AnnualReviewController::class, 'store'])->name('reviews.store');
    Route::get('/entretiens/{review}', [AnnualReviewController::class, 'show'])->name('reviews.show');
    Route::get('/entretiens/{review}/imprimer', [AnnualReviewController::class, 'printable'])->name('reviews.print');
    Route::get('/entretiens/{review}/pdf', [AnnualReviewController::class, 'downloadPdf'])->name('reviews.pdf');
    Route::post('/entretiens/{review}/salarie', [AnnualReviewController::class, 'employeeUpdate'])->name('reviews.employee.update');
    Route::post('/entretiens/{review}/manager', [AnnualReviewController::class, 'managerUpdate'])->name('reviews.manager.update');
    Route::post('/entretiens/{review}/signer', [AnnualReviewController::class, 'sign'])->name('reviews.sign');
    Route::post('/entretiens/{review}/reprogrammer', [AnnualReviewController::class, 'reschedule'])->name('reviews.reschedule');
    Route::post('/entretiens/{review}/supprimer', [AnnualReviewController::class, 'destroy'])->name('reviews.destroy');

    // ---------- Trames d'entretien (admin) ----------
    Route::get('/trames', [TemplateController::class, 'index'])->name('templates.index');
    Route::get('/trames/{key}', [TemplateController::class, 'edit'])->name('templates.edit');
    Route::post('/trames/{key}', [TemplateController::class, 'update'])->name('templates.update');

    // ---------- Équipe (admin) ----------
    Route::get('/equipe', [TeamController::class, 'index'])->name('team.index');
    Route::post('/equipe', [TeamController::class, 'store'])->name('team.store');
    Route::post('/equipe/{user}', [TeamController::class, 'update'])->name('team.update');
    Route::post('/equipe/{user}/supprimer', [TeamController::class, 'destroy'])->name('team.destroy');
});

require __DIR__.'/auth.php';

```

---

## `routes/auth.php`

```php
<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ForcePasswordChangeController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::post('password', [PasswordController::class, 'update'])->name('password.update');

    // Changement de mot de passe obligatoire (première connexion)
    Route::get('changer-mot-de-passe', [ForcePasswordChangeController::class, 'show'])
        ->name('password.force-change');
    Route::post('changer-mot-de-passe', [ForcePasswordChangeController::class, 'update'])
        ->name('password.force-change.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

```

---

# 3. Modèles

## `app/Models/User.php`

```php
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'position', 'department', 'hired_on', 'manager_id', 'must_change_password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_EMPLOYEE = 'employee';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_ADMIN = 'admin';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'hired_on' => 'date',
        ];
    }

    public function isManager(): bool
    {
        return in_array($this->role, [self::ROLE_MANAGER, self::ROLE_ADMIN], true);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(self::class, 'manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(self::class, 'manager_id');
    }

    public function annualReviews(): HasMany
    {
        return $this->hasMany(AnnualReview::class, 'employee_id');
    }

    public function managedReviews(): HasMany
    {
        return $this->hasMany(AnnualReview::class, 'manager_id');
    }
}

```

---

## `app/Models/AnnualReview.php`

```php
<?php

namespace App\Models;

use App\Support\ReviewTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnualReview extends Model
{
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_EMPLOYEE_DRAFT = 'employee_draft';
    public const STATUS_READY_FOR_MANAGER = 'ready_for_manager';
    public const STATUS_MANAGER_DRAFT = 'manager_draft';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_SIGNED = 'signed';

    public const STATUSES = [
        self::STATUS_SCHEDULED => 'Planifié',
        self::STATUS_EMPLOYEE_DRAFT => 'En préparation (salarié)',
        self::STATUS_READY_FOR_MANAGER => 'À traiter par le manager',
        self::STATUS_MANAGER_DRAFT => 'En préparation (manager)',
        self::STATUS_COMPLETED => 'Prêt à signer',
        self::STATUS_SIGNED => 'Signé',
    ];

    protected $fillable = [
        'employee_id',
        'manager_id',
        'co_manager_id',
        'year',
        'scheduled_for',
        'status',
        'template_key',
        'header',
        'employee_answers',
        'manager_answers',
        'employee_signed_at',
        'manager_signed_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_for' => 'date',
            'header' => 'array',
            'employee_answers' => 'array',
            'manager_answers' => 'array',
            'employee_signed_at' => 'datetime',
            'manager_signed_at' => 'datetime',
            'year' => 'integer',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /** Co-évaluateur (personne qui assiste à l'entretien) — informatif. */
    public function coManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'co_manager_id');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function isSigned(): bool
    {
        return $this->status === self::STATUS_SIGNED;
    }

    public function isEditableByEmployee(): bool
    {
        return in_array($this->status, [
            self::STATUS_SCHEDULED,
            self::STATUS_EMPLOYEE_DRAFT,
        ], true);
    }

    public function isEditableByManager(): bool
    {
        return in_array($this->status, [
            self::STATUS_READY_FOR_MANAGER,
            self::STATUS_MANAGER_DRAFT,
            self::STATUS_COMPLETED,
        ], true);
    }

    public function template(): array
    {
        return ReviewTemplate::get($this->template_key ?? ReviewTemplate::DEFAULT);
    }
}

```

---

## `app/Models/ReviewTemplateModel.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewTemplateModel extends Model
{
    protected $table = 'review_templates';

    protected $fillable = ['key', 'label', 'header', 'sections'];

    protected function casts(): array
    {
        return [
            'header' => 'array',
            'sections' => 'array',
        ];
    }

    public function toDefinition(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'header' => $this->header ?? [],
            'sections' => $this->sections ?? [],
        ];
    }
}

```

---

# 4. Contrôleurs

## `app/Http/Controllers/DashboardController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\AnnualReview;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $currentYear = (int) Carbon::now()->year;

        $baseQuery = AnnualReview::query();
        if ($user->isAdmin()) {
            // all
        } elseif ($user->isManager()) {
            $baseQuery->where(function ($q) use ($user) {
                $q->where('manager_id', $user->id)
                  ->orWhere('co_manager_id', $user->id)
                  ->orWhere('employee_id', $user->id);
            });
        } else {
            $baseQuery->where('employee_id', $user->id);
        }

        $scoped = (clone $baseQuery);
        $stats = [
            'total' => (clone $scoped)->where('year', $currentYear)->count(),
            'to_prepare' => (clone $scoped)->whereIn('status', [
                AnnualReview::STATUS_SCHEDULED,
                AnnualReview::STATUS_EMPLOYEE_DRAFT,
            ])->count(),
            'to_review' => (clone $scoped)->whereIn('status', [
                AnnualReview::STATUS_READY_FOR_MANAGER,
                AnnualReview::STATUS_MANAGER_DRAFT,
            ])->count(),
            'to_sign' => (clone $scoped)->where('status', AnnualReview::STATUS_COMPLETED)->count(),
            'signed' => (clone $scoped)->where('status', AnnualReview::STATUS_SIGNED)
                ->where('year', $currentYear)->count(),
            'team' => $user->isAdmin() ? User::count() : null,
        ];

        $upcoming = (clone $baseQuery)
            ->with(['employee:id,name,position', 'manager:id,name'])
            ->whereIn('status', [
                AnnualReview::STATUS_SCHEDULED,
                AnnualReview::STATUS_EMPLOYEE_DRAFT,
                AnnualReview::STATUS_READY_FOR_MANAGER,
                AnnualReview::STATUS_MANAGER_DRAFT,
                AnnualReview::STATUS_COMPLETED,
            ])
            ->orderByRaw('scheduled_for IS NULL')
            ->orderBy('scheduled_for')
            ->limit(8)
            ->get()
            ->map(fn (AnnualReview $r) => [
                'id' => $r->id,
                'year' => $r->year,
                'status' => $r->status,
                'status_label' => $r->statusLabel(),
                'scheduled_for' => optional($r->scheduled_for)->toDateString(),
                'employee' => $r->employee ? [
                    'id' => $r->employee->id,
                    'name' => $r->employee->name,
                    'position' => $r->employee->position,
                ] : null,
                'manager' => $r->manager?->only(['id', 'name']),
            ]);

        // Mes actions à effectuer maintenant : entretiens où l'utilisateur
        // doit agir (préparer son auto-éval, traiter en tant que manager,
        // ou signer). Triées par urgence.
        $myActions = $this->buildMyActions($user);

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'upcoming' => $upcoming,
            'myActions' => $myActions,
            'currentYear' => $currentYear,
        ]);
    }

    /** @return list<array<string,mixed>> */
    private function buildMyActions(User $user): array
    {
        $actions = [];

        // 1) Mes propres entretiens (en tant que salarié)
        $ownReviews = AnnualReview::with('manager:id,name')
            ->where('employee_id', $user->id)
            ->orderByDesc('year')
            ->get();

        foreach ($ownReviews as $r) {
            $action = $this->buildOwnActionFromReview($r);
            if ($action) {
                $actions[] = $action;
            }
        }

        // 2) Entretiens que je conduis (en tant que manager)
        if ($user->isManager() || $user->isAdmin()) {
            $managedReviews = AnnualReview::with('employee:id,name,position')
                ->where('manager_id', $user->id)
                ->orderByDesc('year')
                ->get();

            foreach ($managedReviews as $r) {
                $action = $this->buildManagerActionFromReview($r);
                if ($action) {
                    $actions[] = $action;
                }
            }
        }

        // Tri : les actions urgentes (à préparer / à traiter / à signer) en premier
        usort($actions, fn ($a, $b) => ($b['priority'] ?? 0) <=> ($a['priority'] ?? 0));

        return $actions;
    }

    private function buildOwnActionFromReview(AnnualReview $r): ?array
    {
        $base = [
            'review_id' => $r->id,
            'year' => $r->year,
            'role' => 'employee',
            'manager_name' => $r->manager?->name,
        ];

        return match ($r->status) {
            AnnualReview::STATUS_SCHEDULED => array_merge($base, [
                'title' => "Préparer mon entretien {$r->year}",
                'subtitle' => 'Vous pouvez commencer votre auto-évaluation.',
                'cta' => 'Commencer la préparation',
                'tone' => 'primary',
                'priority' => 90,
            ]),
            AnnualReview::STATUS_EMPLOYEE_DRAFT => array_merge($base, [
                'title' => "Continuer mon entretien {$r->year}",
                'subtitle' => "Brouillon en cours — pensez à l'envoyer au manager quand c'est prêt.",
                'cta' => 'Reprendre la préparation',
                'tone' => 'primary',
                'priority' => 95,
            ]),
            AnnualReview::STATUS_READY_FOR_MANAGER, AnnualReview::STATUS_MANAGER_DRAFT => array_merge($base, [
                'title' => "Mon entretien {$r->year} est entre les mains du manager",
                'subtitle' => 'Vous pourrez signer dès qu\'il aura finalisé sa partie.',
                'cta' => 'Voir mon entretien',
                'tone' => 'info',
                'priority' => 30,
            ]),
            AnnualReview::STATUS_COMPLETED => $r->employee_signed_at
                ? null
                : array_merge($base, [
                    'title' => "Signer mon entretien {$r->year}",
                    'subtitle' => 'Le manager a finalisé — votre signature est attendue.',
                    'cta' => 'Signer maintenant',
                    'tone' => 'urgent',
                    'priority' => 100,
                ]),
            default => null,
        };
    }

    private function buildManagerActionFromReview(AnnualReview $r): ?array
    {
        $base = [
            'review_id' => $r->id,
            'year' => $r->year,
            'role' => 'manager',
            'employee_name' => $r->employee?->name,
            'employee_position' => $r->employee?->position,
        ];

        return match ($r->status) {
            AnnualReview::STATUS_READY_FOR_MANAGER => array_merge($base, [
                'title' => "Conduire l'entretien de {$r->employee?->name}",
                'subtitle' => "L'auto-évaluation est prête, à vous de compléter votre partie.",
                'cta' => 'Ouvrir et compléter',
                'tone' => 'primary',
                'priority' => 90,
            ]),
            AnnualReview::STATUS_MANAGER_DRAFT => array_merge($base, [
                'title' => "Continuer l'entretien de {$r->employee?->name}",
                'subtitle' => 'Brouillon en cours — pensez à finaliser pour signature.',
                'cta' => 'Reprendre',
                'tone' => 'primary',
                'priority' => 85,
            ]),
            AnnualReview::STATUS_COMPLETED => $r->manager_signed_at
                ? null
                : array_merge($base, [
                    'title' => "Signer l'entretien de {$r->employee?->name}",
                    'subtitle' => 'En attente de votre signature.',
                    'cta' => 'Signer maintenant',
                    'tone' => 'urgent',
                    'priority' => 100,
                ]),
            default => null,
        };
    }
}

```

---

## `app/Http/Controllers/AnnualReviewController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\AnnualReview;
use App\Models\User;
use App\Support\ReviewTemplate;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class AnnualReviewController extends Controller
{
    /** Liste des entretiens que l'utilisateur peut voir. */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $query = AnnualReview::with([
                'employee:id,name,email,position,department',
                'manager:id,name',
                'coManager:id,name',
            ])
            ->orderByDesc('year')
            ->orderBy('scheduled_for');

        if ($user->isAdmin()) {
            // tout
        } elseif ($user->isManager()) {
            $query->where(function ($q) use ($user) {
                $q->where('manager_id', $user->id)
                  ->orWhere('co_manager_id', $user->id)
                  ->orWhere('employee_id', $user->id);
            });
        } else {
            $query->where('employee_id', $user->id);
        }

        $reviews = $query->get()->map(fn (AnnualReview $r) => [
            'id' => $r->id,
            'year' => $r->year,
            'status' => $r->status,
            'status_label' => $r->statusLabel(),
            'scheduled_for' => optional($r->scheduled_for)->toDateString(),
            'employee' => $r->employee ? [
                'id' => $r->employee->id,
                'name' => $r->employee->name,
                'position' => $r->employee->position,
                'department' => $r->employee->department,
            ] : null,
            'manager' => $r->manager ? [
                'id' => $r->manager->id,
                'name' => $r->manager->name,
            ] : null,
            'co_manager' => $r->coManager ? [
                'id' => $r->coManager->id,
                'name' => $r->coManager->name,
            ] : null,
            'signed' => $r->isSigned(),
            'is_mine' => $r->employee_id === $user->id,
        ]);

        // Seule la directrice (admin) peut planifier des entretiens.
        $employees = [];
        $potentialManagers = [];
        if ($user->isAdmin()) {
            $employees = User::query()
                ->orderBy('position')
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'position', 'department']);

            $potentialManagers = User::query()
                ->whereIn('role', [User::ROLE_MANAGER, User::ROLE_ADMIN])
                ->orderBy('name')
                ->get(['id', 'name', 'position']);
        }

        return Inertia::render('Reviews/Index', [
            'reviews' => $reviews,
            'employees' => $employees,
            'potentialManagers' => $potentialManagers,
            'defaultYear' => (int) Carbon::now()->year,
            'can' => [
                'create' => $user->isAdmin(),
                'pickManager' => $user->isAdmin(),
                'delete' => $user->isAdmin(),
            ],
        ]);
    }

    /**
     * Planifie un ou plusieurs entretiens. Accepte une liste d'assignations :
     *   year, assignments: [{ employee_id, manager_id?, scheduled_for? }]
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', AnnualReview::class);

        $data = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'assignments' => ['required', 'array', 'min:1'],
            'assignments.*.employee_id' => ['required', 'exists:users,id'],
            'assignments.*.manager_id' => ['nullable', 'exists:users,id'],
            'assignments.*.co_manager_id' => ['nullable', 'exists:users,id'],
            'assignments.*.scheduled_for' => ['nullable', 'date'],
        ]);

        $currentUser = $request->user();
        $created = 0;
        $skipped = [];

        foreach ($data['assignments'] as $row) {
            $employee = User::find($row['employee_id']);
            if (! $employee) { continue; }

            // Détermine le manager de l'entretien :
            //  - admin : peut choisir librement (par défaut : lui-même)
            //  - manager non-admin : c'est forcément lui, sur son subordonné
            if ($currentUser->isAdmin()) {
                $managerId = $row['manager_id'] ?? $currentUser->id;
                $manager = User::find($managerId);
                if (! $manager || ! in_array($manager->role, [User::ROLE_MANAGER, User::ROLE_ADMIN], true)) {
                    $skipped[] = "{$employee->name} : manager invalide";
                    continue;
                }
                if ($manager->id === $employee->id) {
                    $skipped[] = "{$employee->name} : le salarié ne peut pas être son propre manager";
                    continue;
                }
            } else {
                if ($employee->manager_id !== $currentUser->id) {
                    $skipped[] = "{$employee->name} : vous n'êtes pas son manager";
                    continue;
                }
                $managerId = $currentUser->id;
            }

            // Unicité (employee_id + year) — on ignore silencieusement les doublons
            $exists = AnnualReview::where('employee_id', $employee->id)
                ->where('year', $data['year'])
                ->exists();
            if ($exists) {
                $skipped[] = "{$employee->name} : déjà un entretien pour {$data['year']}";
                continue;
            }

            // Co-évaluateur (optionnel, admin uniquement) — informatif + visibilité.
            $coManagerId = null;
            if ($currentUser->isAdmin() && ! empty($row['co_manager_id'])) {
                $coManager = User::find($row['co_manager_id']);
                if ($coManager
                    && in_array($coManager->role, [User::ROLE_MANAGER, User::ROLE_ADMIN], true)
                    && $coManager->id !== $employee->id
                    && $coManager->id !== $managerId) {
                    $coManagerId = $coManager->id;
                }
            }

            AnnualReview::create([
                'employee_id' => $employee->id,
                'manager_id' => $managerId,
                'co_manager_id' => $coManagerId,
                'year' => $data['year'],
                'scheduled_for' => $row['scheduled_for'] ?? null,
                'status' => AnnualReview::STATUS_SCHEDULED,
                'template_key' => ReviewTemplate::keyForPosition($employee->position),
            ]);
            $created++;
        }

        $msg = $created . ' entretien' . ($created > 1 ? 's' : '') . ' planifié' . ($created > 1 ? 's' : '');
        if (! empty($skipped)) {
            $msg .= ' — ignorés : ' . implode(' ; ', $skipped);
        }
        return redirect()->route('reviews.index')->with($created > 0 ? 'success' : 'error', $msg);
    }

    public function show(AnnualReview $review): Response
    {
        Gate::authorize('view', $review);

        $review->load([
            'employee:id,name,email,position,department,hired_on',
            'manager:id,name,email',
            'coManager:id,name,email',
        ]);

        return Inertia::render('Reviews/Show', [
            'review' => $this->serialize($review),
            'template' => $review->template(),
        ]);
    }

    /**
     * Vue HTML imprimable — le navigateur gère le "Enregistrer au format PDF".
     * (Pas d'Inertia : on renvoie un document Blade autonome.)
     */
    public function printable(AnnualReview $review)
    {
        Gate::authorize('view', $review);

        $review->load([
            'employee:id,name,email,position,department,hired_on',
            'manager:id,name,email',
            'coManager:id,name,email',
        ]);

        return response()->view('reviews.print', [
            'review' => $review,
            'template' => $review->template(),
        ]);
    }

    /**
     * Téléchargement direct en PDF via dompdf — le PDF est généré
     * côté serveur, sans passer par la boîte d'impression du navigateur.
     */
    public function downloadPdf(AnnualReview $review)
    {
        Gate::authorize('view', $review);

        $review->load([
            'employee:id,name,email,position,department,hired_on',
            'manager:id,name,email',
            'coManager:id,name,email',
        ]);

        $filename = sprintf(
            '%d_%s_Entretien.pdf',
            $review->year,
            \Illuminate\Support\Str::upper(\Illuminate\Support\Str::slug($review->employee->name, '_'))
        );

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reviews.pdf', [
            'review' => $review,
            'template' => $review->template(),
        ])->setPaper('a4')->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->download($filename);
    }

    /** Le salarié enregistre / envoie sa partie. */
    public function employeeUpdate(Request $request, AnnualReview $review): RedirectResponse
    {
        Gate::authorize('employeeEdit', $review);

        $data = $request->validate([
            'header' => ['nullable', 'array'],
            'answers' => ['nullable', 'array'],
            'do_submit' => ['nullable', 'boolean'],
        ]);

        $template = $review->template();

        // Filtre les clés header autorisées pour l'employé
        $employeeHeaderKeys = collect($template['header'] ?? [])
            ->where('owner', 'employee')
            ->pluck('key')
            ->all();

        $existingHeader = $review->header ?? [];
        foreach (($data['header'] ?? []) as $k => $v) {
            if (in_array($k, $employeeHeaderKeys, true)) {
                $existingHeader[$k] = $v;
            }
        }
        $review->header = $existingHeader;

        // Les réponses salarié sont stockées telles quelles (filtrage par confiance :
        // le formulaire n'expose que les champs owner=employee, et la vue manager
        // n'affichera que les clés attendues de toute façon)
        $review->employee_answers = $data['answers'] ?? [];

        $submit = (bool) ($data['do_submit'] ?? false);
        if ($submit) {
            $review->status = AnnualReview::STATUS_READY_FOR_MANAGER;
        } elseif ($review->status === AnnualReview::STATUS_SCHEDULED) {
            $review->status = AnnualReview::STATUS_EMPLOYEE_DRAFT;
        }
        $review->save();

        return back()->with('success', $submit
            ? 'Auto-évaluation envoyée'
            : 'Brouillon enregistré');
    }

    /** Le manager enregistre / finalise sa partie. */
    public function managerUpdate(Request $request, AnnualReview $review): RedirectResponse
    {
        Gate::authorize('managerEdit', $review);

        $data = $request->validate([
            'header' => ['nullable', 'array'],
            'answers' => ['nullable', 'array'],
            'finalize' => ['nullable', 'boolean'],
        ]);

        $template = $review->template();

        $managerHeaderKeys = collect($template['header'] ?? [])
            ->where('owner', 'manager')
            ->pluck('key')
            ->all();

        $existingHeader = $review->header ?? [];
        foreach (($data['header'] ?? []) as $k => $v) {
            if (in_array($k, $managerHeaderKeys, true)) {
                $existingHeader[$k] = $v;
            }
        }
        $review->header = $existingHeader;

        $review->manager_answers = $data['answers'] ?? [];

        $finalize = (bool) ($data['finalize'] ?? false);
        if ($finalize) {
            $review->status = AnnualReview::STATUS_COMPLETED;
        } elseif ($review->status === AnnualReview::STATUS_READY_FOR_MANAGER) {
            $review->status = AnnualReview::STATUS_MANAGER_DRAFT;
        }
        $review->save();

        return back()->with('success', $finalize
            ? 'Entretien prêt à être signé'
            : 'Brouillon manager enregistré');
    }

    public function sign(Request $request, AnnualReview $review): RedirectResponse
    {
        Gate::authorize('view', $review);

        $user = $request->user();
        $now = Carbon::now();
        $changed = false;

        if ($review->status === AnnualReview::STATUS_COMPLETED
            || $review->status === AnnualReview::STATUS_SIGNED) {
            if ($review->employee_id === $user->id && ! $review->employee_signed_at) {
                $review->employee_signed_at = $now;
                $changed = true;
            }
            if (($review->manager_id === $user->id || $user->isAdmin())
                && ! $review->manager_signed_at) {
                $review->manager_signed_at = $now;
                $changed = true;
            }
        } else {
            return back()->with('error', "L'entretien doit être finalisé par le manager avant signature.");
        }

        if ($changed && $review->employee_signed_at && $review->manager_signed_at) {
            $review->status = AnnualReview::STATUS_SIGNED;
        }

        $review->save();

        return back()->with('success', 'Signature enregistrée');
    }

    /** Modifie la date d'un entretien planifié (admin uniquement). */
    public function reschedule(Request $request, AnnualReview $review): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $data = $request->validate([
            'scheduled_for' => ['nullable', 'date'],
        ]);

        $review->update(['scheduled_for' => $data['scheduled_for'] ?? null]);

        return back()->with('success', 'Date de l\'entretien mise à jour');
    }

    public function destroy(AnnualReview $review): RedirectResponse
    {
        Gate::authorize('delete', $review);
        $review->delete();
        return redirect()->route('reviews.index')->with('success', 'Entretien supprimé');
    }

    private function serialize(AnnualReview $review): array
    {
        return [
            'id' => $review->id,
            'year' => $review->year,
            'scheduled_for' => optional($review->scheduled_for)->toDateString(),
            'status' => $review->status,
            'status_label' => $review->statusLabel(),
            'template_key' => $review->template_key,
            'header' => $review->header ?? (object) [],
            'employee_answers' => $review->employee_answers ?? (object) [],
            'manager_answers' => $review->manager_answers ?? (object) [],
            'employee_signed_at' => optional($review->employee_signed_at)->toIso8601String(),
            'manager_signed_at' => optional($review->manager_signed_at)->toIso8601String(),
            'employee' => $review->employee ? [
                'id' => $review->employee->id,
                'name' => $review->employee->name,
                'email' => $review->employee->email,
                'position' => $review->employee->position,
                'department' => $review->employee->department,
                'hired_on' => optional($review->employee->hired_on)->toDateString(),
            ] : null,
            'manager' => $review->manager ? [
                'id' => $review->manager->id,
                'name' => $review->manager->name,
                'email' => $review->manager->email,
            ] : null,
            'co_manager' => $review->coManager ? [
                'id' => $review->coManager->id,
                'name' => $review->coManager->name,
                'email' => $review->coManager->email,
            ] : null,
        ];
    }
}

```

---

## `app/Http/Controllers/TeamController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Positions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorizeAdmin($request);

        $users = User::query()
            ->orderBy('role')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'position', 'department', 'manager_id', 'hired_on'])
            ->map(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'position' => $u->position,
                'department' => $u->department,
                'manager_id' => $u->manager_id,
                'hired_on' => optional($u->hired_on)->toDateString(),
            ]);

        $managers = User::query()
            ->whereIn('role', [User::ROLE_MANAGER, User::ROLE_ADMIN])
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Team/Index', [
            'users' => $users,
            'managers' => $managers,
            'positions' => Positions::list(),
            'roles' => [
                ['value' => User::ROLE_EMPLOYEE, 'label' => 'Salarié'],
                ['value' => User::ROLE_MANAGER, 'label' => 'Manager'],
                ['value' => User::ROLE_ADMIN, 'label' => 'Administrateur'],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $this->validateUser($request);
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('team.index')->with('success', 'Membre ajouté');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $this->validateUser($request, $user);
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('team.index')->with('success', 'Membre mis à jour');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin($request);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Vous ne pouvez pas vous supprimer vous-même.');
        }

        $user->delete();

        return back()->with('success', 'Membre supprimé');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(optional($request->user())->isAdmin(), 403);
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        $emailRule = ['required', 'email', 'max:255'];
        $emailRule[] = Rule::unique('users', 'email')->ignore($user?->id);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => $emailRule,
            'role' => ['required', Rule::in([User::ROLE_EMPLOYEE, User::ROLE_MANAGER, User::ROLE_ADMIN])],
            'position' => ['required', Rule::in(Positions::list())],
            'department' => ['nullable', 'string', 'max:255'],
            'manager_id' => ['nullable', 'exists:users,id'],
            'hired_on' => ['nullable', 'date'],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Rules\Password::defaults()],
        ];

        return $request->validate($rules);
    }
}

```

---

## `app/Http/Controllers/TemplateController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\ReviewTemplateModel;
use App\Support\ReviewTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TemplateController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorizeAdmin($request);

        $templates = collect(ReviewTemplate::all())->map(fn (array $t) => [
            'key' => $t['key'],
            'label' => $t['label'],
            'section_count' => count($t['sections'] ?? []),
            'field_count' => collect($t['sections'] ?? [])
                ->flatMap(fn ($s) => $s['fields'] ?? [])
                ->count(),
        ]);

        return Inertia::render('Templates/Index', [
            'templates' => $templates,
        ]);
    }

    public function edit(Request $request, string $key): Response
    {
        $this->authorizeAdmin($request);

        $template = ReviewTemplate::get($key);

        return Inertia::render('Templates/Edit', [
            'template' => $template,
        ]);
    }

    public function update(Request $request, string $key): RedirectResponse
    {
        $this->authorizeAdmin($request);

        // Validation de la structure (les champs auxiliaires comme rows /
        // hint / evaluation_options / options ne sont pas listés mais ne
        // sont pas filtrés non plus : on stocke l'input brut juste après).
        $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'header' => ['nullable', 'array'],
            'header.*.key' => ['required', 'string'],
            'header.*.label' => ['required', 'string'],
            'header.*.owner' => ['required', 'string'],
            'sections' => ['required', 'array', 'min:1'],
            'sections.*.title' => ['required', 'string'],
            'sections.*.fields' => ['required', 'array'],
            'sections.*.fields.*.type' => ['required', 'string'],
            'sections.*.fields.*.key' => ['required', 'string'],
            'sections.*.fields.*.question' => ['nullable', 'string'],
            'sections.*.fields.*.owner' => ['nullable', 'string'],
        ]);

        $label = $request->input('label');
        $header = $request->input('header', []);
        $sections = $request->input('sections', []);

        ReviewTemplateModel::updateOrCreate(
            ['key' => $key],
            [
                'label' => $label,
                'header' => $header,
                'sections' => $sections,
            ]
        );

        return redirect()->route('templates.index')
            ->with('success', 'Trame « ' . $label . ' » mise à jour');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(optional($request->user())->isAdmin(), 403);
    }
}

```

---

## `app/Http/Controllers/Auth/ForcePasswordChangeController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class ForcePasswordChangeController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Auth/ForcePasswordChange');
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect()->route('dashboard')->with('success', 'Mot de passe mis à jour. Bienvenue !');
    }
}

```

---

## `app/Http/Controllers/Auth/RegisteredUserController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Positions;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register', [
            'positions' => Positions::list(),
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'position' => ['required', Rule::in(Positions::list())],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'position' => $request->position,
            'role' => User::ROLE_EMPLOYEE,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}

```

---

# 5. Policies & Middleware

## `app/Policies/AnnualReviewPolicy.php`

```php
<?php

namespace App\Policies;

use App\Models\AnnualReview;
use App\Models\User;

class AnnualReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AnnualReview $review): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $review->employee_id === $user->id
            || $review->manager_id === $user->id
            || $review->co_manager_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function employeeEdit(User $user, AnnualReview $review): bool
    {
        return $review->employee_id === $user->id
            && $review->isEditableByEmployee();
    }

    public function managerEdit(User $user, AnnualReview $review): bool
    {
        if ($user->isAdmin()) {
            return $review->isEditableByManager();
        }
        return $review->manager_id === $user->id
            && $review->isEditableByManager();
    }

    public function delete(User $user, AnnualReview $review): bool
    {
        return $user->isAdmin() && ! $review->isSigned();
    }
}

```

---

## `app/Http/Middleware/EnsurePasswordChanged.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Force l'utilisateur à changer son mot de passe temporaire
 * avant d'accéder à toute autre page de l'application.
 */
class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user
            && $user->must_change_password
            && ! $request->routeIs('password.force-change', 'password.force-change.update', 'logout')
        ) {
            return redirect()->route('password.force-change');
        }

        return $next($request);
    }
}

```

---

## `app/Http/Middleware/HandleInertiaRequests.php`

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'isManager' => fn () => (bool) optional($request->user())->isManager(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}

```

---

# 6. Support (Positions, Templates)

## `app/Support/Positions.php`

```php
<?php

namespace App\Support;

/**
 * Profils métiers du Cabinet Dentaire de l'Obiou.
 */
class Positions
{
    public const DENTIST = 'Dentiste';
    public const DENTAL_ASSISTANT = 'Assistant dentaire';
    public const ADMIN_ASSISTANT = 'Assistant administratif';
    public const OPERATIONS_DIRECTOR = "Directrice d'exploitation";
    public const CLINICAL_REFERENT = 'Référente clinique';
    public const ADMIN_REFERENT = 'Référente administrative';
    public const STERILIZATION_REFERENT = 'Référente stérilisation';

    public const ALL = [
        self::DENTIST,
        self::DENTAL_ASSISTANT,
        self::ADMIN_ASSISTANT,
        self::OPERATIONS_DIRECTOR,
        self::CLINICAL_REFERENT,
        self::ADMIN_REFERENT,
        self::STERILIZATION_REFERENT,
    ];

    public static function list(): array
    {
        return self::ALL;
    }
}

```

---

## `app/Support/ReviewTemplate.php`

```php
<?php

namespace App\Support;

use App\Models\ReviewTemplateModel;
use App\Support\ReviewTemplates\AdminAssistantTemplate;
use App\Support\ReviewTemplates\AssistantTemplate;
use App\Support\ReviewTemplates\DentisteTemplate;
use App\Support\ReviewTemplates\DirectriceTemplate;
use App\Support\ReviewTemplates\ReferenteAdministrativeTemplate;
use App\Support\ReviewTemplates\ReferenteCliniqueTemplate;
use App\Support\ReviewTemplates\ReferenteSterilisationTemplate;

/**
 * Registre des trames d'entretien annuel.
 *
 * Résolution : la base de données (table review_templates) est
 * consultée en priorité ; si la trame n'existe pas encore en base,
 * on retombe sur la définition PHP statique.
 */
class ReviewTemplate
{
    public const DEFAULT = AssistantTemplate::KEY;

    /**
     * Renvoie la définition d'une trame à partir de sa clé.
     * DB d'abord, fallback PHP ensuite.
     */
    public static function get(string $key): array
    {
        $db = ReviewTemplateModel::where('key', $key)->first();
        if ($db) {
            return $db->toDefinition();
        }

        return self::getFromPhp($key);
    }

    /**
     * Récupère toutes les trames connues (DB puis complétées par le PHP).
     */
    public static function all(): array
    {
        $dbTemplates = ReviewTemplateModel::all()->keyBy('key');
        $phpKeys = [
            AssistantTemplate::KEY,
            AdminAssistantTemplate::KEY,
            DirectriceTemplate::KEY,
            DentisteTemplate::KEY,
            ReferenteCliniqueTemplate::KEY,
            ReferenteSterilisationTemplate::KEY,
            ReferenteAdministrativeTemplate::KEY,
        ];

        $result = [];
        foreach ($phpKeys as $k) {
            if ($dbTemplates->has($k)) {
                $result[] = $dbTemplates->get($k)->toDefinition();
            } else {
                $result[] = self::getFromPhp($k);
            }
        }
        // Ajouter les trames DB-only (créées via l'UI, pas en PHP)
        foreach ($dbTemplates as $k => $model) {
            if (! in_array($k, $phpKeys, true)) {
                $result[] = $model->toDefinition();
            }
        }
        return $result;
    }

    private static function getFromPhp(string $key): array
    {
        return match ($key) {
            AssistantTemplate::KEY => AssistantTemplate::definition(),
            AdminAssistantTemplate::KEY => AdminAssistantTemplate::definition(),
            DirectriceTemplate::KEY => DirectriceTemplate::definition(),
            DentisteTemplate::KEY => DentisteTemplate::definition(),
            ReferenteCliniqueTemplate::KEY => ReferenteCliniqueTemplate::definition(),
            ReferenteSterilisationTemplate::KEY => ReferenteSterilisationTemplate::definition(),
            ReferenteAdministrativeTemplate::KEY => ReferenteAdministrativeTemplate::definition(),
            default => AssistantTemplate::definition(),
        };
    }

    /**
     * Détermine la clé de trame en fonction du poste.
     */
    public static function keyForPosition(?string $position): string
    {
        return match ($position) {
            Positions::DENTAL_ASSISTANT => AssistantTemplate::KEY,
            Positions::ADMIN_ASSISTANT => AdminAssistantTemplate::KEY,
            Positions::OPERATIONS_DIRECTOR => DirectriceTemplate::KEY,
            Positions::DENTIST => DentisteTemplate::KEY,
            Positions::CLINICAL_REFERENT => ReferenteCliniqueTemplate::KEY,
            Positions::STERILIZATION_REFERENT => ReferenteSterilisationTemplate::KEY,
            Positions::ADMIN_REFERENT => ReferenteAdministrativeTemplate::KEY,
            default => self::DEFAULT,
        };
    }

    /**
     * Récupère la liste de toutes les clés de champs « employee » d'une trame.
     */
    public static function employeeFieldKeys(array $definition): array
    {
        return self::fieldKeysForOwner($definition, 'employee');
    }

    public static function managerFieldKeys(array $definition): array
    {
        return self::fieldKeysForOwner($definition, 'manager');
    }

    private static function fieldKeysForOwner(array $definition, string $owner): array
    {
        $keys = [];
        // Header
        foreach ($definition['header'] ?? [] as $headerField) {
            if (($headerField['owner'] ?? null) === $owner) {
                $keys[] = $headerField['key'];
            }
        }
        // Sections
        foreach ($definition['sections'] ?? [] as $section) {
            foreach ($section['fields'] ?? [] as $field) {
                $fieldOwner = $field['owner'] ?? self::implicitOwner($field['type'] ?? '');
                if ($fieldOwner === $owner) {
                    $keys[] = $field['key'];
                }
            }
        }
        return $keys;
    }

    /**
     * Pour les widgets composites, le owner est implicite :
     *  - objectives_review : rempli par le manager
     *  - competency_grid   : les deux parties (stocké séparément)
     * On renvoie null pour signaler qu'il faut traiter au cas par cas.
     */
    private static function implicitOwner(string $type): ?string
    {
        return match ($type) {
            'objectives_review', 'objectives_plan' => 'manager',
            default => null,
        };
    }
}

```

---

## `app/Support/ReviewTemplates/AdminAssistantTemplate.php`

```php
<?php

namespace App\Support\ReviewTemplates;

/**
 * Trame d'entretien annuel — Assistant(e) administratif.
 *
 * Variantes par rapport à la trame « assistant dentaire » :
 *  - Entête : « Agendas gérés » à la place de « Praticien binôme ».
 *  - Section 01 : ergonomie du poste + outils de travail (au lieu de
 *    fauteuil + stérilisation), avec commentaires par bloc.
 *  - Section 03 : grille de compétences spécifique administrative (6 lignes).
 *  - Section 06 : ressenti vie pro / perso en champ libre.
 */
class AdminAssistantTemplate
{
    public const KEY = 'assistant_admin';

    public static function definition(): array
    {
        $evaluationOptions = ['Dépassé', 'Atteint', 'Partiellement atteint', 'Non réalisé'];

        return [
            'key' => self::KEY,
            'label' => "Assistant(e) administratif",
            'header' => [
                ['key' => 'agendas_geres', 'label' => 'Agendas gérés', 'owner' => 'manager'],
                ['key' => 'responsabilites', 'label' => 'Responsabilité au cabinet (missions complémentaires)', 'owner' => 'employee'],
                ['key' => 'qui_realise_entretien', 'label' => "Qui réalise l'entretien", 'owner' => 'manager'],
            ],
            'sections' => [
                [
                    'title' => '01. Bien-être & Qualité de Vie, Conditions de Travail',
                    'fields' => [
                        ['type' => 'scale_10', 'key' => 'bien_etre_travail', 'question' => 'Comment vous sentez-vous dans votre travail ?', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'bien_etre_poste', 'question' => 'Le poste que vous occupez vous plaît-il ?', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'bien_etre_relations', 'question' => "Les relations avec les autres membres de l'équipe sont-elles bonnes ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'bien_etre_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'ergonomie_poste', 'question' => "Comment noteriez-vous l'ergonomie de votre poste de travail ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'ergonomie_poste_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'outils_travail', 'question' => 'Comment noteriez-vous vos outils de travail (logiciels, matériels…) ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'outils_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => "02. Bilan de l'année écoulée",
                    'fields' => [
                        [
                            'type' => 'objectives_review',
                            'key' => 'bilan_objectifs',
                            'question' => "Évaluation des objectifs de l'année écoulée",
                            'hint' => "Échelle d'évaluation : dépassé / atteint / partiellement atteint / non réalisé",
                            'evaluation_options' => $evaluationOptions,
                        ],
                        ['type' => 'textarea', 'key' => 'bilan_commentaires', 'question' => 'Commentaires', 'owner' => 'manager'],
                        [
                            'type' => 'activities_table',
                            'key' => 'activites',
                            'question' => 'Quelles sont les activités réalisées et faits marquants ?',
                            'owner' => 'employee',
                        ],
                    ],
                ],
                [
                    'title' => '03. Bilan des compétences attendues pour le poste occupé',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'reussites_poste', 'question' => 'Que réussissez-vous le mieux dans votre poste actuel ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'plaisir_fonctions', 'question' => "Qu'aimez-vous le plus dans vos fonctions actuelles ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'axes_amelioration', 'question' => "Quels sont vos points d'amélioration ou compétences à acquérir ?", 'owner' => 'employee'],
                        [
                            'type' => 'competency_grid',
                            'key' => 'competences',
                            'question' => 'Bilan des compétences techniques, comportementales et relationnelles attendues',
                            'hint' => "Échelle d'évaluation : dépassé / atteint / partiellement atteint / non réalisé",
                            'evaluation_options' => $evaluationOptions,
                            'rows' => [
                                "Je maîtrise les compétences d'assistanat administratif",
                                'Je maîtrise les outils informatiques',
                                "J'accueille les patients chaleureusement afin de contribuer à leur bien-être",
                                'Je suis proactif(ve)',
                                'Je me positionne en solution',
                                "Je suis à l'aise avec la communication (patients, collaborateurs, praticiens)",
                            ],
                        ],
                        ['type' => 'textarea', 'key' => 'competences_non_utilisees', 'question' => "Avez-vous des compétences non utilisées susceptibles d'être mises à profit dans votre vie professionnelle ?", 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => "04. Perspectives pour l'année à venir",
                    'fields' => [
                        [
                            'type' => 'objectives_plan',
                            'key' => 'nouveaux_objectifs',
                            'question' => "Objectifs pour l'année à venir",
                            'owner' => 'manager',
                        ],
                    ],
                ],
                [
                    'title' => '05. Formations',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'formations_salarie', 'question' => 'Formations souhaitées par le salarié', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'formations_salarie_objectifs', 'question' => 'Objectifs visés (salarié)', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'formations_manager', 'question' => 'Formations souhaitées par le manager', 'owner' => 'manager'],
                        ['type' => 'textarea', 'key' => 'formations_manager_objectifs', 'question' => 'Objectifs visés (manager)', 'owner' => 'manager'],
                    ],
                ],
                [
                    'title' => "06. Conditions d'activité",
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'conditions_remarques', 'question' => "Quelles sont vos remarques, points d'amélioration ou suggestions d'amélioration liés à vos conditions d'activité ?", 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'charge_travail', 'question' => 'Comment évaluez-vous votre charge de travail ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'charge_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'vie_privee_pro', 'question' => "Quel est votre ressenti quant à l'articulation de votre vie privée / vie professionnelle ?", 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => "07. Synthèse de l'entretien annuel",
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'synthese_collaborateur', 'question' => 'Commentaire du collaborateur', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'synthese_manager', 'question' => 'Commentaire du manager', 'owner' => 'manager'],
                    ],
                ],
            ],
        ];
    }
}

```

---

## `app/Support/ReviewTemplates/AssistantTemplate.php`

```php
<?php

namespace App\Support\ReviewTemplates;

/**
 * Trame d'entretien annuel — Assistant(e) dentaire / administratif.
 *
 * Chaque champ précise son « owner » :
 *  - employee : rempli par le salarié pendant sa préparation
 *  - manager  : rempli par le manager pendant sa préparation
 */
class AssistantTemplate
{
    public const KEY = 'assistant';

    public static function definition(): array
    {
        $evaluationOptions = ['Dépassé', 'Atteint', 'Partiellement atteint', 'Non réalisé'];

        return [
            'key' => self::KEY,
            'label' => "Assistant(e) dentaire",
            'header' => [
                ['key' => 'praticien_binome', 'label' => 'Praticien binôme', 'owner' => 'manager'],
                ['key' => 'responsabilites', 'label' => 'Responsabilité au cabinet (missions complémentaires)', 'owner' => 'employee'],
                ['key' => 'praticien_entretien', 'label' => "Praticien qui réalise l'entretien", 'owner' => 'manager'],
            ],
            'sections' => [
                [
                    'title' => '01. Bien-être & Qualité de Vie, Conditions de Travail',
                    'fields' => [
                        ['type' => 'scale_10', 'key' => 'bien_etre_travail', 'question' => 'Comment vous sentez-vous dans votre travail ?', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'bien_etre_poste', 'question' => 'Le poste que vous occupez vous plaît-il ?', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'bien_etre_relations', 'question' => "Les relations avec les autres membres de l'équipe sont-elles bonnes ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'bien_etre_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'ergonomie_fauteuil', 'question' => "Comment noteriez-vous l'ergonomie au fauteuil ?", 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'ergonomie_sterilisation', 'question' => "Comment noteriez-vous l'ergonomie en stérilisation ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'ergonomie_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => "02. Bilan de l'année écoulée",
                    'fields' => [
                        [
                            'type' => 'objectives_review',
                            'key' => 'bilan_objectifs',
                            'question' => "Évaluation des objectifs de l'année écoulée",
                            'hint' => "Échelle d'évaluation : dépassé / atteint / partiellement atteint / non réalisé",
                            'evaluation_options' => $evaluationOptions,
                            // Colonnes pour chaque ligne :
                            //  - objectif  (owner: manager)   — reprend l'objectif fixé l'an dernier
                            //  - evaluation (owner: manager)  — select
                        ],
                        ['type' => 'textarea', 'key' => 'bilan_commentaires', 'question' => 'Commentaires', 'owner' => 'manager'],
                        [
                            'type' => 'activities_table',
                            'key' => 'activites',
                            'question' => 'Quelles sont les activités réalisées et faits marquants ?',
                            'owner' => 'employee',
                            // Colonnes : Réalisations, Réussites, Difficultés
                        ],
                    ],
                ],
                [
                    'title' => '03. Bilan des compétences attendues pour le poste occupé',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'reussites_poste', 'question' => 'Que réussissez-vous le mieux dans votre poste actuel ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'plaisir_fonctions', 'question' => "Qu'aimez-vous le plus dans vos fonctions actuelles ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'axes_amelioration', 'question' => "Quels sont vos points d'amélioration ou compétences à acquérir ?", 'owner' => 'employee'],
                        [
                            'type' => 'competency_grid',
                            'key' => 'competences',
                            'question' => 'Bilan des compétences techniques, comportementales et relationnelles attendues',
                            'hint' => "Échelle d'évaluation : dépassé / atteint / partiellement atteint / non réalisé",
                            'evaluation_options' => $evaluationOptions,
                            'rows' => [
                                "Je maîtrise les compétences d'assistanat sur la partie clinique",
                                'Je maîtrise les fondamentaux de stérilisation',
                                'Je maîtrise les processus métiers administratifs',
                                "J'accompagne le patient afin de contribuer à son bien-être",
                                'Je suis proactif(ve) dans mon binôme / trinôme',
                                'Je suis autonome au quotidien',
                                "Je suis à l'aise avec la communication (patients, collaborateurs, praticiens)",
                            ],
                        ],
                        ['type' => 'textarea', 'key' => 'competences_non_utilisees', 'question' => 'Avez-vous des compétences non utilisées susceptibles d\'être mises à profit dans votre vie professionnelle ?', 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => "04. Perspectives pour l'année à venir",
                    'fields' => [
                        [
                            'type' => 'objectives_plan',
                            'key' => 'nouveaux_objectifs',
                            'question' => "Objectifs pour l'année à venir",
                            'owner' => 'manager',
                            // Colonnes : Objectif, Indicateurs de réalisation, Moyens à mettre en œuvre, Délais
                        ],
                    ],
                ],
                [
                    'title' => '05. Formations',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'formations_salarie', 'question' => 'Formations souhaitées par le salarié', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'formations_salarie_objectifs', 'question' => 'Objectifs visés (salarié)', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'formations_manager', 'question' => 'Formations souhaitées par le manager', 'owner' => 'manager'],
                        ['type' => 'textarea', 'key' => 'formations_manager_objectifs', 'question' => 'Objectifs visés (manager)', 'owner' => 'manager'],
                    ],
                ],
                [
                    'title' => "06. Conditions d'activité",
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'conditions_remarques', 'question' => "Quelles sont vos remarques, points d'amélioration ou suggestions d'amélioration liés à vos conditions d'activité ?", 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'charge_travail', 'question' => 'Comment évaluez-vous votre charge de travail ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'charge_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                        [
                            'type' => 'choice',
                            'key' => 'vie_privee_pro',
                            'question' => "Quel est votre ressenti quant à l'articulation de votre vie privée / vie professionnelle ?",
                            'options' => ['Très équilibré', 'Équilibré', 'Plutôt équilibré', 'Plutôt déséquilibré', 'Déséquilibré'],
                            'owner' => 'employee',
                        ],
                    ],
                ],
                [
                    'title' => "07. Synthèse de l'entretien annuel",
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'synthese_collaborateur', 'question' => 'Commentaire du collaborateur', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'synthese_manager', 'question' => 'Commentaire du manager', 'owner' => 'manager'],
                    ],
                ],
            ],
        ];
    }
}

```

---

## `app/Support/ReviewTemplates/DentisteTemplate.php`

```php
<?php

namespace App\Support\ReviewTemplates;

/**
 * Trame d'entretien annuel — Dentiste (praticien).
 *
 * Structure simple basée sur des questions ouvertes (textarea) — adaptée
 * aux entretiens de pairs (un dentiste évalue un autre dentiste).
 *
 * NB : la section 1 n'a pas encore été communiquée par le cabinet ;
 *      elle sera ajoutée ici quand son contenu sera connu.
 */
class DentisteTemplate
{
    public const KEY = 'dentiste';

    public static function definition(): array
    {
        return [
            'key' => self::KEY,
            'label' => 'Dentiste (praticien)',
            'header' => [
                // Entête minimal — l'entretien est conduit par un pair désigné par la directrice.
                ['key' => 'praticien_evaluateur', 'label' => "Praticien qui réalise l'entretien", 'owner' => 'manager'],
            ],
            'sections' => [
                [
                    'title' => '01. Activité professionnelle & organisation',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'bilan_annee', 'question' => "Bilan de l'année écoulée : quels sont vos réussites et points forts cette année dans votre pratique ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'difficultes', 'question' => 'Difficultés rencontrées : quels obstacles ou sources de frustration avez-vous rencontrés dans votre activité ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'gestion_temps', 'question' => 'Gestion du temps et des rendez-vous : êtes-vous satisfait(e) de votre rythme de travail et de la planification des consultations ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'collaboration_equipe', 'question' => 'Collaboration en équipe : comment percevez-vous la communication et la coopération avec les assistant(e)s, secrétaires, assistante de direction ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'collaboration_dentistes', 'question' => 'Collaboration en équipe : comment percevez-vous la communication et la coopération avec vos collègues dentistes ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'section2_commentaire', 'question' => 'Commentaire libre', 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => '02. Développement professionnel',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'competences_renforcer', 'question' => "Compétences à renforcer : y a-t-il des techniques ou connaissances que vous aimeriez approfondir ou développer ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'formations', 'question' => "Formations et spécialisations : souhaitez-vous suivre des formations spécifiques dans l'année à venir ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'innovation_materiel', 'question' => "Innovation et matériel : avez-vous des besoins ou envies concernant de nouveaux équipements ou outils qui pourraient améliorer votre pratique ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'section3_commentaire', 'question' => 'Commentaire libre', 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => '03. Relation patient',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'satisfaction_patients', 'question' => 'Satisfaction des patients : selon vous, comment les patients perçoivent-ils la qualité de vos soins et de votre accompagnement ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'experience_patient', 'question' => "Expérience patient : quelles améliorations pourraient être mises en place pour optimiser l'accueil, le suivi ou la relation avec les patients ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'section4_commentaire', 'question' => 'Commentaire libre', 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => '04. Bien-être & équilibre personnel',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'equilibre_pro_perso', 'question' => 'Équilibre vie pro / vie perso : arrivez-vous à préserver un équilibre satisfaisant entre travail et vie privée ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'sante_bien_etre', 'question' => "Santé et bien-être : qu'est-ce qui vous aide à gérer le stress lié à votre métier ? Auriez-vous besoin de soutien supplémentaire ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'motivation_perspectives', 'question' => "Motivation & perspectives : qu'est-ce qui vous motive le plus dans votre métier aujourd'hui ? Et quelles sont vos attentes pour l'avenir dans le cabinet ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'section5_commentaire', 'question' => 'Commentaire libre', 'owner' => 'employee'],
                    ],
                ],
                [
                    // Section additionnelle : permet au dentiste évaluateur (pair) de
                    // poser ses observations et recommandations écrites.
                    'title' => "Synthèse — commentaire du dentiste évaluateur",
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'synthese_manager', 'question' => "Observations, préconisations et points d'attention pour l'année à venir", 'owner' => 'manager'],
                    ],
                ],
            ],
        ];
    }
}

```

---

## `app/Support/ReviewTemplates/DirectriceTemplate.php`

```php
<?php

namespace App\Support\ReviewTemplates;

/**
 * Trame d'entretien annuel — Directrice d'exploitation.
 *
 * Particularités :
 *  - Entête minimal (pas de « Praticien binôme » ni « Agendas gérés »
 *    ni « Qui réalise l'entretien »).
 *  - Section 01 : ergonomie du poste + outils de travail.
 *  - Section 03 : grille de compétences en 5 lignes centrée sur le
 *    métier d'encadrement (pas d'accueil patient, pas d'assistanat).
 *  - Section 06 : vie privée / pro en champ libre.
 */
class DirectriceTemplate
{
    public const KEY = 'directrice';

    public static function definition(): array
    {
        $evaluationOptions = ['Dépassé', 'Atteint', 'Partiellement atteint', 'Non réalisé'];

        return [
            'key' => self::KEY,
            'label' => "Directrice d'exploitation",
            // Pas de champs d'entête dédiés : les infos (Nom, Poste, Date d'embauche,
            // Date d'entretien) viennent directement du profil utilisateur / du review.
            'header' => [],
            'sections' => [
                [
                    'title' => '01. Bien-être & Qualité de Vie, Conditions de Travail',
                    'fields' => [
                        ['type' => 'scale_10', 'key' => 'bien_etre_travail', 'question' => 'Comment vous sentez-vous dans votre travail ?', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'bien_etre_poste', 'question' => 'Le poste que vous occupez vous plaît-il ?', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'bien_etre_relations', 'question' => "Les relations avec les autres membres de l'équipe sont-elles bonnes ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'bien_etre_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'ergonomie_poste', 'question' => "Comment noteriez-vous l'ergonomie de votre poste de travail ?", 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'outils_travail', 'question' => 'Comment noteriez-vous vos outils de travail (logiciels, matériels…) ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'ergonomie_outils_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => "02. Bilan de l'année écoulée",
                    'fields' => [
                        [
                            'type' => 'objectives_review',
                            'key' => 'bilan_objectifs',
                            'question' => "Évaluation des objectifs de l'année écoulée",
                            'hint' => "Échelle d'évaluation : dépassé / atteint / partiellement atteint / non réalisé",
                            'evaluation_options' => $evaluationOptions,
                        ],
                        ['type' => 'textarea', 'key' => 'bilan_commentaires', 'question' => 'Commentaires', 'owner' => 'manager'],
                        [
                            'type' => 'activities_table',
                            'key' => 'activites',
                            'question' => 'Quelles sont les activités réalisées et faits marquants ?',
                            'owner' => 'employee',
                        ],
                    ],
                ],
                [
                    'title' => '03. Bilan des compétences attendues pour le poste occupé',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'reussites_poste', 'question' => 'Que réussissez-vous le mieux dans votre poste actuel ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'plaisir_fonctions', 'question' => "Qu'aimez-vous le plus dans vos fonctions actuelles ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'axes_amelioration', 'question' => "Quels sont vos points d'amélioration ou compétences à acquérir ?", 'owner' => 'employee'],
                        [
                            'type' => 'competency_grid',
                            'key' => 'competences',
                            'question' => 'Bilan des compétences techniques, comportementales et relationnelles attendues',
                            'hint' => "Échelle d'évaluation : dépassé / atteint / partiellement atteint / non réalisé",
                            'evaluation_options' => $evaluationOptions,
                            'rows' => [
                                'Je maîtrise les compétences de mon métier',
                                'Je maîtrise les outils informatiques',
                                'Je suis proactif(ve)',
                                'Je me positionne en solution',
                                "Je suis à l'aise avec la communication (patients, collaborateurs, praticiens)",
                            ],
                        ],
                        ['type' => 'textarea', 'key' => 'competences_non_utilisees', 'question' => "Avez-vous des compétences non utilisées susceptibles d'être mises à profit dans votre vie professionnelle ?", 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => "04. Perspectives pour l'année à venir",
                    'fields' => [
                        [
                            'type' => 'objectives_plan',
                            'key' => 'nouveaux_objectifs',
                            'question' => "Objectifs pour l'année à venir",
                            'owner' => 'manager',
                        ],
                    ],
                ],
                [
                    'title' => '05. Formations',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'formations_salarie', 'question' => 'Formations souhaitées par le salarié', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'formations_salarie_objectifs', 'question' => 'Objectifs visés (salarié)', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'formations_manager', 'question' => 'Formations souhaitées par le manager', 'owner' => 'manager'],
                        ['type' => 'textarea', 'key' => 'formations_manager_objectifs', 'question' => 'Objectifs visés (manager)', 'owner' => 'manager'],
                    ],
                ],
                [
                    'title' => "06. Conditions d'activité",
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'conditions_remarques', 'question' => "Quelles sont vos remarques, points d'amélioration ou suggestions d'amélioration liés à vos conditions d'activité ?", 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'charge_travail', 'question' => 'Comment évaluez-vous votre charge de travail ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'charge_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'vie_privee_pro', 'question' => "Quel est votre ressenti quant à l'articulation de votre vie privée / vie professionnelle ?", 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => "07. Synthèse de l'entretien annuel",
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'synthese_collaborateur', 'question' => 'Commentaire du collaborateur', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'synthese_manager', 'question' => 'Commentaire du manager', 'owner' => 'manager'],
                    ],
                ],
            ],
        ];
    }
}

```

---

## `app/Support/ReviewTemplates/ReferenteAdministrativeTemplate.php`

```php
<?php

namespace App\Support\ReviewTemplates;

/**
 * Trame d'entretien annuel — Référente administrative.
 *
 * Proche de la trame assistant administratif mais la grille de
 * compétences (section 3) reflète le rôle d'encadrement
 * administratif (dossiers complexes, coordination, leadership).
 */
class ReferenteAdministrativeTemplate
{
    public const KEY = 'ref_admin';

    public static function definition(): array
    {
        $evaluationOptions = ['Dépassé', 'Atteint', 'Partiellement atteint', 'Non réalisé'];

        return [
            'key' => self::KEY,
            'label' => 'Référente administrative',
            'header' => [
                ['key' => 'agendas_geres', 'label' => 'Agendas gérés / pôles suivis', 'owner' => 'manager'],
                ['key' => 'responsabilites', 'label' => 'Responsabilité au cabinet (missions complémentaires)', 'owner' => 'employee'],
                ['key' => 'qui_realise_entretien', 'label' => "Qui réalise l'entretien", 'owner' => 'manager'],
            ],
            'sections' => [
                [
                    'title' => '01. Bien-être & Qualité de Vie, Conditions de Travail',
                    'fields' => [
                        ['type' => 'scale_10', 'key' => 'bien_etre_travail', 'question' => 'Comment vous sentez-vous dans votre travail ?', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'bien_etre_poste', 'question' => 'Le poste que vous occupez vous plaît-il ?', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'bien_etre_relations', 'question' => "Les relations avec les autres membres de l'équipe sont-elles bonnes ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'bien_etre_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'ergonomie_poste', 'question' => "Comment noteriez-vous l'ergonomie de votre poste de travail ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'ergonomie_poste_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'outils_travail', 'question' => 'Comment noteriez-vous vos outils de travail (logiciels, matériels…) ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'outils_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => "02. Bilan de l'année écoulée",
                    'fields' => [
                        [
                            'type' => 'objectives_review',
                            'key' => 'bilan_objectifs',
                            'question' => "Évaluation des objectifs de l'année écoulée",
                            'hint' => "Échelle d'évaluation : dépassé / atteint / partiellement atteint / non réalisé",
                            'evaluation_options' => $evaluationOptions,
                        ],
                        ['type' => 'textarea', 'key' => 'bilan_commentaires', 'question' => 'Commentaires', 'owner' => 'manager'],
                        [
                            'type' => 'activities_table',
                            'key' => 'activites',
                            'question' => 'Quelles sont les activités réalisées et faits marquants ?',
                            'owner' => 'employee',
                        ],
                    ],
                ],
                [
                    'title' => '03. Bilan des compétences attendues pour le poste occupé',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'reussites_poste', 'question' => 'Que réussissez-vous le mieux dans votre poste actuel ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'plaisir_fonctions', 'question' => "Qu'aimez-vous le plus dans vos fonctions actuelles ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'axes_amelioration', 'question' => "Quels sont vos points d'amélioration ou compétences à acquérir ?", 'owner' => 'employee'],
                        [
                            'type' => 'competency_grid',
                            'key' => 'competences',
                            'question' => 'Bilan des compétences techniques, managériales et relationnelles attendues',
                            'hint' => "Échelle d'évaluation : dépassé / atteint / partiellement atteint / non réalisé",
                            'evaluation_options' => $evaluationOptions,
                            'rows' => [
                                'Je maîtrise les processus administratifs du cabinet',
                                "J'encadre et accompagne l'équipe administrative",
                                'Je gère les dossiers complexes (mutuelles, tiers payant, litiges)',
                                "Je maîtrise les outils informatiques (logiciel métier, traitement de l'information)",
                                "Je coordonne les agendas et l'accueil",
                                'Je me positionne en solution face aux problématiques rencontrées',
                                "Je suis à l'aise avec la communication (patients, collaborateurs, praticiens)",
                            ],
                        ],
                        ['type' => 'textarea', 'key' => 'competences_non_utilisees', 'question' => "Avez-vous des compétences non utilisées susceptibles d'être mises à profit dans votre vie professionnelle ?", 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => "04. Perspectives pour l'année à venir",
                    'fields' => [
                        [
                            'type' => 'objectives_plan',
                            'key' => 'nouveaux_objectifs',
                            'question' => "Objectifs pour l'année à venir",
                            'owner' => 'manager',
                        ],
                    ],
                ],
                [
                    'title' => '05. Formations',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'formations_salarie', 'question' => 'Formations souhaitées par le salarié', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'formations_salarie_objectifs', 'question' => 'Objectifs visés (salarié)', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'formations_manager', 'question' => 'Formations souhaitées par le manager', 'owner' => 'manager'],
                        ['type' => 'textarea', 'key' => 'formations_manager_objectifs', 'question' => 'Objectifs visés (manager)', 'owner' => 'manager'],
                    ],
                ],
                [
                    'title' => "06. Conditions d'activité",
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'conditions_remarques', 'question' => "Quelles sont vos remarques, points d'amélioration ou suggestions d'amélioration liés à vos conditions d'activité ?", 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'charge_travail', 'question' => 'Comment évaluez-vous votre charge de travail ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'charge_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'vie_privee_pro', 'question' => "Quel est votre ressenti quant à l'articulation de votre vie privée / vie professionnelle ?", 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => "07. Synthèse de l'entretien annuel",
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'synthese_collaborateur', 'question' => 'Commentaire du collaborateur', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'synthese_manager', 'question' => 'Commentaire du manager', 'owner' => 'manager'],
                    ],
                ],
            ],
        ];
    }
}

```

---

## `app/Support/ReviewTemplates/ReferenteCliniqueTemplate.php`

```php
<?php

namespace App\Support\ReviewTemplates;

/**
 * Trame d'entretien annuel — Référente clinique.
 *
 * Proche de la trame assistant dentaire, mais la grille de
 * compétences (section 3) reflète le rôle d'encadrement clinique
 * (formation des assistantes, coordination de l'activité,
 * amélioration des pratiques).
 */
class ReferenteCliniqueTemplate
{
    public const KEY = 'ref_clinique';

    public static function definition(): array
    {
        $evaluationOptions = ['Dépassé', 'Atteint', 'Partiellement atteint', 'Non réalisé'];

        return [
            'key' => self::KEY,
            'label' => 'Référente clinique',
            'header' => [
                ['key' => 'praticien_referent', 'label' => 'Praticien(s) référent(s)', 'owner' => 'manager'],
                ['key' => 'responsabilites', 'label' => 'Responsabilité au cabinet (missions complémentaires)', 'owner' => 'employee'],
                ['key' => 'praticien_entretien', 'label' => "Praticien qui réalise l'entretien", 'owner' => 'manager'],
            ],
            'sections' => [
                [
                    'title' => '01. Bien-être & Qualité de Vie, Conditions de Travail',
                    'fields' => [
                        ['type' => 'scale_10', 'key' => 'bien_etre_travail', 'question' => 'Comment vous sentez-vous dans votre travail ?', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'bien_etre_poste', 'question' => 'Le poste que vous occupez vous plaît-il ?', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'bien_etre_relations', 'question' => "Les relations avec les autres membres de l'équipe sont-elles bonnes ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'bien_etre_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'ergonomie_fauteuil', 'question' => "Comment noteriez-vous l'ergonomie au fauteuil ?", 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'ergonomie_sterilisation', 'question' => "Comment noteriez-vous l'ergonomie en stérilisation ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'ergonomie_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => "02. Bilan de l'année écoulée",
                    'fields' => [
                        [
                            'type' => 'objectives_review',
                            'key' => 'bilan_objectifs',
                            'question' => "Évaluation des objectifs de l'année écoulée",
                            'hint' => "Échelle d'évaluation : dépassé / atteint / partiellement atteint / non réalisé",
                            'evaluation_options' => $evaluationOptions,
                        ],
                        ['type' => 'textarea', 'key' => 'bilan_commentaires', 'question' => 'Commentaires', 'owner' => 'manager'],
                        [
                            'type' => 'activities_table',
                            'key' => 'activites',
                            'question' => 'Quelles sont les activités réalisées et faits marquants ?',
                            'owner' => 'employee',
                        ],
                    ],
                ],
                [
                    'title' => '03. Bilan des compétences attendues pour le poste occupé',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'reussites_poste', 'question' => 'Que réussissez-vous le mieux dans votre poste actuel ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'plaisir_fonctions', 'question' => "Qu'aimez-vous le plus dans vos fonctions actuelles ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'axes_amelioration', 'question' => "Quels sont vos points d'amélioration ou compétences à acquérir ?", 'owner' => 'employee'],
                        [
                            'type' => 'competency_grid',
                            'key' => 'competences',
                            'question' => 'Bilan des compétences techniques, managériales et relationnelles attendues',
                            'hint' => "Échelle d'évaluation : dépassé / atteint / partiellement atteint / non réalisé",
                            'evaluation_options' => $evaluationOptions,
                            'rows' => [
                                "Je maîtrise les compétences d'assistanat clinique",
                                'Je maîtrise les fondamentaux de stérilisation',
                                "J'encadre et accompagne les assistantes cliniques",
                                "J'accompagne le patient afin de contribuer à son bien-être",
                                "Je coordonne l'activité clinique au quotidien (matériel, planning)",
                                "Je suis proactive dans l'amélioration des pratiques",
                                "Je suis à l'aise avec la communication (patients, collaborateurs, praticiens)",
                            ],
                        ],
                        ['type' => 'textarea', 'key' => 'competences_non_utilisees', 'question' => "Avez-vous des compétences non utilisées susceptibles d'être mises à profit dans votre vie professionnelle ?", 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => "04. Perspectives pour l'année à venir",
                    'fields' => [
                        [
                            'type' => 'objectives_plan',
                            'key' => 'nouveaux_objectifs',
                            'question' => "Objectifs pour l'année à venir",
                            'owner' => 'manager',
                        ],
                    ],
                ],
                [
                    'title' => '05. Formations',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'formations_salarie', 'question' => 'Formations souhaitées par le salarié', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'formations_salarie_objectifs', 'question' => 'Objectifs visés (salarié)', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'formations_manager', 'question' => 'Formations souhaitées par le manager', 'owner' => 'manager'],
                        ['type' => 'textarea', 'key' => 'formations_manager_objectifs', 'question' => 'Objectifs visés (manager)', 'owner' => 'manager'],
                    ],
                ],
                [
                    'title' => "06. Conditions d'activité",
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'conditions_remarques', 'question' => "Quelles sont vos remarques, points d'amélioration ou suggestions d'amélioration liés à vos conditions d'activité ?", 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'charge_travail', 'question' => 'Comment évaluez-vous votre charge de travail ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'charge_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'vie_privee_pro', 'question' => "Quel est votre ressenti quant à l'articulation de votre vie privée / vie professionnelle ?", 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => "07. Synthèse de l'entretien annuel",
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'synthese_collaborateur', 'question' => 'Commentaire du collaborateur', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'synthese_manager', 'question' => 'Commentaire du manager', 'owner' => 'manager'],
                    ],
                ],
            ],
        ];
    }
}

```

---

## `app/Support/ReviewTemplates/ReferenteSterilisationTemplate.php`

```php
<?php

namespace App\Support\ReviewTemplates;

/**
 * Trame d'entretien annuel — Référente stérilisation.
 *
 * Trame spécialisée : la section 1 interroge l'ergonomie du poste
 * de stérilisation et les outils de traçabilité ; la grille de
 * compétences (section 3) est centrée sur les protocoles, la
 * maintenance des équipements, la traçabilité et la transmission.
 */
class ReferenteSterilisationTemplate
{
    public const KEY = 'ref_steril';

    public static function definition(): array
    {
        $evaluationOptions = ['Dépassé', 'Atteint', 'Partiellement atteint', 'Non réalisé'];

        return [
            'key' => self::KEY,
            'label' => 'Référente stérilisation',
            'header' => [
                ['key' => 'responsabilites', 'label' => 'Responsabilité au cabinet (missions complémentaires)', 'owner' => 'employee'],
                ['key' => 'praticien_entretien', 'label' => "Praticien qui réalise l'entretien", 'owner' => 'manager'],
            ],
            'sections' => [
                [
                    'title' => '01. Bien-être & Qualité de Vie, Conditions de Travail',
                    'fields' => [
                        ['type' => 'scale_10', 'key' => 'bien_etre_travail', 'question' => 'Comment vous sentez-vous dans votre travail ?', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'bien_etre_poste', 'question' => 'Le poste que vous occupez vous plaît-il ?', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'bien_etre_relations', 'question' => "Les relations avec les autres membres de l'équipe sont-elles bonnes ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'bien_etre_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'ergonomie_sterilisation', 'question' => "Comment noteriez-vous l'ergonomie du poste de stérilisation ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'ergonomie_sterilisation_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'outils_sterilisation', 'question' => 'Comment noteriez-vous vos outils de travail (équipements, traçabilité, consommables) ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'outils_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => "02. Bilan de l'année écoulée",
                    'fields' => [
                        [
                            'type' => 'objectives_review',
                            'key' => 'bilan_objectifs',
                            'question' => "Évaluation des objectifs de l'année écoulée",
                            'hint' => "Échelle d'évaluation : dépassé / atteint / partiellement atteint / non réalisé",
                            'evaluation_options' => $evaluationOptions,
                        ],
                        ['type' => 'textarea', 'key' => 'bilan_commentaires', 'question' => 'Commentaires', 'owner' => 'manager'],
                        [
                            'type' => 'activities_table',
                            'key' => 'activites',
                            'question' => 'Quelles sont les activités réalisées et faits marquants ?',
                            'owner' => 'employee',
                        ],
                    ],
                ],
                [
                    'title' => '03. Bilan des compétences attendues pour le poste occupé',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'reussites_poste', 'question' => 'Que réussissez-vous le mieux dans votre poste actuel ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'plaisir_fonctions', 'question' => "Qu'aimez-vous le plus dans vos fonctions actuelles ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'axes_amelioration', 'question' => "Quels sont vos points d'amélioration ou compétences à acquérir ?", 'owner' => 'employee'],
                        [
                            'type' => 'competency_grid',
                            'key' => 'competences',
                            'question' => 'Bilan des compétences techniques, organisationnelles et relationnelles attendues',
                            'hint' => "Échelle d'évaluation : dépassé / atteint / partiellement atteint / non réalisé",
                            'evaluation_options' => $evaluationOptions,
                            'rows' => [
                                'Je maîtrise les protocoles de pré-désinfection, nettoyage et stérilisation',
                                "J'assure la maintenance préventive et curative des équipements (autoclaves, thermodésinfecteurs)",
                                'Je garantis la traçabilité des dispositifs médicaux et documents associés',
                                'Je forme et accompagne les assistantes aux bonnes pratiques de stérilisation',
                                'Je me tiens à jour des évolutions réglementaires et normatives',
                                'Je gère les stocks de consommables (indicateurs, sachets, produits)',
                                "Je suis à l'aise avec la communication au sein de l'équipe",
                            ],
                        ],
                        ['type' => 'textarea', 'key' => 'competences_non_utilisees', 'question' => "Avez-vous des compétences non utilisées susceptibles d'être mises à profit dans votre vie professionnelle ?", 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => "04. Perspectives pour l'année à venir",
                    'fields' => [
                        [
                            'type' => 'objectives_plan',
                            'key' => 'nouveaux_objectifs',
                            'question' => "Objectifs pour l'année à venir",
                            'owner' => 'manager',
                        ],
                    ],
                ],
                [
                    'title' => '05. Formations',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'formations_salarie', 'question' => 'Formations souhaitées par le salarié', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'formations_salarie_objectifs', 'question' => 'Objectifs visés (salarié)', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'formations_manager', 'question' => 'Formations souhaitées par le manager', 'owner' => 'manager'],
                        ['type' => 'textarea', 'key' => 'formations_manager_objectifs', 'question' => 'Objectifs visés (manager)', 'owner' => 'manager'],
                    ],
                ],
                [
                    'title' => "06. Conditions d'activité",
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'conditions_remarques', 'question' => "Quelles sont vos remarques, points d'amélioration ou suggestions d'amélioration liés à vos conditions d'activité ?", 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'charge_travail', 'question' => 'Comment évaluez-vous votre charge de travail ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'charge_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'vie_privee_pro', 'question' => "Quel est votre ressenti quant à l'articulation de votre vie privée / vie professionnelle ?", 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => "07. Synthèse de l'entretien annuel",
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'synthese_collaborateur', 'question' => 'Commentaire du collaborateur', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'synthese_manager', 'question' => 'Commentaire du manager', 'owner' => 'manager'],
                    ],
                ],
            ],
        ];
    }
}

```

---

# 7. Migrations

## `database/migrations/0001_01_01_000000_create_users_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

```

---

## `database/migrations/0001_01_01_000001_create_cache_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->bigInteger('expiration')->index();
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->bigInteger('expiration')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};

```

---

## `database/migrations/0001_01_01_000002_create_jobs_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};

```

---

## `database/migrations/2026_04_15_200000_add_review_fields_to_users.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('employee')->after('email');
            $table->string('position')->nullable()->after('role');
            $table->string('department')->nullable()->after('position');
            $table->date('hired_on')->nullable()->after('department');
            $table->foreignId('manager_id')->nullable()->after('hired_on')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('manager_id');
            $table->dropColumn(['role', 'position', 'department', 'hired_on']);
        });
    }
};

```

---

## `database/migrations/2026_04_15_200100_create_annual_reviews_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annual_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('year');
            $table->date('scheduled_for')->nullable();
            // scheduled -> employee_draft -> ready_for_manager -> manager_draft -> completed -> signed
            $table->string('status', 30)->default('scheduled');

            // Auto-évaluation salarié
            $table->text('self_achievements')->nullable();
            $table->text('self_difficulties')->nullable();
            $table->text('self_skills_developed')->nullable();
            $table->text('self_motivation')->nullable();

            // Bilan objectifs précédents
            $table->json('previous_objectives')->nullable();

            // Nouveaux objectifs
            $table->json('new_objectives')->nullable();

            // Développement / formation / mobilité
            $table->text('training_needs')->nullable();
            $table->text('career_development')->nullable();

            // Appréciation manager
            $table->text('manager_appreciation')->nullable();
            $table->text('manager_areas_for_improvement')->nullable();
            $table->unsignedTinyInteger('overall_rating')->nullable(); // 1..5

            // Commentaires finaux
            $table->text('employee_comments')->nullable();
            $table->text('manager_comments')->nullable();

            // Signatures
            $table->timestamp('employee_signed_at')->nullable();
            $table->timestamp('manager_signed_at')->nullable();

            $table->timestamps();

            $table->unique(['employee_id', 'year']);
            $table->index(['manager_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annual_reviews');
    }
};

```

---

## `database/migrations/2026_04_15_210000_rework_annual_reviews_for_templates.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ajoute les colonnes "template"
        Schema::table('annual_reviews', function (Blueprint $table) {
            $table->string('template_key', 50)->default('assistant')->after('status');
            $table->json('header')->nullable()->after('template_key');
            $table->json('employee_answers')->nullable()->after('header');
            $table->json('manager_answers')->nullable()->after('employee_answers');
        });

        // Supprime les colonnes "texte libre" de l'ancienne version
        Schema::table('annual_reviews', function (Blueprint $table) {
            $table->dropColumn([
                'self_achievements',
                'self_difficulties',
                'self_skills_developed',
                'self_motivation',
                'previous_objectives',
                'new_objectives',
                'training_needs',
                'career_development',
                'manager_appreciation',
                'manager_areas_for_improvement',
                'overall_rating',
                'employee_comments',
                'manager_comments',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('annual_reviews', function (Blueprint $table) {
            $table->dropColumn(['template_key', 'header', 'employee_answers', 'manager_answers']);

            $table->text('self_achievements')->nullable();
            $table->text('self_difficulties')->nullable();
            $table->text('self_skills_developed')->nullable();
            $table->text('self_motivation')->nullable();
            $table->json('previous_objectives')->nullable();
            $table->json('new_objectives')->nullable();
            $table->text('training_needs')->nullable();
            $table->text('career_development')->nullable();
            $table->text('manager_appreciation')->nullable();
            $table->text('manager_areas_for_improvement')->nullable();
            $table->unsignedTinyInteger('overall_rating')->nullable();
            $table->text('employee_comments')->nullable();
            $table->text('manager_comments')->nullable();
        });
    }
};

```

---

## `database/migrations/2026_04_16_100000_add_must_change_password_to_users.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('must_change_password')->default(false)->after('manager_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('must_change_password');
        });
    }
};

```

---

## `database/migrations/2026_04_16_120000_create_review_templates_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->string('label');
            $table->json('header')->nullable();
            $table->json('sections');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_templates');
    }
};

```

---

## `database/migrations/2026_04_17_100000_add_co_manager_to_annual_reviews.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('annual_reviews', function (Blueprint $table) {
            // Personne qui assiste à l'entretien (co-évaluateur).
            // Purement informatif : aucun droit supplémentaire par rapport
            // à son rôle applicatif habituel.
            $table->foreignId('co_manager_id')
                ->nullable()
                ->after('manager_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('annual_reviews', function (Blueprint $table) {
            $table->dropConstrainedForeignId('co_manager_id');
        });
    }
};

```

---

# 8. Seeders

## `database/seeders/DatabaseSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder par défaut — volontairement vide.
 *
 * Le cabinet dispose de seeders dédiés non destructifs :
 *  - TeamSeeder           : import initial de l'équipe (idempotent)
 *  - TestAccountsSeeder   : comptes de test + entretiens de démo
 *  - ReviewTemplateSeeder : init des trames en base
 *
 * L'entretien de chaque salarié doit être planifié manuellement par
 * la directrice via l'onglet « Entretiens → Planifier ». On ne crée
 * RIEN automatiquement ici pour éviter de recréer des entretiens
 * supprimés à chaque redéploiement.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Volontairement vide — voir le docblock ci-dessus.
    }
}

```

---

## `database/seeders/ReviewTemplateSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\ReviewTemplateModel;
use App\Support\ReviewTemplates\AdminAssistantTemplate;
use App\Support\ReviewTemplates\AssistantTemplate;
use App\Support\ReviewTemplates\DentisteTemplate;
use App\Support\ReviewTemplates\DirectriceTemplate;
use App\Support\ReviewTemplates\ReferenteAdministrativeTemplate;
use App\Support\ReviewTemplates\ReferenteCliniqueTemplate;
use App\Support\ReviewTemplates\ReferenteSterilisationTemplate;
use Illuminate\Database\Seeder;

/**
 * Initialise la table review_templates à partir des trames définies
 * en PHP. Idempotent (updateOrCreate).
 *
 * Exécution :  php artisan db:seed --class=ReviewTemplateSeeder --force
 */
class ReviewTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            AssistantTemplate::definition(),
            AdminAssistantTemplate::definition(),
            DirectriceTemplate::definition(),
            DentisteTemplate::definition(),
            ReferenteCliniqueTemplate::definition(),
            ReferenteSterilisationTemplate::definition(),
            ReferenteAdministrativeTemplate::definition(),
        ];

        foreach ($templates as $t) {
            ReviewTemplateModel::updateOrCreate(
                ['key' => $t['key']],
                [
                    'label' => $t['label'],
                    'header' => $t['header'] ?? [],
                    'sections' => $t['sections'],
                ]
            );
        }

        $this->command->info(count($templates) . ' trames importées dans review_templates.');
    }
}

```

---

## `database/seeders/TeamSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\Positions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Import de l'équipe réelle du Cabinet Dentaire de l'Obiou.
 *
 * Exécution :  php artisan db:seed --class=TeamSeeder --force
 *
 * Particularités :
 *  - Chaque compte reçoit un mot de passe temporaire unique.
 *  - must_change_password = true → la personne est forcée de choisir
 *    son propre mot de passe à la première connexion.
 *  - Le seeder est idempotent (firstOrCreate) : on peut le relancer
 *    sans écraser les comptes existants.
 *  - Les comptes de démo (directrice@cabinet.fr, etc.) sont supprimés
 *    en fin d'exécution.
 */
class TeamSeeder extends Seeder
{
    public function run(): void
    {
        //
        // 1) Directrice d'exploitation (admin)
        //
        $magalie = User::firstOrCreate(
            ['email' => 'gestion@cabinetdentaireobiou.fr'],
            [
                'name' => 'Magalie LASTELLA',
                'password' => Hash::make('Obiou7823'),
                'role' => User::ROLE_ADMIN,
                'position' => Positions::OPERATIONS_DIRECTOR,
                'department' => 'Direction',
                'must_change_password' => true,
            ]
        );

        //
        // 2) Dentistes / Chirurgiens-dentistes (role = manager).
        //    Les entretiens des dentistes sont conduits entre pairs ;
        //    leur user.manager_id reste null (ils sont au sommet de
        //    la hiérarchie, pas évalués par la directrice).
        //
        $dentistsSpec = [
            ['email' => 'tandeol@hotmail.fr',                'name' => 'Thibault ANDEOL',    'password' => 'Obiou9124'],
            ['email' => 'robin.basset@me.com',               'name' => 'Robin BASSET',       'password' => 'Obiou6405'],
            ['email' => 'drmeierthomas@gmail.com',           'name' => 'Thomas MEIER',       'password' => 'Obiou2748'],
            ['email' => 'dr.merindol@gmail.com',             'name' => 'Agathe MERINDOL',    'password' => 'Obiou8371'],
            ['email' => 'adaudeville@gmail.com',             'name' => 'Alice DAUDEVILLE',   'password' => 'Obiou4512'],
            ['email' => 'caro.donadieu@gmail.com',           'name' => 'Caroline DONADIEU',  'password' => 'Obiou8739'],
            ['email' => 'dr.rach.lei@gmail.com',             'name' => 'Leila RACHIDI',      'password' => 'Obiou3256'],
            ['email' => 'tifenn8@hotmail.fr',                'name' => 'Tifenn MANCHE',      'password' => 'Obiou6041'],
            ['email' => 'loic.fontanel@gmail.com',           'name' => 'Loic FONTANEL',      'password' => 'Obiou1387'],
            ['email' => 'dr.wiktoria.orysiak@gmail.com',     'name' => 'Wiktoria ORYSIAK',   'password' => 'Obiou7923'],
            ['email' => 'perrine.obstetar@hotmail.fr',       'name' => 'Perrine OBSTETAR',   'password' => 'Obiou5468'],
            ['email' => 'docteur.renecorail@gmail.com',      'name' => 'Diane RENE CORAIL',  'password' => 'Obiou9652'],
        ];

        $dentistByName = [];
        foreach ($dentistsSpec as $d) {
            $dentistByName[$d['name']] = User::firstOrCreate(
                ['email' => $d['email']],
                [
                    'name' => $d['name'],
                    'password' => Hash::make($d['password']),
                    'role' => User::ROLE_MANAGER,
                    'position' => Positions::DENTIST,
                    'department' => 'Soins',
                    'must_change_password' => true,
                ]
            );
        }

        //
        // 3) Assistantes et référentes (role = employee)
        //    Format : [email, name, position_key, manager_name, temp_password]
        //    position_key : 'dent' / 'admin' / 'ref_clinique' / 'ref_admin' / 'ref_steril'
        //
        $employees = [
            ['anae.baron28@gmail.com',        'Anaé BARON',            'admin',         'Magalie LASTELLA',  'Obiou2941'],
            ['laura.chaudet@hotmail.com',     'Laura CHAUDET',         'dent',          'Thibault ANDEOL',   'Obiou5037'],
            ['julie@delbar.fr',               'Julie DELBAR',          'dent',          'Robin BASSET',      'Obiou6128'],
            ['cloclotempesta@gmail.com',      'Chloé DIAFERIA',        'dent',          'Thomas MEIER',      'Obiou4216'],
            ['chgeeraert@gmail.com',          'Christel GIODDA',       'dent',          'Agathe MERINDOL',   'Obiou9384'],
            ['audreylambert.b@gmail.com',     'Audrey LAMBERT',        'ref_clinique',  'Magalie LASTELLA',  'Obiou1572'],
            ['veronique.larsen@laposte.net',  'Véronique LARSEN',      'admin',         'Magalie LASTELLA',  'Obiou8063'],
            ['julopes@hotmail.fr',            'Julie LOPES',           'admin',         'Magalie LASTELLA',  'Obiou3719'],
            ['taoutaoulinda@gmail.com',       'Linda MAKHLOUCHE',      'dent',          'Agathe MERINDOL',   'Obiou6842'],
            ['echelard.e@gmail.com',          'Elisa MARCHISIO',       'admin',         'Magalie LASTELLA',  'Obiou4591'],
            ['lauriemasnada@hotmail.com',     'Laure MASNADA',         'dent',          'Robin BASSET',      'Obiou2376'],
            ['fmazzilli9@icloud.com',         'Fiona MAZZILLI',        'dent',          'Thomas MEIER',      'Obiou7051'],
            ['severine.de-palma@orange.fr',   'Severine MULERO',       'ref_admin',     'Magalie LASTELLA',  'Obiou5284'],
            ['cristianoanea@yahoo.com',       'Cristian OANEA',        'dent',          'Thibault ANDEOL',   'Obiou9617'],
            ['palamuso.marine91@gmail.com',   'Marine PALAMUSO',       'dent',          'Robin BASSET',      'Obiou3462'],
            ['melissa.ptrtp@gmail.com',       'Melissa PATIR',         'admin',         'Magalie LASTELLA',  'Obiou8190'],
            ['lolprost@gmail.com',            'Laurence PROST',        'dent',          'Robin BASSET',      'Obiou5743'],
            ['claudiarivasr85@gmail.com',     'Claudia RIVAS',         'dent',          'Thibault ANDEOL',   'Obiou2905'],
            ['charlotterocahague@yahoo.fr',   'Charlotte ROCA-HAGUE',  'dent',          'Thibault ANDEOL',   'Obiou6831'],
            ['sarasara38400@gmail.com',       'Sara ROCCHI',           'dent',          'Agathe MERINDOL',   'Obiou4057'],
            ['sarahpatu@hotmail.fr',          'Sarah SAUMON',          'ref_steril',    'Magalie LASTELLA',  'Obiou7629'],
            ['trapieremma@outlook.fr',        'Emma TRAPIER',          'dent',          'Thomas MEIER',      'Obiou1843'],
            ['clara.vazm@outlook.fr',         'Clara VAZ MARQUES',     'admin',         'Magalie LASTELLA',  'Obiou5076'],
            ['berra.gyorur@gmail.com',        'Berra YORUR',           'dent',          'Thomas MEIER',      'Obiou3598'],
        ];

        $created = 0;
        $skipped = 0;
        foreach ($employees as [$email, $name, $posKey, $managerName, $password]) {
            $position = match ($posKey) {
                'admin' => Positions::ADMIN_ASSISTANT,
                'dent' => Positions::DENTAL_ASSISTANT,
                'ref_clinique' => Positions::CLINICAL_REFERENT,
                'ref_admin' => Positions::ADMIN_REFERENT,
                'ref_steril' => Positions::STERILIZATION_REFERENT,
                default => Positions::DENTAL_ASSISTANT,
            };
            $manager = $managerName === 'Magalie LASTELLA'
                ? $magalie
                : ($dentistByName[$managerName] ?? null);

            if (! $manager) {
                $this->command->warn("  Manager introuvable pour {$name} : {$managerName}");
                $skipped++;
                continue;
            }

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make($password),
                    'role' => User::ROLE_EMPLOYEE,
                    'position' => $position,
                    'department' => in_array($posKey, ['admin', 'ref_admin'], true) ? 'Administratif' : 'Soins',
                    'manager_id' => $manager->id,
                    'must_change_password' => true,
                ]
            );

            if ($user->wasRecentlyCreated) {
                $created++;
            } else {
                $skipped++;
            }
        }

        //
        // 4) Nettoyage des comptes de démonstration et des anciens
        //    placeholders @cabinetdentaireobiou.fr créés avant la
        //    réception de la liste réelle des dentistes.
        //
        $obsoleteEmails = [
            // démo initiale
            'directrice@cabinet.fr',
            'dentiste@cabinet.fr',
            'amelie.assistante@cabinet.fr',
            'karim.assistant@cabinet.fr',
            'sophie.admin@cabinet.fr',
            'demo@smashyou.fr',
            // placeholders dentistes (avant réception des vrais emails)
            'thibault.andeol@cabinetdentaireobiou.fr',
            'robin.basset@cabinetdentaireobiou.fr',
            'thomas.meier@cabinetdentaireobiou.fr',
            'agathe.merindol@cabinetdentaireobiou.fr',
        ];
        $deleted = User::whereIn('email', $obsoleteEmails)->delete();

        $this->command->info("TeamSeeder terminé :");
        $this->command->info("  • 1 directrice · " . count($dentistsSpec) . " dentistes · {$created} salarié(e)s créé(e)s (" . ($skipped ? $skipped . ' existait(ent) déjà' : 'tous nouveaux') . ")");
        if ($deleted > 0) {
            $this->command->info("  • {$deleted} compte(s) obsolète(s) supprimé(s)");
        }
    }
}

```

---

## `database/seeders/TestAccountsSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\AnnualReview;
use App\Models\User;
use App\Support\Positions;
use App\Support\ReviewTemplate;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Crée un compte de test par poste métier + un entretien planifié
 * pour chaque compte, pour permettre à la directrice de vérifier
 * le flux complet (auto-éval, manager, signature, PDF).
 *
 * Tous les comptes ont le même mot de passe simple « Test1234 » et
 * must_change_password = false (pas de redirection forcée).
 *
 * Exécution :  php artisan db:seed --class=TestAccountsSeeder --force
 */
class TestAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $magalie = User::where('email', 'gestion@cabinetdentaireobiou.fr')->first();
        $thibault = User::where('email', 'tandeol@hotmail.fr')->first();

        $password = Hash::make('Test1234');

        //
        // 1) Créer les 7 comptes test (1 par poste)
        //
        $testAccounts = [
            [
                'email' => 'test.directrice@cabinetdentaireobiou.fr',
                'name' => 'Test Directrice',
                'role' => User::ROLE_ADMIN,
                'position' => Positions::OPERATIONS_DIRECTOR,
                'department' => 'Direction',
                'manager' => null,
            ],
            [
                'email' => 'test.dentiste@cabinetdentaireobiou.fr',
                'name' => 'Test Dentiste',
                'role' => User::ROLE_MANAGER,
                'position' => Positions::DENTIST,
                'department' => 'Soins',
                'manager' => null,
            ],
            [
                'email' => 'test.assistante.dentaire@cabinetdentaireobiou.fr',
                'name' => 'Test Assistante Dentaire',
                'role' => User::ROLE_EMPLOYEE,
                'position' => Positions::DENTAL_ASSISTANT,
                'department' => 'Soins',
                'manager' => $thibault,
            ],
            [
                'email' => 'test.assistante.administrative@cabinetdentaireobiou.fr',
                'name' => 'Test Assistante Administrative',
                'role' => User::ROLE_EMPLOYEE,
                'position' => Positions::ADMIN_ASSISTANT,
                'department' => 'Administratif',
                'manager' => $magalie,
            ],
            [
                'email' => 'test.referente.clinique@cabinetdentaireobiou.fr',
                'name' => 'Test Référente Clinique',
                'role' => User::ROLE_EMPLOYEE,
                'position' => Positions::CLINICAL_REFERENT,
                'department' => 'Soins',
                'manager' => $magalie,
            ],
            [
                'email' => 'test.referente.administrative@cabinetdentaireobiou.fr',
                'name' => 'Test Référente Administrative',
                'role' => User::ROLE_EMPLOYEE,
                'position' => Positions::ADMIN_REFERENT,
                'department' => 'Administratif',
                'manager' => $magalie,
            ],
            [
                'email' => 'test.referente.sterilisation@cabinetdentaireobiou.fr',
                'name' => 'Test Référente Stérilisation',
                'role' => User::ROLE_EMPLOYEE,
                'position' => Positions::STERILIZATION_REFERENT,
                'department' => 'Soins',
                'manager' => $magalie,
            ],
        ];

        $created = 0;
        $users = [];
        foreach ($testAccounts as $a) {
            $user = User::updateOrCreate(
                ['email' => $a['email']],
                [
                    'name' => $a['name'],
                    'password' => $password,
                    'role' => $a['role'],
                    'position' => $a['position'],
                    'department' => $a['department'],
                    'manager_id' => $a['manager']?->id,
                    'must_change_password' => false,
                ]
            );
            $users[$a['email']] = $user;
            if ($user->wasRecentlyCreated) {
                $created++;
            }
        }

        //
        // 2) Créer un entretien planifié pour chaque compte test.
        //    Les 5 employés → manager = test.dentiste (celui qui conduit).
        //    Le test.dentiste → manager = test.directrice (sa propre review).
        //    La test.directrice → pas d'entretien (rien au-dessus d'elle).
        //
        $year = (int) Carbon::now()->year;
        $testDentiste = $users['test.dentiste@cabinetdentaireobiou.fr'] ?? null;
        $testDirectrice = $users['test.directrice@cabinetdentaireobiou.fr'] ?? null;

        $entretiensAssignments = [
            'test.assistante.dentaire@cabinetdentaireobiou.fr'      => $testDentiste,
            'test.assistante.administrative@cabinetdentaireobiou.fr' => $testDirectrice,
            'test.referente.clinique@cabinetdentaireobiou.fr'        => $testDentiste,
            'test.referente.administrative@cabinetdentaireobiou.fr'  => $testDirectrice,
            'test.referente.sterilisation@cabinetdentaireobiou.fr'   => $testDentiste,
            'test.dentiste@cabinetdentaireobiou.fr'                  => $testDirectrice,
        ];

        $reviewsCreated = 0;
        foreach ($entretiensAssignments as $email => $manager) {
            $employee = $users[$email] ?? null;
            if (! $employee || ! $manager || $employee->id === $manager->id) {
                continue;
            }
            $review = AnnualReview::firstOrCreate(
                ['employee_id' => $employee->id, 'year' => $year],
                [
                    'manager_id' => $manager->id,
                    'scheduled_for' => Carbon::now()->addDays(7)->toDateString(),
                    'status' => AnnualReview::STATUS_SCHEDULED,
                    'template_key' => ReviewTemplate::keyForPosition($employee->position),
                ]
            );
            if ($review->wasRecentlyCreated) {
                $reviewsCreated++;
            }
        }

        $this->command->info($created . ' compte(s) de test créé(s) / ' . count($testAccounts) . ' au total.');
        $this->command->info($reviewsCreated . ' entretien(s) de test planifié(s).');
        $this->command->info('Mot de passe commun : Test1234');
    }
}

```

---

# 9. Vues Blade

## `resources/views/app.blade.php`

```php
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Cabinet Dentaire') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>

```

---

## `resources/views/reviews/print.blade.php`

```php
@php
    /**
     * Vue imprimable d'un entretien annuel.
     * L'utilisateur utilise Ctrl/Cmd+P et "Enregistrer au format PDF".
     */
    $employeeAnswers = $review->employee_answers ?? [];
    $managerAnswers = $review->manager_answers ?? [];
    $header = $review->header ?? [];

    $fieldAnswers = function (array $field) use ($employeeAnswers, $managerAnswers) {
        $owner = $field['owner'] ?? null;
        if ($owner === 'manager') return $managerAnswers[$field['key']] ?? null;
        return $employeeAnswers[$field['key']] ?? null;
    };
    $formatDate = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->format('d/m/Y') : '—';
    $formatDateTime = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->format('d/m/Y \à H:i') : null;

    $filename = sprintf(
        '%s_%s_Entretien_%d',
        $review->year,
        \Illuminate\Support\Str::upper(\Illuminate\Support\Str::slug($review->employee->name, '_')),
        $review->year
    );
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $filename }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        @page { size: A4; margin: 1.2cm 1.4cm; }
        * { box-sizing: border-box; }
        html, body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #0F172A;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        a { color: inherit; text-decoration: none; }

        /* Barre d'action en haut (cachée à l'impression) */
        .toolbar {
            background: #ECFEFF;
            border-bottom: 2px solid #14B8A6;
            padding: 12px 20px;
            display: flex;
            gap: 12px;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }
        .toolbar button, .toolbar a {
            background: #115E59;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
        }
        .toolbar a.ghost { background: transparent; color: #115E59; border: 1px solid #14B8A6; }
        .toolbar .hint { font-size: 11px; color: #475569; width: 100%; text-align: center; margin-top: 4px; }

        /* Conteneur principal */
        .page { max-width: 780px; margin: 0 auto; padding: 20px; }

        /* En-tête avec logo */
        .logo-header {
            background: #0F4C47;
            color: white;
            padding: 14px 18px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
        }
        .logo-mark {
            background: white;
            color: #0F4C47;
            padding: 8px 12px;
            border-radius: 4px;
            font-weight: 900;
            font-size: 9pt;
            line-height: 1.05;
            letter-spacing: 0.05em;
            white-space: pre;
        }
        .logo-title { font-size: 14pt; font-weight: 700; margin: 0; }
        .logo-sub { font-size: 10pt; opacity: 0.9; margin-top: 2px; }

        /* Infos entête */
        table.info { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.info th, table.info td {
            border: 1px solid #CBD5E1;
            padding: 5px 8px;
            font-size: 9.5pt;
            text-align: left;
            vertical-align: top;
        }
        table.info th { background: #F1F5F9; font-weight: 600; width: 22%; }

        /* Sections */
        h2.section-title {
            font-size: 12pt;
            color: #FFFFFF;
            background: #115E59;
            padding: 7px 12px;
            border-radius: 4px;
            margin: 20px 0 10px;
        }

        /* Champ */
        .field { margin: 10px 0; page-break-inside: avoid; }
        .field .label {
            font-size: 10pt;
            font-weight: 600;
            color: #0F172A;
            background: #FEF3C7;
            padding: 4px 8px;
            border-radius: 3px;
        }
        .field .hint { font-size: 8.5pt; color: #64748B; margin-top: 2px; font-style: italic; }
        .field .value {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            padding: 6px 8px;
            border-radius: 3px;
            margin-top: 4px;
            white-space: pre-wrap;
            min-height: 20px;
        }
        .empty { color: #94A3B8; font-style: italic; }

        /* Échelle 1..10 */
        .scale {
            display: flex;
            gap: 2px;
            margin-top: 6px;
        }
        .scale-val {
            flex: 1;
            text-align: center;
            border: 1px solid #CBD5E1;
            padding: 5px 0;
            font-size: 9pt;
            font-weight: 600;
            background: #fff;
        }
        .scale-val.selected {
            background: #FDE047;
            border-color: #F59E0B;
            color: #0F172A;
        }

        /* Tables */
        table.grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 9.5pt;
        }
        table.grid th, table.grid td {
            border: 1px solid #CBD5E1;
            padding: 6px 8px;
            vertical-align: top;
        }
        table.grid th { background: #F1F5F9; font-weight: 600; }
        table.grid td.row-label { background: #FEF3C7; font-weight: 600; width: 28%; }

        /* Signatures */
        .signatures {
            margin-top: 30px;
            display: flex;
            gap: 16px;
            page-break-inside: avoid;
        }
        .signature-box {
            flex: 1;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            padding: 14px;
            min-height: 80px;
        }
        .signature-box .who { font-weight: 700; color: #115E59; margin-bottom: 4px; }
        .signature-box .name { font-size: 10pt; }
        .signature-box .signed { color: #047857; font-weight: 600; margin-top: 10px; }
        .signature-box .unsigned { color: #94A3B8; font-style: italic; margin-top: 10px; }

        @media print {
            .toolbar { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .page { padding: 0; max-width: none; }
        }
    </style>
</head>
<body>

<div class="toolbar">
    <button onclick="window.print()">📄 Enregistrer en PDF</button>
    <a href="{{ route('reviews.show', $review->id) }}" class="ghost">← Retour</a>
    <div class="hint">
        Une boîte de dialogue va s'ouvrir — choisissez <strong>Enregistrer au format PDF</strong>
        comme destination.
    </div>
</div>

<div class="page">

    <div class="logo-header">
        <div class="logo-mark">CABINET
DENTAIRE
DE L'OBIOU</div>
        <div>
            <div class="logo-title">Entretien annuel {{ $review->year }}</div>
            <div class="logo-sub">{{ $review->employee->name }} — {{ $review->employee->position ?? '—' }}</div>
        </div>
    </div>

    <table class="info">
        <tr>
            <th>Nom complet</th>
            <td>{{ $review->employee->name }}</td>
            <th>Date d'embauche</th>
            <td>{{ $formatDate($review->employee->hired_on) }}</td>
        </tr>
        <tr>
            <th>Poste occupé</th>
            <td>{{ $review->employee->position ?? '—' }}</td>
            <th>Date de l'entretien</th>
            <td>{{ $formatDate($review->scheduled_for) }}</td>
        </tr>
        <tr>
            <th>Qui réalise l'entretien</th>
            <td colspan="3">{{ $review->manager?->name ?? '—' }}</td>
        </tr>
        @if ($review->coManager)
            <tr>
                <th>Qui assiste à l'entretien</th>
                <td colspan="3">{{ $review->coManager->name }}</td>
            </tr>
        @endif
        @foreach ($template['header'] ?? [] as $headerField)
            <tr>
                <th>{{ $headerField['label'] }}</th>
                <td colspan="3">{{ $header[$headerField['key']] ?? '' ?: '—' }}</td>
            </tr>
        @endforeach
    </table>

    @foreach ($template['sections'] as $section)
        <h2 class="section-title">{{ $section['title'] }}</h2>

        @foreach ($section['fields'] as $field)
            @php
                $type = $field['type'] ?? '';
                $value = $fieldAnswers($field);
            @endphp

            @if ($type === 'scale_10')
                <div class="field">
                    <div class="label">{{ $field['question'] }}</div>
                    <div class="scale">
                        @for ($i = 1; $i <= 10; $i++)
                            <div class="scale-val @if ((int) $value === $i) selected @endif">{{ $i }}</div>
                        @endfor
                    </div>
                </div>

            @elseif ($type === 'text' || $type === 'textarea' || $type === 'choice')
                <div class="field">
                    <div class="label">{{ $field['question'] }}</div>
                    <div class="value">{{ $value ?: '—' }}</div>
                </div>

            @elseif ($type === 'objectives_review')
                <div class="field">
                    <div class="label">{{ $field['question'] }}</div>
                    @if (!empty($field['hint']))
                        <div class="hint">{{ $field['hint'] }}</div>
                    @endif
                    <table class="grid">
                        <thead>
                            <tr>
                                <th style="width: 60%;">Objectif fixé l'an passé</th>
                                <th>Évaluation manager</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $rows = $managerAnswers[$field['key']] ?? []; @endphp
                            @forelse ($rows as $row)
                                <tr>
                                    <td>{{ is_array($row) ? ($row['objectif'] ?? '') : '' }}</td>
                                    <td>{{ is_array($row) ? ($row['evaluation'] ?? '') : '' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="empty">— aucun objectif renseigné —</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            @elseif ($type === 'activities_table')
                <div class="field">
                    <div class="label">{{ $field['question'] }}</div>
                    <table class="grid">
                        <thead>
                            <tr>
                                <th>Réalisations</th>
                                <th>Réussites</th>
                                <th>Difficultés</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $rows = $employeeAnswers[$field['key']] ?? []; @endphp
                            @forelse ($rows as $row)
                                <tr>
                                    <td>{{ is_array($row) ? ($row['realisations'] ?? '') : '' }}</td>
                                    <td>{{ is_array($row) ? ($row['reussites'] ?? '') : '' }}</td>
                                    <td>{{ is_array($row) ? ($row['difficultes'] ?? '') : '' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="empty">— aucune activité renseignée —</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            @elseif ($type === 'objectives_plan')
                <div class="field">
                    <div class="label">{{ $field['question'] }}</div>
                    <table class="grid">
                        <thead>
                            <tr>
                                <th>Objectif</th>
                                <th>Indicateurs de réalisation</th>
                                <th>Moyens à mettre en œuvre</th>
                                <th>Délais</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $rows = $managerAnswers[$field['key']] ?? []; @endphp
                            @forelse ($rows as $row)
                                <tr>
                                    <td>{{ is_array($row) ? ($row['objectif'] ?? '') : '' }}</td>
                                    <td>{{ is_array($row) ? ($row['indicateurs'] ?? '') : '' }}</td>
                                    <td>{{ is_array($row) ? ($row['moyens'] ?? '') : '' }}</td>
                                    <td>{{ is_array($row) ? ($row['delais'] ?? '') : '' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="empty">— aucun objectif renseigné —</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            @elseif ($type === 'competency_grid')
                <div class="field">
                    <div class="label">{{ $field['question'] }}</div>
                    @if (!empty($field['hint']))
                        <div class="hint">{{ $field['hint'] }}</div>
                    @endif
                    <table class="grid">
                        <thead>
                            <tr>
                                <th style="width: 28%;">Compétence</th>
                                <th>Auto-évaluation</th>
                                <th>Commentaires manager</th>
                                <th>Actions à mener</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $empCells = is_array($employeeAnswers[$field['key']] ?? null)
                                    ? $employeeAnswers[$field['key']]
                                    : [];
                                $mgrCells = is_array($managerAnswers[$field['key']] ?? null)
                                    ? $managerAnswers[$field['key']]
                                    : [];
                            @endphp
                            @foreach ($field['rows'] ?? [] as $i => $rowLabel)
                                @php
                                    $autoVal = is_array($empCells[$i] ?? null)
                                        ? ($empCells[$i]['auto'] ?? '')
                                        : ($empCells[$i] ?? '');
                                    $mgrObj = is_array($mgrCells[$i] ?? null) ? $mgrCells[$i] : [];
                                @endphp
                                <tr>
                                    <td class="row-label">{{ $rowLabel }}</td>
                                    <td>{{ $autoVal }}</td>
                                    <td>{{ $mgrObj['manager_comment'] ?? $mgrObj['commentaire'] ?? '' }}</td>
                                    <td>{{ $mgrObj['action'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @endif
        @endforeach
    @endforeach

    <!-- Signatures -->
    <h2 class="section-title">Signatures</h2>
    <div class="signatures">
        <div class="signature-box">
            <div class="who">Salarié</div>
            <div class="name">{{ $review->employee->name }}</div>
            @if ($review->employee_signed_at)
                <div class="signed">✓ Signé le {{ $formatDateTime($review->employee_signed_at) }}</div>
            @else
                <div class="unsigned">Non signé</div>
            @endif
        </div>
        <div class="signature-box">
            <div class="who">Qui réalise l'entretien</div>
            <div class="name">{{ $review->manager?->name ?? '—' }}</div>
            @if ($review->manager_signed_at)
                <div class="signed">✓ Signé le {{ $formatDateTime($review->manager_signed_at) }}</div>
            @else
                <div class="unsigned">Non signé</div>
            @endif
        </div>
    </div>

    @if ($review->coManager)
        <div style="margin-top: 14px; padding: 12px; border: 1px dashed #CBD5E1; border-radius: 6px; background: #F8FAFC;">
            <div style="font-weight: 700; color: #115E59; margin-bottom: 2px;">Qui assiste à l'entretien</div>
            <div>{{ $review->coManager->name }}</div>
            <div style="font-size: 11px; color: #64748B; margin-top: 4px; font-style: italic;">
                Présence pour trace — ne signe pas l'entretien.
            </div>
        </div>
    @endif

</div>

<script>
    // Suggère un nom de fichier propre dans la boîte d'impression
    document.title = @json($filename);
</script>
</body>
</html>

```

---

## `resources/views/reviews/pdf.blade.php`

```php
@php
    $employeeAnswers = $review->employee_answers ?? [];
    $managerAnswers = $review->manager_answers ?? [];
    $header = $review->header ?? [];

    $fieldAnswers = function (array $field) use ($employeeAnswers, $managerAnswers) {
        $owner = $field['owner'] ?? null;
        if ($owner === 'manager') return $managerAnswers[$field['key']] ?? null;
        return $employeeAnswers[$field['key']] ?? null;
    };

    $annotation = function (string $key) use ($managerAnswers) {
        return $managerAnswers['_note_' . $key] ?? null;
    };
    $fmt = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->format('d/m/Y') : '—';
    $fmtDT = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->format('d/m/Y \à H:i') : null;
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Entretien {{ $review->year }} — {{ $review->employee->name }}</title>
    <style>
        @page { size: A4; margin: 1.2cm 1.4cm; }
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5pt;
            color: #0F172A;
            margin: 0;
            padding: 0;
        }

        /* Bande logo / entête */
        table.hero {
            width: 100%;
            background: #0F4C47;
            color: #FFFFFF;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.hero td {
            padding: 12px 14px;
            vertical-align: middle;
            border: 0;
        }
        .logo-mark {
            display: inline-block;
            background: #FFFFFF;
            color: #0F4C47;
            padding: 8px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 8.5pt;
            line-height: 1.05;
            letter-spacing: 0.05em;
            white-space: pre;
        }
        .hero-title { font-size: 14pt; font-weight: bold; }
        .hero-sub { font-size: 10pt; }

        /* Table d'infos */
        table.info { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.info th, table.info td {
            border: 1px solid #CBD5E1;
            padding: 5px 7px;
            font-size: 9pt;
            text-align: left;
            vertical-align: top;
        }
        table.info th { background: #F1F5F9; font-weight: bold; width: 22%; }

        /* Sections */
        h2.section-title {
            font-size: 11pt;
            color: #FFFFFF;
            background: #115E59;
            padding: 6px 10px;
            margin: 16px 0 8px;
            border-radius: 3px;
        }

        /* Champs */
        .field { margin: 8px 0; page-break-inside: avoid; }
        .field .label {
            font-size: 9.5pt;
            font-weight: bold;
            color: #0F172A;
            background: #FEF3C7;
            padding: 4px 7px;
            border-radius: 2px;
        }
        .field .hint { font-size: 8pt; color: #64748B; font-style: italic; margin-top: 2px; }
        .field .value {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            padding: 5px 7px;
            margin-top: 4px;
            white-space: pre-wrap;
            min-height: 18px;
            font-size: 9.5pt;
        }
        .empty { color: #94A3B8; font-style: italic; }
        .annotation {
            border-left: 3px solid #E9D5FF;
            background: #FAF5FF;
            padding: 4px 8px;
            margin-top: 4px;
            font-size: 9pt;
        }
        .annotation .ann-label { font-weight: bold; color: #7C3AED; font-size: 8pt; }

        /* Échelle 1..10 — rendu via table pour dompdf */
        table.scale { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.scale td {
            text-align: center;
            border: 1px solid #CBD5E1;
            padding: 5px 0;
            font-size: 9pt;
            font-weight: bold;
            background: #FFFFFF;
            width: 10%;
        }
        table.scale td.selected {
            background: #FDE047;
            border-color: #F59E0B;
        }

        /* Tables de données */
        table.grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            font-size: 9pt;
        }
        table.grid th, table.grid td {
            border: 1px solid #CBD5E1;
            padding: 5px 7px;
            vertical-align: top;
        }
        table.grid th { background: #F1F5F9; font-weight: bold; text-align: left; }
        table.grid td.row-label { background: #FEF3C7; font-weight: bold; width: 28%; }

        /* Signatures */
        table.signatures { width: 100%; border-collapse: separate; border-spacing: 10px 0; margin-top: 18px; }
        table.signatures td {
            width: 50%;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            padding: 12px;
            vertical-align: top;
        }
        .sig-who { font-weight: bold; color: #115E59; margin-bottom: 4px; font-size: 10pt; }
        .sig-signed { color: #047857; font-weight: bold; margin-top: 8px; }
        .sig-unsigned { color: #94A3B8; font-style: italic; margin-top: 8px; }
    </style>
</head>
<body>

<table class="hero">
    <tr>
        <td style="width: 130px;">
            <span class="logo-mark">CABINET
DENTAIRE
DE L'OBIOU</span>
        </td>
        <td>
            <div class="hero-title">Entretien annuel {{ $review->year }}</div>
            <div class="hero-sub">{{ $review->employee->name }} — {{ $review->employee->position ?? '—' }}</div>
        </td>
    </tr>
</table>

<table class="info">
    <tr>
        <th>Nom complet</th>
        <td>{{ $review->employee->name }}</td>
        <th>Date d'embauche</th>
        <td>{{ $fmt($review->employee->hired_on) }}</td>
    </tr>
    <tr>
        <th>Poste occupé</th>
        <td>{{ $review->employee->position ?? '—' }}</td>
        <th>Date de l'entretien</th>
        <td>{{ $fmt($review->scheduled_for) }}</td>
    </tr>
    <tr>
        <th>Qui réalise l'entretien</th>
        <td colspan="3">{{ $review->manager?->name ?? '—' }}</td>
    </tr>
    @if ($review->coManager)
        <tr>
            <th>Qui assiste à l'entretien</th>
            <td colspan="3">{{ $review->coManager->name }}</td>
        </tr>
    @endif
    @foreach ($template['header'] ?? [] as $headerField)
        <tr>
            <th>{{ $headerField['label'] }}</th>
            <td colspan="3">{{ $header[$headerField['key']] ?? '' ?: '—' }}</td>
        </tr>
    @endforeach
</table>

@foreach ($template['sections'] as $section)
    <h2 class="section-title">{{ $section['title'] }}</h2>

    @foreach ($section['fields'] as $field)
        @php
            $type = $field['type'] ?? '';
            $value = $fieldAnswers($field);
        @endphp

        @php $note = ($field['owner'] ?? '') === 'employee' ? $annotation($field['key'] ?? '') : null; @endphp

        @if ($type === 'scale_10')
            <div class="field">
                <div class="label">{{ $field['question'] }}</div>
                <table class="scale">
                    <tr>
                        @for ($i = 1; $i <= 10; $i++)
                            <td @if ((int) $value === $i) class="selected" @endif>{{ $i }}</td>
                        @endfor
                    </tr>
                </table>
                @if ($note)
                    <div class="annotation"><span class="ann-label">Annotation :</span> {{ $note }}</div>
                @endif
            </div>

        @elseif ($type === 'text' || $type === 'textarea' || $type === 'choice')
            <div class="field">
                <div class="label">{{ $field['question'] }}</div>
                <div class="value">{{ $value ?: '—' }}</div>
                @if ($note)
                    <div class="annotation"><span class="ann-label">Annotation :</span> {{ $note }}</div>
                @endif
            </div>

        @elseif ($type === 'objectives_review')
            <div class="field">
                <div class="label">{{ $field['question'] }}</div>
                @if (!empty($field['hint']))
                    <div class="hint">{{ $field['hint'] }}</div>
                @endif
                <table class="grid">
                    <thead>
                        <tr>
                            <th style="width: 60%;">Objectif fixé l'an passé</th>
                            <th>Évaluation manager</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rows = $managerAnswers[$field['key']] ?? []; @endphp
                        @forelse ($rows as $row)
                            <tr>
                                <td>{{ is_array($row) ? ($row['objectif'] ?? '') : '' }}</td>
                                <td>{{ is_array($row) ? ($row['evaluation'] ?? '') : '' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="empty">— aucun objectif renseigné —</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif ($type === 'activities_table')
            <div class="field">
                <div class="label">{{ $field['question'] }}</div>
                <table class="grid">
                    <thead>
                        <tr>
                            <th>Réalisations</th>
                            <th>Réussites</th>
                            <th>Difficultés</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rows = $employeeAnswers[$field['key']] ?? []; @endphp
                        @forelse ($rows as $row)
                            <tr>
                                <td>{{ is_array($row) ? ($row['realisations'] ?? '') : '' }}</td>
                                <td>{{ is_array($row) ? ($row['reussites'] ?? '') : '' }}</td>
                                <td>{{ is_array($row) ? ($row['difficultes'] ?? '') : '' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="empty">— aucune activité renseignée —</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif ($type === 'objectives_plan')
            <div class="field">
                <div class="label">{{ $field['question'] }}</div>
                <table class="grid">
                    <thead>
                        <tr>
                            <th>Objectif</th>
                            <th>Indicateurs</th>
                            <th>Moyens</th>
                            <th>Délais</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rows = $managerAnswers[$field['key']] ?? []; @endphp
                        @forelse ($rows as $row)
                            <tr>
                                <td>{{ is_array($row) ? ($row['objectif'] ?? '') : '' }}</td>
                                <td>{{ is_array($row) ? ($row['indicateurs'] ?? '') : '' }}</td>
                                <td>{{ is_array($row) ? ($row['moyens'] ?? '') : '' }}</td>
                                <td>{{ is_array($row) ? ($row['delais'] ?? '') : '' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty">— aucun objectif renseigné —</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif ($type === 'competency_grid')
            <div class="field">
                <div class="label">{{ $field['question'] }}</div>
                @if (!empty($field['hint']))
                    <div class="hint">{{ $field['hint'] }}</div>
                @endif
                <table class="grid">
                    <thead>
                        <tr>
                            <th style="width: 28%;">Compétence</th>
                            <th>Auto-évaluation</th>
                            <th>Commentaires manager</th>
                            <th>Actions à mener</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $empCells = is_array($employeeAnswers[$field['key']] ?? null) ? $employeeAnswers[$field['key']] : [];
                            $mgrCells = is_array($managerAnswers[$field['key']] ?? null) ? $managerAnswers[$field['key']] : [];
                        @endphp
                        @foreach ($field['rows'] ?? [] as $i => $rowLabel)
                            @php
                                $autoVal = is_array($empCells[$i] ?? null) ? ($empCells[$i]['auto'] ?? '') : ($empCells[$i] ?? '');
                                $mgrObj = is_array($mgrCells[$i] ?? null) ? $mgrCells[$i] : [];
                            @endphp
                            <tr>
                                <td class="row-label">{{ $rowLabel }}</td>
                                <td>{{ $autoVal }}</td>
                                <td>{{ $mgrObj['manager_comment'] ?? $mgrObj['commentaire'] ?? '' }}</td>
                                <td>{{ $mgrObj['action'] ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @endif
    @endforeach
@endforeach

<h2 class="section-title">Signatures</h2>
<table class="signatures">
    <tr>
        <td>
            <div class="sig-who">Salarié</div>
            <div>{{ $review->employee->name }}</div>
            @if ($review->employee_signed_at)
                <div class="sig-signed">Signé le {{ $fmtDT($review->employee_signed_at) }}</div>
            @else
                <div class="sig-unsigned">Non signé</div>
            @endif
        </td>
        <td>
            <div class="sig-who">Qui réalise l'entretien</div>
            <div>{{ $review->manager?->name ?? '—' }}</div>
            @if ($review->manager_signed_at)
                <div class="sig-signed">Signé le {{ $fmtDT($review->manager_signed_at) }}</div>
            @else
                <div class="sig-unsigned">Non signé</div>
            @endif
        </td>
    </tr>
    @if ($review->coManager)
        <tr>
            <td colspan="2" style="padding-top: 10px;">
                <div class="sig-who">Qui assiste à l'entretien</div>
                <div>{{ $review->coManager->name }}</div>
                <div style="font-size: 8pt; color: #64748B; margin-top: 2px;">(présence pour trace, ne signe pas)</div>
            </td>
        </tr>
    @endif
</table>

</body>
</html>

```

---

# 10. Composants Vue

## `resources/js/Components/ApplicationLogo.vue`

```vue
<template>
    <svg viewBox="0 0 316 316" xmlns="http://www.w3.org/2000/svg">
        <path
            d="M305.8 81.125C305.77 80.995 305.69 80.885 305.65 80.755C305.56 80.525 305.49 80.285 305.37 80.075C305.29 79.935 305.17 79.815 305.07 79.685C304.94 79.515 304.83 79.325 304.68 79.175C304.55 79.045 304.39 78.955 304.25 78.845C304.09 78.715 303.95 78.575 303.77 78.475L251.32 48.275C249.97 47.495 248.31 47.495 246.96 48.275L194.51 78.475C194.33 78.575 194.19 78.725 194.03 78.845C193.89 78.955 193.73 79.045 193.6 79.175C193.45 79.325 193.34 79.515 193.21 79.685C193.11 79.815 192.99 79.935 192.91 80.075C192.79 80.285 192.71 80.525 192.63 80.755C192.58 80.875 192.51 80.995 192.48 81.125C192.38 81.495 192.33 81.875 192.33 82.265V139.625L148.62 164.795V52.575C148.62 52.185 148.57 51.805 148.47 51.435C148.44 51.305 148.36 51.195 148.32 51.065C148.23 50.835 148.16 50.595 148.04 50.385C147.96 50.245 147.84 50.125 147.74 49.995C147.61 49.825 147.5 49.635 147.35 49.485C147.22 49.355 147.06 49.265 146.92 49.155C146.76 49.025 146.62 48.885 146.44 48.785L93.99 18.585C92.64 17.805 90.98 17.805 89.63 18.585L37.18 48.785C37 48.885 36.86 49.035 36.7 49.155C36.56 49.265 36.4 49.355 36.27 49.485C36.12 49.635 36.01 49.825 35.88 49.995C35.78 50.125 35.66 50.245 35.58 50.385C35.46 50.595 35.38 50.835 35.3 51.065C35.25 51.185 35.18 51.305 35.15 51.435C35.05 51.805 35 52.185 35 52.575V232.235C35 233.795 35.84 235.245 37.19 236.025L142.1 296.425C142.33 296.555 142.58 296.635 142.82 296.725C142.93 296.765 143.04 296.835 143.16 296.865C143.53 296.965 143.9 297.015 144.28 297.015C144.66 297.015 145.03 296.965 145.4 296.865C145.5 296.835 145.59 296.775 145.69 296.745C145.95 296.655 146.21 296.565 146.45 296.435L251.36 236.035C252.72 235.255 253.55 233.815 253.55 232.245V174.885L303.81 145.945C305.17 145.165 306 143.725 306 142.155V82.265C305.95 81.875 305.89 81.495 305.8 81.125ZM144.2 227.205L100.57 202.515L146.39 176.135L196.66 147.195L240.33 172.335L208.29 190.625L144.2 227.205ZM244.75 114.995V164.795L226.39 154.225L201.03 139.625V89.825L219.39 100.395L244.75 114.995ZM249.12 57.105L292.81 82.265L249.12 107.425L205.43 82.265L249.12 57.105ZM114.49 184.425L96.13 194.995V85.305L121.49 70.705L139.85 60.135V169.815L114.49 184.425ZM91.76 27.425L135.45 52.585L91.76 77.745L48.07 52.585L91.76 27.425ZM43.67 60.135L62.03 70.705L87.39 85.305V202.545V202.555V202.565C87.39 202.735 87.44 202.895 87.46 203.055C87.49 203.265 87.49 203.485 87.55 203.695V203.705C87.6 203.875 87.69 204.035 87.76 204.195C87.84 204.375 87.89 204.575 87.99 204.745C87.99 204.745 87.99 204.755 88 204.755C88.09 204.905 88.22 205.035 88.33 205.175C88.45 205.335 88.55 205.495 88.69 205.635L88.7 205.645C88.82 205.765 88.98 205.855 89.12 205.965C89.28 206.085 89.42 206.225 89.59 206.325C89.6 206.325 89.6 206.325 89.61 206.335C89.62 206.335 89.62 206.345 89.63 206.345L139.87 234.775V285.065L43.67 229.705V60.135ZM244.75 229.705L148.58 285.075V234.775L219.8 194.115L244.75 179.875V229.705ZM297.2 139.625L253.49 164.795V114.995L278.85 100.395L297.21 89.825V139.625H297.2Z"
        />
    </svg>
</template>

```

---

## `resources/js/Components/BrandLogo.vue`

```vue
<script setup>
/**
 * Logo Cabinet Dentaire de l'Obiou — emoji « presse-papier » sur fond teal.
 *
 * Props :
 *  - variant : "full" (emoji + texte) | "mark" (emoji seul)
 *  - size    : "sm" | "md" | "lg" | "xl"
 */
defineProps({
    variant: { type: String, default: 'full' },
    size: { type: String, default: 'md' },
});

const sizes = {
    sm: 'h-9',
    md: 'h-12',
    lg: 'h-16',
    xl: 'h-24',
};

// Tailles d'émoji alignées sur la hauteur du conteneur.
const emojiSizes = {
    sm: 'text-xl',
    md: 'text-2xl',
    lg: 'text-3xl',
    xl: 'text-5xl',
};
</script>

<template>
    <div :class="['inline-flex items-stretch overflow-hidden rounded-lg bg-brand-primary-bg text-white shadow-sm ring-1 ring-white/10', sizes[size] || sizes.md]">
        <!-- Emoji professionnel (cible / objectifs) -->
        <div class="grid aspect-square place-items-center">
            <span :class="['leading-none', emojiSizes[size] || emojiSizes.md]" aria-hidden="true">
                🎯
            </span>
        </div>

        <!-- Texte (partie « full » seulement) -->
        <div v-if="variant === 'full'" class="flex items-center border-l border-white/20 px-3 pr-4">
            <div class="font-bold uppercase leading-tight tracking-wide"
                :class="size === 'sm' ? 'text-[10px]' : (size === 'lg' || size === 'xl') ? 'text-base' : 'text-xs'">
                Cabinet<br />
                Dentaire<br />
                de l'Obiou
            </div>
        </div>
    </div>
</template>

```

---

## `resources/js/Components/Checkbox.vue`

```vue
<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        required: true,
    },
    value: {
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
    />
</template>

```

---

## `resources/js/Components/DangerButton.vue`

```vue
<template>
    <button
        class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 active:bg-red-700 dark:focus:ring-offset-gray-800"
    >
        <slot />
    </button>
</template>

```

---

## `resources/js/Components/DataModal.vue`

```vue
<script setup>
/**
 * Modal générique avec slot body (contenu du formulaire) + titre.
 * Utilise le Modal de Breeze mais ajoute header + padding cohérents.
 */
import Modal from '@/Components/Modal.vue';

defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, required: true },
    maxWidth: { type: String, default: '2xl' },
});
defineEmits(['close']);
</script>

<template>
    <Modal :show="show" :max-width="maxWidth" @close="$emit('close')">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ title }}</h3>
        </div>
        <div class="p-6">
            <slot />
        </div>
    </Modal>
</template>

```

---

## `resources/js/Components/Dropdown.vue`

```vue
<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: '48',
    },
    contentClasses: {
        type: String,
        default: 'py-1 bg-white dark:bg-gray-700',
    },
});

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const widthClass = computed(() => {
    return {
        48: 'w-48',
    }[props.width.toString()];
});

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'ltr:origin-top-left rtl:origin-top-right start-0';
    } else if (props.align === 'right') {
        return 'ltr:origin-top-right rtl:origin-top-left end-0';
    } else {
        return 'origin-top';
    }
});

const open = ref(false);
</script>

<template>
    <div class="relative">
        <div @click="open = !open">
            <slot name="trigger" />
        </div>

        <!-- Full Screen Dropdown Overlay -->
        <div
            v-show="open"
            class="fixed inset-0 z-40"
            @click="open = false"
        ></div>

        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-show="open"
                class="absolute z-50 mt-2 rounded-md shadow-lg"
                :class="[widthClass, alignmentClasses]"
                style="display: none"
                @click="open = false"
            >
                <div
                    class="rounded-md ring-1 ring-black ring-opacity-5"
                    :class="contentClasses"
                >
                    <slot name="content" />
                </div>
            </div>
        </Transition>
    </div>
</template>

```

---

## `resources/js/Components/DropdownLink.vue`

```vue
<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:text-gray-300 dark:hover:bg-gray-800 dark:focus:bg-gray-800"
    >
        <slot />
    </Link>
</template>

```

---

## `resources/js/Components/FlashToast.vue`

```vue
<script setup>
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

/**
 * Affiche un toast éphémère à partir des messages flash (success/error)
 * partagés par HandleInertiaRequests.
 */
const page = usePage();
const visible = ref(false);
const message = ref('');
const type = ref('success');
let timeout = null;

const flash = computed(() => page.props.flash || {});

watch(
    flash,
    (f) => {
        const msg = f.success || f.error;
        if (!msg) return;
        message.value = msg;
        type.value = f.error ? 'error' : 'success';
        visible.value = true;
        clearTimeout(timeout);
        timeout = setTimeout(() => (visible.value = false), 3000);
    },
    { deep: true, immediate: true }
);
</script>

<template>
    <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0 translate-y-2"
        leave-active-class="transition duration-150" leave-to-class="opacity-0 translate-y-2">
        <div v-if="visible"
            :class="[type === 'error' ? 'bg-red-600' : 'bg-emerald-600', 'fixed bottom-6 right-6 z-50 rounded-lg px-5 py-3 text-sm font-medium text-white shadow-lg']">
            {{ message }}
        </div>
    </Transition>
</template>

```

---

## `resources/js/Components/InputError.vue`

```vue
<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-show="message">
        <p class="text-sm text-red-600 dark:text-red-400">
            {{ message }}
        </p>
    </div>
</template>

```

---

## `resources/js/Components/InputLabel.vue`

```vue
<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>

```

---

## `resources/js/Components/Modal.vue`

```vue
<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);
const dialog = ref();
const showSlot = ref(props.show);

watch(
    () => props.show,
    () => {
        if (props.show) {
            document.body.style.overflow = 'hidden';
            showSlot.value = true;

            dialog.value?.showModal();
        } else {
            document.body.style.overflow = '';

            setTimeout(() => {
                dialog.value?.close();
                showSlot.value = false;
            }, 200);
        }
    },
);

const close = () => {
    if (props.closeable) {
        emit('close');
    }
};

const closeOnEscape = (e) => {
    if (e.key === 'Escape') {
        e.preventDefault();

        if (props.show) {
            close();
        }
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);

    document.body.style.overflow = '';
});

const maxWidthClass = computed(() => {
    return {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
    }[props.maxWidth];
});
</script>

<template>
    <dialog
        class="z-50 m-0 min-h-full min-w-full overflow-y-auto bg-transparent backdrop:bg-transparent"
        ref="dialog"
    >
        <div
            class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
            scroll-region
        >
            <Transition
                enter-active-class="ease-out duration-300"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-show="show"
                    class="fixed inset-0 transform transition-all"
                    @click="close"
                >
                    <div
                        class="absolute inset-0 bg-gray-500 opacity-75 dark:bg-gray-900"
                    />
                </div>
            </Transition>

            <Transition
                enter-active-class="ease-out duration-300"
                enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                leave-active-class="ease-in duration-200"
                leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
                <div
                    v-show="show"
                    class="mb-6 transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:mx-auto sm:w-full dark:bg-gray-800"
                    :class="maxWidthClass"
                >
                    <slot v-if="showSlot" />
                </div>
            </Transition>
        </div>
    </dialog>
</template>

```

---

## `resources/js/Components/NavLink.vue`

```vue
<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'inline-flex items-center px-1 pt-1 border-b-2 border-brand-primary text-sm font-semibold leading-5 text-brand-primary dark:text-brand-cream focus:outline-none focus:border-brand-primary-dark transition duration-150 ease-in-out'
        : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-600 dark:text-gray-400 hover:text-brand-primary dark:hover:text-brand-cream hover:border-brand-tan focus:outline-none transition duration-150 ease-in-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>

```

---

## `resources/js/Components/PrimaryButton.vue`

```vue
<template>
    <button
        class="inline-flex items-center rounded-md border border-transparent bg-brand-primary px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-brand-primary-dark focus:bg-brand-primary-dark focus:outline-none focus:ring-2 focus:ring-brand-primary focus:ring-offset-2 active:bg-brand-primary-dark disabled:opacity-50"
    >
        <slot />
    </button>
</template>

```

---

## `resources/js/Components/ResponsiveNavLink.vue`

```vue
<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-indigo-400 dark:border-indigo-600 text-start text-base font-medium text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50 focus:outline-none focus:text-indigo-800 dark:focus:text-indigo-200 focus:bg-indigo-100 dark:focus:bg-indigo-900 focus:border-indigo-700 dark:focus:border-indigo-300 transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>

```

---

## `resources/js/Components/SecondaryButton.vue`

```vue
<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800"
    >
        <slot />
    </button>
</template>

```

---

## `resources/js/Components/StatusBadge.vue`

```vue
<script setup>
defineProps({
    label: { type: String, required: true },
    cls: { type: String, default: 'ok' }, // ok | warn | danger | info
});
const colors = {
    ok: 'bg-brand-mint text-emerald-800 ring-emerald-200/60 dark:bg-emerald-900/40 dark:text-emerald-200 dark:ring-emerald-700/40',
    warn: 'bg-brand-peach text-orange-800 ring-orange-200/60 dark:bg-amber-900/40 dark:text-amber-200 dark:ring-amber-700/40',
    danger: 'bg-brand-rose text-pink-800 ring-pink-200/60 dark:bg-red-900/40 dark:text-red-200 dark:ring-red-700/40',
    info: 'bg-brand-sky text-sky-800 ring-sky-200/60 dark:bg-sky-900/40 dark:text-sky-200 dark:ring-sky-700/40',
};
const dotColors = {
    ok: 'bg-emerald-500',
    warn: 'bg-orange-500 animate-soft-pulse',
    danger: 'bg-pink-500 animate-soft-pulse',
    info: 'bg-sky-500',
};
</script>

<template>
    <span :class="['inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold uppercase tracking-wide ring-1', colors[cls] || colors.ok]">
        <span :class="['h-1.5 w-1.5 rounded-full', dotColors[cls] || dotColors.ok]" />
        {{ label }}
    </span>
</template>

```

---

## `resources/js/Components/TextInput.vue`

```vue
<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
        v-model="model"
        ref="input"
    />
</template>

```

---

# 11. Layouts Vue

## `resources/js/Layouts/AuthenticatedLayout.vue`

```vue
<script setup>
import { ref, computed } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import FlashToast from '@/Components/FlashToast.vue';
import BrandLogo from '@/Components/BrandLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const page = usePage();
const isAdmin = computed(() => page.props.auth.user?.role === 'admin');
</script>

<template>
    <div>
        <div class="min-h-screen">
            <!-- Barre de navigation -->
            <nav class="sticky top-0 z-20 border-b border-brand-beige/70 bg-white/80 backdrop-blur-md shadow-sm dark:border-gray-700 dark:bg-gray-800/80">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')"
                                    class="flex items-center gap-3 transition hover:opacity-90"
                                    aria-label="Cabinet Dentaire de l'Obiou — Tableau de bord">
                                    <BrandLogo variant="mark" size="sm" />
                                    <span class="hidden sm:block">
                                        <span class="block text-sm font-bold uppercase tracking-wide text-gray-900 dark:text-brand-cream">Cabinet Dentaire</span>
                                        <span class="block text-[10px] uppercase tracking-[0.2em] text-brand-primary">de l'Obiou</span>
                                    </span>
                                </Link>
                            </div>

                            <!-- Liens navigation -->
                            <div class="hidden space-x-5 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">Tableau de bord</NavLink>
                                <NavLink :href="route('reviews.index')" :active="route().current('reviews.*')">Entretiens</NavLink>
                                <NavLink v-if="isAdmin" :href="route('templates.index')" :active="route().current('templates.*')">Trames</NavLink>
                                <NavLink v-if="isAdmin" :href="route('team.index')" :active="route().current('team.*')">Équipe</NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button type="button"
                                            class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-600 transition hover:text-brand-primary focus:outline-none dark:bg-gray-800 dark:text-gray-300">
                                            {{ page.props.auth.user.name }}
                                            <svg class="-me-0.5 ms-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </span>
                                </template>
                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">Profil</DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">Déconnexion</DropdownLink>
                                </template>
                            </Dropdown>
                        </div>

                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center rounded-md p-2 text-brand-primary hover:bg-brand-tertiary focus:outline-none">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Menu mobile -->
                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">Tableau de bord</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('reviews.index')" :active="route().current('reviews.*')">Entretiens</ResponsiveNavLink>
                        <ResponsiveNavLink v-if="isAdmin" :href="route('templates.index')" :active="route().current('templates.*')">Trames</ResponsiveNavLink>
                        <ResponsiveNavLink v-if="isAdmin" :href="route('team.index')" :active="route().current('team.*')">Équipe</ResponsiveNavLink>
                    </div>
                    <div class="border-t border-brand-tan/50 pb-1 pt-4 dark:border-gray-600">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800 dark:text-gray-200">{{ page.props.auth.user.name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ page.props.auth.user.email }}</div>
                        </div>
                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">Profil</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">Déconnexion</ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <header v-if="$slots.header" class="border-b border-brand-beige/50 bg-white/70 shadow-sm backdrop-blur dark:border-gray-700 dark:bg-gray-800/70">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main>
                <slot />
            </main>
        </div>
        <FlashToast />
    </div>
</template>

```

---

## `resources/js/Layouts/GuestLayout.vue`

```vue
<script setup>
import BrandLogo from '@/Components/BrandLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-brand-cream px-4 py-10">
        <!-- Taches pastel d'arrière-plan, très douces -->
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-20 -left-20 h-80 w-80 rounded-full bg-brand-mint/50 blur-3xl" />
            <div class="absolute bottom-0 -right-20 h-80 w-80 rounded-full bg-brand-rose/40 blur-3xl" />
            <div class="absolute top-1/3 right-0 h-72 w-72 rounded-full bg-brand-lavender/30 blur-3xl" />
        </div>

        <div class="relative">
            <Link href="/" class="inline-flex transition hover:opacity-90">
                <BrandLogo variant="full" size="lg" />
            </Link>
        </div>

        <div
            class="relative mt-6 w-full overflow-hidden rounded-xl bg-white px-6 py-6 shadow-soft ring-1 ring-brand-beige/60 sm:max-w-md sm:rounded-2xl dark:bg-gray-800"
        >
            <slot />
        </div>

        <p class="relative mt-6 text-xs text-gray-500">
            Entretiens annuels — plateforme interne
        </p>
    </div>
</template>

```

---

# 12. Pages Vue

## `resources/js/Pages/Dashboard.vue`

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: { type: Object, required: true },
    upcoming: { type: Array, default: () => [] },
    myActions: { type: Array, default: () => [] },
    currentYear: { type: Number, required: true },
});

const page = usePage();
const userName = computed(() => page.props.auth.user?.name || '');

const statusColor = (status) => {
    if (status === 'signed') return 'ok';
    if (status === 'completed') return 'info';
    if (status === 'ready_for_manager') return 'warn';
    if (status === 'scheduled') return 'info';
    return 'warn';
};

// Style des cartes "Mes actions" selon le ton (urgent/primary/info)
const actionStyle = (tone) => {
    const styles = {
        urgent: {
            card: 'bg-brand-rose ring-2 ring-pink-300/70',
            icon: 'bg-white text-pink-700',
            badge: 'bg-pink-700 text-white',
            badgeLabel: 'À FAIRE MAINTENANT',
        },
        primary: {
            card: 'bg-brand-peach ring-2 ring-orange-300/70',
            icon: 'bg-white text-orange-700',
            badge: 'bg-orange-700 text-white',
            badgeLabel: 'À FAIRE',
        },
        info: {
            card: 'bg-brand-sky ring-1 ring-sky-300/60',
            icon: 'bg-white text-sky-700',
            badge: 'bg-sky-700 text-white',
            badgeLabel: 'EN COURS',
        },
    };
    return styles[tone] || styles.info;
};

// Icône SVG selon le rôle
const actionIcon = (role) => role === 'manager'
    ? 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
    : 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z';

// Chaque carte a son fond pastel et son icône pour un rendu doux et lisible.
const statCards = computed(() => [
    {
        label: `Entretiens ${props.currentYear}`,
        value: props.stats.total,
        bgCard: 'bg-brand-tertiary',
        bgIcon: 'bg-white/70',
        iconColor: 'text-teal-700',
        icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    },
    {
        label: 'À préparer',
        value: props.stats.to_prepare,
        bgCard: 'bg-brand-peach',
        bgIcon: 'bg-white/70',
        iconColor: 'text-orange-700',
        icon: 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
    },
    {
        label: 'À traiter manager',
        value: props.stats.to_review,
        bgCard: 'bg-brand-lavender',
        bgIcon: 'bg-white/70',
        iconColor: 'text-violet-700',
        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
    },
    {
        label: 'À signer',
        value: props.stats.to_sign,
        bgCard: 'bg-brand-rose',
        bgIcon: 'bg-white/70',
        iconColor: 'text-pink-700',
        icon: 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
    },
    {
        label: `Signés ${props.currentYear}`,
        value: props.stats.signed,
        bgCard: 'bg-brand-mint',
        bgIcon: 'bg-white/70',
        iconColor: 'text-emerald-700',
        icon: 'M5 13l4 4L19 7',
    },
    {
        label: 'Équipe',
        value: props.stats.team,
        bgCard: 'bg-brand-sky',
        bgIcon: 'bg-white/70',
        iconColor: 'text-sky-700',
        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        condition: props.stats.team !== null,
    },
]);

const visibleCards = computed(() => statCards.value.filter((c) => c.condition !== false));
</script>

<template>
    <Head title="Tableau de bord" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-brand-primary">
                    Cabinet dentaire de l'Obiou
                </span>
                <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">
                    Bonjour {{ userName }} 👋
                </h2>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Campagne d'entretiens professionnels annuels et de formations {{ currentYear }}
                </span>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

                <!-- Mes actions à effectuer (très visible, en haut) -->
                <div v-if="myActions.length" class="space-y-3 animate-fade-in">
                    <h3 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                        <span class="grid h-7 w-7 place-items-center rounded-full bg-brand-primary text-white text-sm font-bold">
                            {{ myActions.length }}
                        </span>
                        {{ myActions.length === 1 ? 'Action à effectuer' : 'Actions à effectuer' }}
                    </h3>
                    <Link v-for="a in myActions" :key="`${a.role}-${a.review_id}`"
                        :href="route('reviews.show', a.review_id)"
                        :class="['group block overflow-hidden rounded-2xl p-5 shadow-soft transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg', actionStyle(a.tone).card]">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-start gap-4">
                                <div :class="['grid h-12 w-12 shrink-0 place-items-center rounded-xl shadow-sm', actionStyle(a.tone).icon]">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" :d="actionIcon(a.role)" />
                                    </svg>
                                </div>
                                <div>
                                    <span :class="['inline-block rounded-full px-2 py-0.5 text-[10px] font-bold tracking-wider', actionStyle(a.tone).badge]">
                                        {{ actionStyle(a.tone).badgeLabel }}
                                    </span>
                                    <h4 class="mt-1 text-lg font-bold text-slate-900">{{ a.title }}</h4>
                                    <p class="mt-1 text-sm text-slate-700">{{ a.subtitle }}</p>
                                </div>
                            </div>
                            <div class="shrink-0 self-end sm:self-center">
                                <span class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-brand-primary-dark shadow-sm transition group-hover:bg-brand-primary group-hover:text-white">
                                    {{ a.cta }}
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- État "tout est à jour" si rien à faire -->
                <div v-else class="overflow-hidden rounded-2xl bg-brand-mint p-6 shadow-soft animate-fade-in">
                    <div class="flex items-center gap-4">
                        <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-white text-emerald-700 shadow-sm">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-slate-900">Tout est à jour</h4>
                            <p class="mt-1 text-sm text-slate-700">
                                Vous n'avez aucune action en attente. Bonne journée !
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Bannière pastel -->
                <div class="overflow-hidden rounded-2xl bg-brand-gradient p-6 text-slate-800 shadow-soft animate-fade-in">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                        <div>
                            <h3 class="text-lg font-semibold">Campagne d'entretiens professionnels annuels et de formations {{ currentYear }}</h3>
                            <p class="mt-1 text-sm text-slate-700">
                                {{ stats.total }} entretien{{ stats.total > 1 ? 's' : '' }} au total ·
                                {{ stats.signed }} déjà signé{{ stats.signed > 1 ? 's' : '' }} ·
                                {{ stats.to_sign }} prêt{{ stats.to_sign > 1 ? 's' : '' }} à signer
                            </p>
                        </div>
                        <Link :href="route('reviews.index')"
                            class="inline-flex items-center gap-2 rounded-lg bg-white/70 px-4 py-2 text-sm font-semibold text-brand-primary-dark ring-1 ring-white/60 transition hover:bg-white hover:shadow-md">
                            Aller aux entretiens
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </Link>
                    </div>
                </div>

                <!-- Statistiques (cartes pastel pleines) -->
                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
                    <div v-for="(c, i) in visibleCards" :key="i"
                        :class="['group relative overflow-hidden rounded-xl p-4 shadow-soft transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md', c.bgCard]">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-wider text-slate-700/80">{{ c.label }}</div>
                                <div class="mt-1 text-3xl font-bold text-slate-900">{{ c.value }}</div>
                            </div>
                            <div :class="['rounded-lg p-2', c.bgIcon]">
                                <svg class="h-5 w-5" :class="c.iconColor" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" :d="c.icon" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Prochains entretiens -->
                <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900 dark:text-gray-100">
                            <span class="grid h-8 w-8 place-items-center rounded-lg bg-brand-tertiary text-brand-primary">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </span>
                            Prochains entretiens
                        </h3>
                        <Link :href="route('reviews.index')"
                            class="text-sm font-medium text-brand-primary transition hover:text-brand-primary-dark hover:underline">
                            Voir tout →
                        </Link>
                    </div>
                    <ul v-if="upcoming.length" class="divide-y divide-gray-100 dark:divide-gray-700">
                        <li v-for="r in upcoming" :key="r.id"
                            class="group flex items-center justify-between py-3 text-sm transition hover:bg-brand-tertiary/40 rounded -mx-2 px-2">
                            <div class="flex items-center gap-3">
                                <span class="grid h-9 w-9 place-items-center rounded-full bg-brand-tertiary text-xs font-bold uppercase text-brand-primary">
                                    {{ (r.employee?.name || '—').split(' ').map(w => w[0]).slice(0, 2).join('') }}
                                </span>
                                <div>
                                    <Link :href="route('reviews.show', r.id)"
                                        class="font-medium text-gray-900 group-hover:text-brand-primary dark:text-gray-100">
                                        {{ r.employee?.name }}
                                    </Link>
                                    <div class="text-xs text-gray-500">{{ r.employee?.position }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-500">{{ r.scheduled_for || 'Non planifié' }}</span>
                                <StatusBadge :label="r.status_label" :cls="statusColor(r.status)" />
                            </div>
                        </li>
                    </ul>
                    <div v-else class="py-8 text-center">
                        <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-emerald-100 text-emerald-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm italic text-gray-500">
                            Aucun entretien en cours — tout est à jour
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

```

---

## `resources/js/Pages/Welcome.vue`

```vue
<script setup>
import BrandLogo from '@/Components/BrandLogo.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: { type: Boolean, default: true },
    canRegister: { type: Boolean, default: true },
});
</script>

<template>
    <Head title="Cabinet Dentaire de l'Obiou — Entretiens annuels" />
    <div class="relative min-h-screen overflow-hidden bg-brand-cream">
        <!-- Taches pastel d'arrière-plan -->
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-20 -left-20 h-96 w-96 rounded-full bg-brand-mint/60 blur-3xl" />
            <div class="absolute top-1/3 -right-20 h-96 w-96 rounded-full bg-brand-rose/50 blur-3xl" />
            <div class="absolute bottom-0 left-1/3 h-96 w-96 rounded-full bg-brand-sky/50 blur-3xl" />
            <div class="absolute top-0 right-1/4 h-72 w-72 rounded-full bg-brand-lavender/40 blur-3xl" />
        </div>

        <div class="relative mx-auto flex min-h-screen max-w-5xl flex-col items-center justify-center px-6 py-16">
            <BrandLogo variant="full" size="xl" class="animate-fade-in shadow-xl" />

            <h1 class="mt-8 text-center text-4xl font-bold tracking-tight text-brand-dark sm:text-5xl">
                Entretiens
                <span class="bg-gradient-to-r from-brand-primary to-brand-sky bg-clip-text text-transparent">
                    annuels
                </span>
            </h1>
            <p class="mt-4 max-w-xl text-center text-lg text-slate-600">
                Plateforme interne pour planifier, conduire et signer les entretiens annuels
                de toute l'équipe.
            </p>

            <div class="mt-10 flex flex-wrap justify-center gap-3">
                <Link v-if="canLogin" :href="route('login')"
                    class="inline-flex items-center gap-2 rounded-xl bg-brand-primary px-6 py-3 text-sm font-semibold text-white shadow-md shadow-brand-primary/20 transition hover:-translate-y-0.5 hover:bg-brand-primary-dark hover:shadow-lg">
                    Se connecter
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </Link>
                <Link v-if="canRegister" :href="route('register')"
                    class="inline-flex items-center gap-2 rounded-xl border-2 border-brand-primary bg-white px-6 py-3 text-sm font-semibold text-brand-primary transition hover:-translate-y-0.5 hover:bg-brand-tertiary hover:shadow-md">
                    Créer un compte
                </Link>
            </div>

            <!-- 3 mini-cartes de présentation pastel -->
            <div class="mt-16 grid w-full max-w-4xl grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-xl bg-brand-tertiary p-5 shadow-soft transition hover:-translate-y-1 hover:shadow-md">
                    <div class="grid h-10 w-10 place-items-center rounded-lg bg-white/70 text-teal-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="mt-3 text-sm font-semibold text-slate-900">Planifier</h3>
                    <p class="mt-1 text-xs text-slate-700">
                        Organisez les entretiens de toute l'équipe en quelques clics.
                    </p>
                </div>
                <div class="rounded-xl bg-brand-lavender p-5 shadow-soft transition hover:-translate-y-1 hover:shadow-md">
                    <div class="grid h-10 w-10 place-items-center rounded-lg bg-white/70 text-violet-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <h3 class="mt-3 text-sm font-semibold text-slate-900">Conduire</h3>
                    <p class="mt-1 text-xs text-slate-700">
                        Chaque salarié prépare, le manager complète, la synthèse se fait à l'écran.
                    </p>
                </div>
                <div class="rounded-xl bg-brand-mint p-5 shadow-soft transition hover:-translate-y-1 hover:shadow-md">
                    <div class="grid h-10 w-10 place-items-center rounded-lg bg-white/70 text-emerald-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="mt-3 text-sm font-semibold text-slate-900">Signer</h3>
                    <p class="mt-1 text-xs text-slate-700">
                        Signature électronique par le salarié et le manager, archivage sécurisé.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

```

---

## `resources/js/Pages/Auth/ForcePasswordChange.vue`

```vue
<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const userName = page.props.auth.user?.name || '';

const form = useForm({
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.force-change.update'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Changer votre mot de passe" />

        <div class="mb-4 text-center">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                Bienvenue, {{ userName }}
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Pour votre sécurité, veuillez choisir un mot de passe personnel avant d'accéder
                à l'application. Votre mot de passe temporaire ne sera plus valide.
            </p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="password" value="Nouveau mot de passe" />
                <TextInput id="password" type="password" class="mt-1 block w-full"
                    v-model="form.password" required autofocus autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirmer le mot de passe" />
                <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                    v-model="form.password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="mt-6">
                <PrimaryButton class="w-full justify-center"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing">
                    Enregistrer et accéder à l'application
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

```

---

## `resources/js/Pages/Auth/Register.vue`

```vue
<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    positions: { type: Array, default: () => [] },
});

const form = useForm({
    name: '',
    email: '',
    position: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Créer un compte" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Nom complet" />
                <TextInput id="name" type="text" class="mt-1 block w-full"
                    v-model="form.name" required autofocus autocomplete="name" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email professionnel" />
                <TextInput id="email" type="email" class="mt-1 block w-full"
                    v-model="form.email" required autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="position" value="Poste" />
                <select id="position" v-model="form.position" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="" disabled>— choisir votre poste —</option>
                    <option v-for="p in positions" :key="p" :value="p">{{ p }}</option>
                </select>
                <InputError class="mt-2" :message="form.errors.position" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Mot de passe" />
                <TextInput id="password" type="password" class="mt-1 block w-full"
                    v-model="form.password" required autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirmer le mot de passe" />
                <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                    v-model="form.password_confirmation" required autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900">
                    Déjà inscrit ?
                </Link>
                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing">
                    Créer mon compte
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

```

---

## `resources/js/Pages/Auth/Login.vue`

```vue
<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400"
                        >Remember me</span
                    >
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

```

---

## `resources/js/Pages/Reviews/Index.vue`

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    reviews: { type: Array, default: () => [] },
    employees: { type: Array, default: () => [] },
    potentialManagers: { type: Array, default: () => [] },
    defaultYear: { type: Number, required: true },
    can: { type: Object, default: () => ({ create: false, pickManager: false }) },
});

const page = usePage();

const blankRow = () => ({ employee_id: '', manager_id: '', co_manager_id: '', scheduled_for: '' });

const form = useForm({
    year: props.defaultYear,
    assignments: [blankRow()],
});

const showForm = ref(false);

const addRow = () => form.assignments.push(blankRow());
const removeRow = (i) => {
    if (form.assignments.length > 1) form.assignments.splice(i, 1);
};

// Les salariés déjà sélectionnés dans le formulaire — pour éviter les doublons
// dans les autres dropdowns (mais on laisse voir tous les choix).
const submit = () => {
    form.post(route('reviews.store'), {
        onSuccess: () => {
            form.reset();
            form.year = props.defaultYear;
            form.assignments = [blankRow()];
            showForm.value = false;
        },
    });
};

const statusColor = (status) => {
    if (status === 'signed') return 'ok';
    if (status === 'completed') return 'info';
    if (status === 'ready_for_manager') return 'warn';
    if (status === 'scheduled') return 'info';
    return 'warn';
};

const destroy = (id) => {
    if (!confirm('Supprimer cet entretien ?')) return;
    useForm({}).post(route('reviews.destroy', id));
};

// Réprogrammer (changer la date d'un entretien déjà planifié)
const rescheduleId = ref(null);
const rescheduleDate = ref('');
const rescheduleForm = useForm({ scheduled_for: '' });
const openReschedule = (r) => {
    rescheduleId.value = r.id;
    rescheduleDate.value = r.scheduled_for || '';
};
const submitReschedule = () => {
    rescheduleForm.scheduled_for = rescheduleDate.value;
    rescheduleForm.post(route('reviews.reschedule', rescheduleId.value), {
        preserveScroll: true,
        onSuccess: () => { rescheduleId.value = null; },
    });
};
const cancelReschedule = () => { rescheduleId.value = null; };
</script>

<template>
    <Head title="Entretiens annuels" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Entretiens annuels
                </h2>
                <PrimaryButton v-if="can.create" @click="showForm = !showForm">
                    {{ showForm ? 'Annuler' : 'Planifier des entretiens' }}
                </PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- Formulaire planification (multi-lignes) -->
                <div v-if="showForm && can.create" class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                    <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-gray-100">
                        Planifier un ou plusieurs entretiens
                    </h3>

                    <form @submit.prevent="submit">
                        <!-- Année commune -->
                        <div class="mb-4 max-w-xs">
                            <InputLabel for="year" value="Année" />
                            <TextInput id="year" v-model="form.year" type="number" min="2000" max="2100"
                                class="mt-1 block w-full" required />
                            <InputError class="mt-2" :message="form.errors.year" />
                        </div>

                        <!-- Lignes d'assignation -->
                        <div class="space-y-3">
                            <div v-for="(row, i) in form.assignments" :key="i"
                                class="grid grid-cols-1 gap-3 rounded border border-gray-200 p-3 sm:grid-cols-12 dark:border-gray-700">
                                <!-- Salarié -->
                                <div :class="can.pickManager ? 'sm:col-span-3' : 'sm:col-span-7'">
                                    <InputLabel :for="`employee_${i}`" value="Salarié" />
                                    <select :id="`employee_${i}`" v-model="row.employee_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        <option value="" disabled>— choisir —</option>
                                        <option v-for="e in employees" :key="e.id" :value="e.id">
                                            {{ e.name }}<span v-if="e.position"> — {{ e.position }}</span>
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors[`assignments.${i}.employee_id`]" />
                                </div>

                                <!-- Qui réalise l'entretien (admin seulement) -->
                                <div v-if="can.pickManager" class="sm:col-span-3">
                                    <InputLabel :for="`manager_${i}`" value="Qui réalise l'entretien" />
                                    <select :id="`manager_${i}`" v-model="row.manager_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        <option value="">— moi-même —</option>
                                        <option v-for="m in potentialManagers" :key="m.id" :value="m.id"
                                            :disabled="m.id === row.employee_id">
                                            {{ m.name }}<span v-if="m.position"> — {{ m.position }}</span>
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors[`assignments.${i}.manager_id`]" />
                                </div>

                                <!-- Qui assiste à l'entretien (admin seulement, optionnel) -->
                                <div v-if="can.pickManager" class="sm:col-span-3">
                                    <InputLabel :for="`co_manager_${i}`" value="Qui assiste à l'entretien" />
                                    <select :id="`co_manager_${i}`" v-model="row.co_manager_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        <option value="">— personne —</option>
                                        <option v-for="m in potentialManagers" :key="m.id" :value="m.id"
                                            :disabled="m.id === row.employee_id || m.id === row.manager_id">
                                            {{ m.name }}<span v-if="m.position"> — {{ m.position }}</span>
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors[`assignments.${i}.co_manager_id`]" />
                                </div>

                                <!-- Date -->
                                <div class="sm:col-span-2">
                                    <InputLabel :for="`date_${i}`" value="Date prévue" />
                                    <TextInput :id="`date_${i}`" v-model="row.scheduled_for" type="date"
                                        class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="form.errors[`assignments.${i}.scheduled_for`]" />
                                </div>

                                <!-- Retirer la ligne -->
                                <div class="sm:col-span-1 flex items-end">
                                    <button type="button"
                                        class="w-full rounded border border-red-200 px-2 py-2 text-xs text-red-600 hover:bg-red-50 disabled:opacity-40"
                                        :disabled="form.assignments.length === 1"
                                        @click="removeRow(i)">
                                        Retirer
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <SecondaryButton type="button" @click="addRow">
                                + Ajouter une personne
                            </SecondaryButton>
                        </div>

                        <p v-if="!employees.length" class="mt-3 text-xs text-gray-500">
                            Aucun salarié enregistré. Ajoutez d'abord les membres de l'équipe dans l'onglet
                            « Équipe ».
                        </p>

                        <div class="mt-5 flex justify-end gap-2">
                            <SecondaryButton type="button" @click="showForm = false">Annuler</SecondaryButton>
                            <PrimaryButton :disabled="form.processing">
                                Planifier ({{ form.assignments.length }})
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- Liste -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                <th class="px-4 py-3">Année</th>
                                <th class="px-4 py-3">Salarié</th>
                                <th class="px-4 py-3">Qui réalise l'entretien</th>
                                <th class="px-4 py-3">Qui assiste</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Statut</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="r in reviews" :key="r.id" class="text-sm text-gray-700 dark:text-gray-200">
                                <td class="px-4 py-3 font-medium">{{ r.year }}</td>
                                <td class="px-4 py-3">
                                    <div>{{ r.employee?.name }}</div>
                                    <div class="text-xs text-gray-500">{{ r.employee?.position }}</div>
                                </td>
                                <td class="px-4 py-3">{{ r.manager?.name || '—' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ r.co_manager?.name || '—' }}</td>
                                <td class="px-4 py-3">
                                    <div v-if="rescheduleId === r.id" class="flex items-center gap-1">
                                        <TextInput v-model="rescheduleDate" type="date" class="w-36 text-sm" />
                                        <button class="text-xs text-brand-primary hover:underline" @click="submitReschedule">OK</button>
                                        <button class="text-xs text-gray-500 hover:underline" @click="cancelReschedule">✕</button>
                                    </div>
                                    <button v-else-if="can.delete && !r.signed"
                                        class="underline decoration-dotted hover:text-brand-primary"
                                        @click="openReschedule(r)">
                                        {{ r.scheduled_for || 'Définir' }}
                                    </button>
                                    <span v-else>{{ r.scheduled_for || '—' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <StatusBadge :label="r.status_label" :cls="statusColor(r.status)" />
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <Link :href="route('reviews.show', r.id)"
                                        class="text-brand-primary hover:underline">Ouvrir</Link>
                                    <a :href="route('reviews.pdf', r.id)"
                                        class="text-brand-primary hover:underline">PDF</a>
                                    <button v-if="can.delete && !r.signed"
                                        class="text-red-600 hover:underline" @click="destroy(r.id)">
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!reviews.length">
                                <td colspan="7" class="px-4 py-10 text-center text-sm italic text-gray-500">
                                    Aucun entretien pour le moment.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

```

---

## `resources/js/Pages/Reviews/Show.vue`

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    review: { type: Object, required: true },
    template: { type: Object, required: true },
});

const page = usePage();
const currentUser = computed(() => page.props.auth.user);

const isEmployee = computed(() => props.review.employee?.id === currentUser.value.id);
const isManagerOfReview = computed(
    () => props.review.manager?.id === currentUser.value.id
        || currentUser.value.role === 'admin',
);

const employeeEditable = computed(
    () => isEmployee.value
        && ['scheduled', 'employee_draft'].includes(props.review.status),
);
const managerEditable = computed(
    () => isManagerOfReview.value
        && ['ready_for_manager', 'manager_draft', 'completed'].includes(props.review.status),
);

// Deux formulaires : un pour la partie salarié, un pour la partie manager.
const employeeForm = useForm({
    header: { ...(props.review.header || {}) },
    answers: { ...(props.review.employee_answers || {}) },
    do_submit: false,
});
const managerForm = useForm({
    header: { ...(props.review.header || {}) },
    answers: { ...(props.review.manager_answers || {}) },
    finalize: false,
});

// Renvoie le bon "sac" de réponses selon le propriétaire et le mode (édition ou lecture)
const readAnswers = (owner) => {
    if (owner === 'manager') {
        return managerEditable.value ? managerForm.answers : (props.review.manager_answers || {});
    }
    return employeeEditable.value ? employeeForm.answers : (props.review.employee_answers || {});
};

const readHeader = (owner) => {
    if (owner === 'manager') {
        return managerEditable.value ? managerForm.header : (props.review.header || {});
    }
    return employeeEditable.value ? employeeForm.header : (props.review.header || {});
};

const canEditField = (owner) => {
    if (owner === 'employee') return employeeEditable.value;
    if (owner === 'manager') return managerEditable.value;
    return false;
};

// --- Actions ligne (tableaux dynamiques) ---
const addRow = (owner, key, rowTemplate) => {
    const bag = readAnswers(owner);
    if (!Array.isArray(bag[key])) bag[key] = [];
    bag[key].push({ ...rowTemplate });
};
const removeRow = (owner, key, index) => {
    const bag = readAnswers(owner);
    if (Array.isArray(bag[key])) bag[key].splice(index, 1);
};

// --- Grille de compétences : accès cellules ---
const getGridCell = (owner, key, rowIndex, cellKey = null) => {
    const bag = readAnswers(owner);
    const arr = Array.isArray(bag[key]) ? bag[key] : [];
    const cell = arr[rowIndex];
    if (cell === undefined || cell === null) return cellKey ? '' : '';
    if (cellKey) return typeof cell === 'object' ? (cell[cellKey] ?? '') : '';
    return cell;
};
const setGridCell = (owner, key, rowIndex, value, cellKey = null) => {
    const bag = readAnswers(owner);
    if (!Array.isArray(bag[key])) bag[key] = [];
    while (bag[key].length <= rowIndex) bag[key].push(cellKey ? {} : '');
    if (cellKey) {
        if (typeof bag[key][rowIndex] !== 'object' || bag[key][rowIndex] === null) {
            bag[key][rowIndex] = {};
        }
        bag[key][rowIndex][cellKey] = value;
    } else {
        bag[key][rowIndex] = value;
    }
};

// --- Sauvegarde (routes en POST — cf. routes/web.php) ---
const saveEmployee = (doSubmit = false) => {
    employeeForm.do_submit = doSubmit;
    employeeForm.post(route('reviews.employee.update', props.review.id), {
        preserveScroll: true,
        onFinish: () => { employeeForm.do_submit = false; },
    });
};
const saveManager = (finalize = false) => {
    managerForm.finalize = finalize;
    managerForm.post(route('reviews.manager.update', props.review.id), {
        preserveScroll: true,
        onFinish: () => { managerForm.finalize = false; },
    });
};
const sign = () => {
    if (!confirm('Confirmer la signature de cet entretien ?')) return;
    router.post(route('reviews.sign', props.review.id), {}, { preserveScroll: true });
};

// --- Auto-save toutes les 2 minutes + protection fermeture ---
const lastAutoSave = ref(null);
let autoSaveTimer = null;

const autoSave = () => {
    if (employeeEditable.value && employeeForm.isDirty) {
        saveEmployee(false);
        lastAutoSave.value = new Date();
    }
    if (managerEditable.value && managerForm.isDirty) {
        saveManager(false);
        lastAutoSave.value = new Date();
    }
};

const beforeUnloadHandler = (e) => {
    if ((employeeEditable.value && employeeForm.isDirty)
        || (managerEditable.value && managerForm.isDirty)) {
        e.preventDefault();
        e.returnValue = '';
    }
};

onMounted(() => {
    autoSaveTimer = setInterval(autoSave, 120000); // 2 min
    window.addEventListener('beforeunload', beforeUnloadHandler);
});

onUnmounted(() => {
    if (autoSaveTimer) clearInterval(autoSaveTimer);
    window.removeEventListener('beforeunload', beforeUnloadHandler);
});

const statusColor = (status) => {
    if (status === 'signed') return 'ok';
    if (status === 'completed') return 'info';
    if (status === 'ready_for_manager') return 'warn';
    if (status === 'scheduled') return 'info';
    return 'warn';
};

const canEmployeeSign = computed(
    () => isEmployee.value
        && ['completed', 'signed'].includes(props.review.status)
        && !props.review.employee_signed_at,
);
const canManagerSign = computed(
    () => isManagerOfReview.value
        && ['completed', 'signed'].includes(props.review.status)
        && !props.review.manager_signed_at,
);

// classe réutilisée pour les inputs
const inputCls = 'block w-full rounded border-gray-300 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary disabled:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:disabled:bg-gray-900/50';
</script>

<template>
    <Head :title="`Entretien ${review.year} — ${review.employee?.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-start justify-between">
                <div>
                    <Link :href="route('reviews.index')" class="text-sm text-brand-primary hover:underline">
                        ← Liste des entretiens
                    </Link>
                    <h2 class="mt-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        Entretien annuel {{ review.year }} — {{ review.employee?.name }}
                    </h2>
                </div>
                <div class="flex items-center gap-3">
                    <a :href="route('reviews.pdf', review.id)"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-brand-primary/40 bg-white px-3 py-1.5 text-xs font-semibold text-brand-primary shadow-sm transition hover:bg-brand-tertiary">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Télécharger PDF
                    </a>
                    <a :href="route('reviews.print', review.id)" target="_blank" rel="noopener"
                        class="text-xs text-gray-500 hover:text-brand-primary hover:underline">
                        Aperçu HTML
                    </a>
                    <StatusBadge :label="review.status_label" :cls="statusColor(review.status)" />
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- Entête -->
                <section class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-brand-primary">
                            {{ template.label }} — Entretien annuel {{ review.year }}
                        </h3>
                        <p class="text-xs text-gray-500">Trame : {{ template.key }}</p>
                    </div>
                    <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                        <div><span class="font-medium">Nom :</span> {{ review.employee?.name }}</div>
                        <div><span class="font-medium">Poste :</span> {{ review.employee?.position || '—' }}</div>
                        <div><span class="font-medium">Date d'embauche :</span> {{ review.employee?.hired_on || '—' }}</div>
                        <div><span class="font-medium">Date d'entretien :</span> {{ review.scheduled_for || '—' }}</div>
                        <div><span class="font-medium">Qui réalise l'entretien :</span> {{ review.manager?.name || '—' }}</div>
                        <div v-if="review.co_manager"><span class="font-medium">Qui assiste :</span> {{ review.co_manager?.name }}</div>
                    </div>

                    <div v-if="template.header?.length" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div v-for="h in template.header" :key="h.key">
                            <InputLabel :value="h.label" />
                            <input type="text"
                                :value="readHeader(h.owner)[h.key]"
                                @input="(e) => { readHeader(h.owner)[h.key] = e.target.value; }"
                                :disabled="!canEditField(h.owner)"
                                :class="['mt-1', inputCls]" />
                            <p v-if="canEditField(h.owner)" class="mt-1 text-[10px] italic text-gray-500">
                                Champ {{ h.owner === 'manager' ? 'manager' : 'salarié' }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Sections de la trame -->
                <section v-for="(sec, si) in template.sections" :key="si"
                    class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <h3 class="mb-4 border-b border-brand-primary/40 pb-2 text-base font-bold text-brand-primary">
                        {{ sec.title }}
                    </h3>
                    <div class="space-y-5">
                        <div v-for="field in sec.fields" :key="field.key">

                            <!-- Échelle 1 à 10 -->
                            <template v-if="field.type === 'scale_10'">
                                <InputLabel :value="field.question" />
                                <div class="mt-2 flex flex-wrap gap-1">
                                    <button v-for="n in 10" :key="n" type="button"
                                        :disabled="!canEditField(field.owner)"
                                        @click="readAnswers(field.owner)[field.key] = n"
                                        :class="[
                                            'h-10 w-10 rounded border text-sm font-semibold transition',
                                            readAnswers(field.owner)[field.key] === n
                                                ? 'border-amber-500 bg-amber-300 text-gray-900'
                                                : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50',
                                            !canEditField(field.owner) ? 'cursor-not-allowed opacity-80' : 'cursor-pointer',
                                        ]">
                                        {{ n }}
                                    </button>
                                </div>
                            </template>

                            <!-- Zone de texte -->
                            <template v-else-if="field.type === 'textarea'">
                                <InputLabel :value="field.question" />
                                <textarea rows="3"
                                    :value="readAnswers(field.owner)[field.key]"
                                    @input="(e) => { readAnswers(field.owner)[field.key] = e.target.value; }"
                                    :disabled="!canEditField(field.owner)"
                                    :class="['mt-1', inputCls]" />
                            </template>

                            <!-- Champ texte court -->
                            <template v-else-if="field.type === 'text'">
                                <InputLabel :value="field.question" />
                                <input type="text"
                                    :value="readAnswers(field.owner)[field.key]"
                                    @input="(e) => { readAnswers(field.owner)[field.key] = e.target.value; }"
                                    :disabled="!canEditField(field.owner)"
                                    :class="['mt-1', inputCls]" />
                            </template>

                            <!-- Choix unique (pastilles) -->
                            <template v-else-if="field.type === 'choice'">
                                <InputLabel :value="field.question" />
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <button v-for="opt in field.options" :key="opt" type="button"
                                        :disabled="!canEditField(field.owner)"
                                        @click="readAnswers(field.owner)[field.key] = opt"
                                        :class="[
                                            'rounded-full border px-3 py-1 text-sm transition',
                                            readAnswers(field.owner)[field.key] === opt
                                                ? 'border-brand-primary bg-brand-primary text-white'
                                                : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50',
                                            !canEditField(field.owner) ? 'cursor-not-allowed opacity-80' : 'cursor-pointer',
                                        ]">
                                        {{ opt }}
                                    </button>
                                </div>
                            </template>

                            <!-- Bilan des objectifs -->
                            <template v-else-if="field.type === 'objectives_review'">
                                <div class="flex items-center justify-between">
                                    <InputLabel :value="field.question" />
                                    <button v-if="canEditField('manager')" type="button"
                                        class="text-xs text-brand-primary hover:underline"
                                        @click="addRow('manager', field.key, { objectif: '', evaluation: '' })">
                                        + Ajouter un objectif
                                    </button>
                                </div>
                                <p v-if="field.hint" class="text-xs italic text-gray-500">{{ field.hint }}</p>
                                <table class="mt-2 w-full border-collapse text-sm">
                                    <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                                        <tr>
                                            <th class="p-2">Objectif fixé</th>
                                            <th class="w-52 p-2">Évaluation manager</th>
                                            <th v-if="canEditField('manager')" class="w-8 p-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(row, i) in (readAnswers('manager')[field.key] || [])" :key="i"
                                            class="border-t border-gray-100">
                                            <td class="p-1">
                                                <input type="text" v-model="readAnswers('manager')[field.key][i].objectif"
                                                    :disabled="!canEditField('manager')" :class="inputCls" />
                                            </td>
                                            <td class="p-1">
                                                <select v-model="readAnswers('manager')[field.key][i].evaluation"
                                                    :disabled="!canEditField('manager')" :class="inputCls">
                                                    <option value="">—</option>
                                                    <option v-for="opt in field.evaluation_options" :key="opt" :value="opt">
                                                        {{ opt }}
                                                    </option>
                                                </select>
                                            </td>
                                            <td v-if="canEditField('manager')" class="p-1 text-center">
                                                <button type="button" class="text-xs text-red-600 hover:underline"
                                                    @click="removeRow('manager', field.key, i)">✕</button>
                                            </td>
                                        </tr>
                                        <tr v-if="!(readAnswers('manager')[field.key] || []).length">
                                            <td colspan="3" class="p-3 text-center text-xs italic text-gray-500">
                                                Aucun objectif enregistré
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </template>

                            <!-- Activités / réussites / difficultés -->
                            <template v-else-if="field.type === 'activities_table'">
                                <div class="flex items-center justify-between">
                                    <InputLabel :value="field.question" />
                                    <button v-if="canEditField('employee')" type="button"
                                        class="text-xs text-brand-primary hover:underline"
                                        @click="addRow('employee', field.key, { realisations: '', reussites: '', difficultes: '' })">
                                        + Ajouter
                                    </button>
                                </div>
                                <table class="mt-2 w-full border-collapse text-sm">
                                    <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                                        <tr>
                                            <th class="p-2">Réalisations</th>
                                            <th class="p-2">Réussites</th>
                                            <th class="p-2">Difficultés</th>
                                            <th v-if="canEditField('employee')" class="w-8 p-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(row, i) in (readAnswers('employee')[field.key] || [])" :key="i"
                                            class="border-t border-gray-100">
                                            <td class="p-1">
                                                <input type="text" v-model="readAnswers('employee')[field.key][i].realisations"
                                                    :disabled="!canEditField('employee')" :class="inputCls" />
                                            </td>
                                            <td class="p-1">
                                                <input type="text" v-model="readAnswers('employee')[field.key][i].reussites"
                                                    :disabled="!canEditField('employee')" :class="inputCls" />
                                            </td>
                                            <td class="p-1">
                                                <input type="text" v-model="readAnswers('employee')[field.key][i].difficultes"
                                                    :disabled="!canEditField('employee')" :class="inputCls" />
                                            </td>
                                            <td v-if="canEditField('employee')" class="p-1 text-center">
                                                <button type="button" class="text-xs text-red-600 hover:underline"
                                                    @click="removeRow('employee', field.key, i)">✕</button>
                                            </td>
                                        </tr>
                                        <tr v-if="!(readAnswers('employee')[field.key] || []).length">
                                            <td colspan="4" class="p-3 text-center text-xs italic text-gray-500">
                                                Aucune activité enregistrée
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </template>

                            <!-- Nouveaux objectifs -->
                            <template v-else-if="field.type === 'objectives_plan'">
                                <div class="flex items-center justify-between">
                                    <InputLabel :value="field.question" />
                                    <button v-if="canEditField('manager')" type="button"
                                        class="text-xs text-brand-primary hover:underline"
                                        @click="addRow('manager', field.key, { objectif: '', indicateurs: '', moyens: '', delais: '' })">
                                        + Ajouter un objectif
                                    </button>
                                </div>
                                <table class="mt-2 w-full border-collapse text-sm">
                                    <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                                        <tr>
                                            <th class="p-2">Objectif</th>
                                            <th class="p-2">Indicateurs</th>
                                            <th class="p-2">Moyens</th>
                                            <th class="w-32 p-2">Délais</th>
                                            <th v-if="canEditField('manager')" class="w-8 p-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(row, i) in (readAnswers('manager')[field.key] || [])" :key="i"
                                            class="border-t border-gray-100">
                                            <td class="p-1">
                                                <input type="text" v-model="readAnswers('manager')[field.key][i].objectif"
                                                    :disabled="!canEditField('manager')" :class="inputCls" />
                                            </td>
                                            <td class="p-1">
                                                <input type="text" v-model="readAnswers('manager')[field.key][i].indicateurs"
                                                    :disabled="!canEditField('manager')" :class="inputCls" />
                                            </td>
                                            <td class="p-1">
                                                <input type="text" v-model="readAnswers('manager')[field.key][i].moyens"
                                                    :disabled="!canEditField('manager')" :class="inputCls" />
                                            </td>
                                            <td class="p-1">
                                                <input type="text" v-model="readAnswers('manager')[field.key][i].delais"
                                                    :disabled="!canEditField('manager')" :class="inputCls" />
                                            </td>
                                            <td v-if="canEditField('manager')" class="p-1 text-center">
                                                <button type="button" class="text-xs text-red-600 hover:underline"
                                                    @click="removeRow('manager', field.key, i)">✕</button>
                                            </td>
                                        </tr>
                                        <tr v-if="!(readAnswers('manager')[field.key] || []).length">
                                            <td colspan="5" class="p-3 text-center text-xs italic text-gray-500">
                                                Aucun objectif enregistré
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </template>

                            <!-- Grille de compétences -->
                            <template v-else-if="field.type === 'competency_grid'">
                                <InputLabel :value="field.question" />
                                <p v-if="field.hint" class="mb-2 text-xs italic text-gray-500">{{ field.hint }}</p>
                                <table class="w-full border-collapse text-sm">
                                    <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                                        <tr>
                                            <th class="p-2">Compétence attendue</th>
                                            <th class="w-44 p-2">Auto-évaluation (salarié)</th>
                                            <th class="p-2">Commentaires manager</th>
                                            <th class="p-2">Actions à mener</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(rowLabel, i) in field.rows" :key="i"
                                            class="border-t border-gray-100 align-top">
                                            <td class="p-2 text-xs text-gray-700 dark:text-gray-200">{{ rowLabel }}</td>
                                            <td class="p-1">
                                                <select :value="getGridCell('employee', field.key, i)"
                                                    @change="(e) => setGridCell('employee', field.key, i, e.target.value)"
                                                    :disabled="!canEditField('employee')" :class="inputCls">
                                                    <option value="">—</option>
                                                    <option v-for="opt in field.evaluation_options" :key="opt" :value="opt">
                                                        {{ opt }}
                                                    </option>
                                                </select>
                                            </td>
                                            <td class="p-1">
                                                <input type="text"
                                                    :value="getGridCell('manager', field.key, i, 'comment')"
                                                    @input="(e) => setGridCell('manager', field.key, i, e.target.value, 'comment')"
                                                    :disabled="!canEditField('manager')" :class="inputCls" />
                                            </td>
                                            <td class="p-1">
                                                <input type="text"
                                                    :value="getGridCell('manager', field.key, i, 'action')"
                                                    @input="(e) => setGridCell('manager', field.key, i, e.target.value, 'action')"
                                                    :disabled="!canEditField('manager')" :class="inputCls" />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </template>

                            <!-- Annotation du manager sur les réponses du salarié -->
                            <div v-if="field.owner === 'employee' && isManagerOfReview"
                                class="mt-2 rounded-lg border-l-4 border-brand-lavender bg-brand-lavender/20 p-3">
                                <label class="flex items-center gap-1.5 text-xs font-semibold text-violet-700">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                    </svg>
                                    Annotation
                                </label>
                                <textarea rows="2"
                                    :value="readAnswers('manager')['_note_' + field.key] || ''"
                                    @input="(e) => { readAnswers('manager')['_note_' + field.key] = e.target.value; }"
                                    :disabled="!managerEditable"
                                    placeholder="Ajoutez une note pour préparer l'entretien…"
                                    :class="['mt-1 text-sm', inputCls]" />
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Boutons salarié -->
                <div v-if="employeeEditable" class="rounded-lg border border-amber-300 bg-amber-50 p-4">
                    <p class="mb-3 text-sm text-amber-800">
                        Vous êtes en train de préparer votre auto-évaluation.
                        Une fois « Envoyer » cliqué, vous ne pourrez plus modifier vos réponses.
                    </p>
                    <p class="mb-3 text-xs text-amber-700 italic">
                        Sauvegarde automatique toutes les 2 minutes.
                        <span v-if="lastAutoSave"> Dernière sauvegarde : {{ lastAutoSave.toLocaleTimeString('fr-FR') }}</span>
                    </p>
                    <div class="flex flex-wrap justify-end gap-2">
                        <SecondaryButton :disabled="employeeForm.processing" @click="saveEmployee(false)">
                            Enregistrer brouillon
                        </SecondaryButton>
                        <PrimaryButton :disabled="employeeForm.processing" @click="saveEmployee(true)">
                            Envoyer
                        </PrimaryButton>
                    </div>
                </div>

                <!-- Boutons manager -->
                <div v-if="managerEditable" class="rounded-lg border border-indigo-300 bg-indigo-50 p-4">
                    <p class="mb-3 text-sm text-indigo-800">
                        Complétez votre partie. Quand l'entretien est prêt pour signature, cliquez sur « Finaliser ».
                    </p>
                    <p class="mb-3 text-xs text-indigo-700 italic">
                        Sauvegarde automatique toutes les 2 minutes.
                        <span v-if="lastAutoSave"> Dernière sauvegarde : {{ lastAutoSave.toLocaleTimeString('fr-FR') }}</span>
                    </p>
                    <div class="flex flex-wrap justify-end gap-2">
                        <SecondaryButton :disabled="managerForm.processing" @click="saveManager(false)">
                            Enregistrer brouillon
                        </SecondaryButton>
                        <PrimaryButton :disabled="managerForm.processing" @click="saveManager(true)">
                            Finaliser pour signature
                        </PrimaryButton>
                    </div>
                </div>

                <!-- Signatures -->
                <section class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <h3 class="mb-4 border-b border-brand-primary/40 pb-2 text-base font-bold text-brand-primary">
                        Signatures
                    </h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded border border-gray-200 p-4 dark:border-gray-700">
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-200">Salarié</div>
                            <div class="mt-1 text-xs text-gray-500">{{ review.employee?.name }}</div>
                            <div v-if="review.employee_signed_at" class="mt-2 text-sm text-emerald-600">
                                ✓ Signé le {{ new Date(review.employee_signed_at).toLocaleString('fr-FR') }}
                            </div>
                            <PrimaryButton v-else-if="canEmployeeSign" class="mt-3" @click="sign">
                                Signer électroniquement
                            </PrimaryButton>
                            <div v-else class="mt-2 text-xs italic text-gray-500">
                                En attente de la finalisation manager.
                            </div>
                        </div>
                        <div class="rounded border border-gray-200 p-4 dark:border-gray-700">
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-200">Manager</div>
                            <div class="mt-1 text-xs text-gray-500">{{ review.manager?.name || '—' }}</div>
                            <div v-if="review.manager_signed_at" class="mt-2 text-sm text-emerald-600">
                                ✓ Signé le {{ new Date(review.manager_signed_at).toLocaleString('fr-FR') }}
                            </div>
                            <PrimaryButton v-else-if="canManagerSign" class="mt-3" @click="sign">
                                Signer électroniquement
                            </PrimaryButton>
                            <div v-else class="mt-2 text-xs italic text-gray-500">
                                En attente de finalisation.
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

```

---

## `resources/js/Pages/Team/Index.vue`

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    users: { type: Array, default: () => [] },
    managers: { type: Array, default: () => [] },
    positions: { type: Array, default: () => [] },
    roles: { type: Array, default: () => [] },
});

const showingModal = ref(false);
const editingUser = ref(null);

const form = useForm({
    name: '',
    email: '',
    role: 'employee',
    position: '',
    department: '',
    manager_id: '',
    hired_on: '',
    password: '',
    password_confirmation: '',
});

const openCreate = () => {
    editingUser.value = null;
    form.reset();
    form.role = 'employee';
    showingModal.value = true;
};

const openEdit = (user) => {
    editingUser.value = user;
    form.name = user.name;
    form.email = user.email;
    form.role = user.role;
    form.position = user.position || '';
    form.department = user.department || '';
    form.manager_id = user.manager_id || '';
    form.hired_on = user.hired_on || '';
    form.password = '';
    form.password_confirmation = '';
    showingModal.value = true;
};

const submit = () => {
    // Toutes les mutations en POST (le proxy Render bloque PUT/DELETE).
    const url = editingUser.value
        ? route('team.update', editingUser.value.id)
        : route('team.store');
    form.post(url, {
        onSuccess: () => { showingModal.value = false; form.reset(); },
    });
};

const destroy = (user) => {
    if (!confirm(`Supprimer ${user.name} ? Les entretiens associés seront également supprimés.`)) return;
    useForm({}).post(route('team.destroy', user.id));
};

const roleLabel = (value) => props.roles.find((r) => r.value === value)?.label || value;
</script>

<template>
    <Head title="Équipe" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Équipe du cabinet
                </h2>
                <PrimaryButton @click="openCreate">+ Ajouter un membre</PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                <th class="px-4 py-3">Nom</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Poste</th>
                                <th class="px-4 py-3">Rôle</th>
                                <th class="px-4 py-3">Manager</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="u in users" :key="u.id" class="text-sm text-gray-700 dark:text-gray-200">
                                <td class="px-4 py-3 font-medium">{{ u.name }}</td>
                                <td class="px-4 py-3">{{ u.email }}</td>
                                <td class="px-4 py-3">{{ u.position || '—' }}</td>
                                <td class="px-4 py-3">{{ roleLabel(u.role) }}</td>
                                <td class="px-4 py-3">
                                    {{ managers.find((m) => m.id === u.manager_id)?.name || '—' }}
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <button class="text-brand-primary hover:underline" @click="openEdit(u)">
                                        Modifier
                                    </button>
                                    <button class="text-red-600 hover:underline" @click="destroy(u)">
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!users.length">
                                <td colspan="6" class="px-4 py-10 text-center text-sm italic text-gray-500">
                                    Aucun membre enregistré.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <Modal :show="showingModal" max-width="2xl" @close="showingModal = false">
            <form class="p-6" @submit.prevent="submit">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ editingUser ? 'Modifier le membre' : 'Nouveau membre' }}
                </h3>

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <InputLabel value="Nom complet" />
                        <TextInput v-model="form.name" type="text" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Email" />
                        <TextInput v-model="form.email" type="email" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Date d'embauche" />
                        <TextInput v-model="form.hired_on" type="date" class="mt-1 block w-full" />
                        <InputError :message="form.errors.hired_on" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Poste" />
                        <select v-model="form.position" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            <option value="" disabled>— choisir —</option>
                            <option v-for="p in positions" :key="p" :value="p">{{ p }}</option>
                        </select>
                        <InputError :message="form.errors.position" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Rôle applicatif" />
                        <select v-model="form.role" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            <option v-for="r in roles" :key="r.value" :value="r.value">{{ r.label }}</option>
                        </select>
                        <InputError :message="form.errors.role" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Manager" />
                        <select v-model="form.manager_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">— aucun —</option>
                            <option v-for="m in managers" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                        <InputError :message="form.errors.manager_id" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Service / pôle" />
                        <TextInput v-model="form.department" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.department" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel :value="editingUser ? 'Nouveau mot de passe (optionnel)' : 'Mot de passe'" />
                        <TextInput v-model="form.password" type="password" class="mt-1 block w-full"
                            :required="!editingUser" autocomplete="new-password" />
                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Confirmer mot de passe" />
                        <TextInput v-model="form.password_confirmation" type="password" class="mt-1 block w-full"
                            :required="!editingUser || !!form.password" autocomplete="new-password" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <SecondaryButton type="button" @click="showingModal = false">Annuler</SecondaryButton>
                    <PrimaryButton :disabled="form.processing">
                        {{ editingUser ? 'Enregistrer' : 'Créer le membre' }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>

```

---

## `resources/js/Pages/Templates/Index.vue`

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    templates: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Trames d'entretien" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Trames d'entretien
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl space-y-4 px-4 sm:px-6 lg:px-8">
                <p class="text-sm text-gray-600">
                    Chaque trame contient les sections et questions qui apparaissent dans le formulaire d'entretien.
                    Vous pouvez modifier les libellés des questions en cliquant sur « Modifier ».
                </p>

                <div class="overflow-hidden rounded-xl bg-white shadow-soft dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                <th class="px-4 py-3">Trame</th>
                                <th class="px-4 py-3">Clé</th>
                                <th class="px-4 py-3 text-center">Sections</th>
                                <th class="px-4 py-3 text-center">Questions</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="t in templates" :key="t.key" class="text-sm text-gray-700 dark:text-gray-200">
                                <td class="px-4 py-3 font-medium">{{ t.label }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ t.key }}</td>
                                <td class="px-4 py-3 text-center">{{ t.section_count }}</td>
                                <td class="px-4 py-3 text-center">{{ t.field_count }}</td>
                                <td class="px-4 py-3 text-right">
                                    <Link :href="route('templates.edit', t.key)"
                                        class="text-brand-primary hover:underline">
                                        Modifier
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

```

---

## `resources/js/Pages/Templates/Edit.vue`

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    template: { type: Object, required: true },
});

const form = useForm({
    label: props.template.label,
    header: (props.template.header || []).map((h) => ({ ...h })),
    sections: (props.template.sections || []).map((s) => ({
        title: s.title,
        fields: (s.fields || []).map((f) => ({ ...f })),
    })),
});

const submit = () => {
    form.post(route('templates.update', props.template.key), {
        preserveScroll: true,
    });
};

const fieldTypeName = (type) => {
    const m = {
        scale_10: 'Échelle 1-10',
        text: 'Texte court',
        textarea: 'Texte long',
        choice: 'Choix',
        objectives_review: 'Tableau objectifs (évaluation)',
        objectives_plan: 'Tableau objectifs (plan)',
        activities_table: 'Tableau activités',
        competency_grid: 'Grille de compétences',
    };
    return m[type] || type;
};

const ownerLabel = (owner) => {
    return owner === 'employee' ? 'Salarié' : owner === 'manager' ? 'Manager' : '—';
};
</script>

<template>
    <Head :title="`Modifier la trame — ${template.label}`" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <Link :href="route('templates.index')" class="text-sm text-brand-primary hover:underline">
                    ← Toutes les trames
                </Link>
                <h2 class="mt-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Modifier la trame : {{ template.label }}
                </h2>
                <p class="mt-1 text-xs text-gray-500">
                    Clé : <code class="font-mono">{{ template.key }}</code> —
                    Vous pouvez modifier le titre de la trame, les titres des sections et les libellés des questions.
                </p>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit">

                    <!-- Nom de la trame -->
                    <div class="rounded-xl bg-white p-5 shadow-soft dark:bg-gray-800">
                        <InputLabel value="Nom de la trame" />
                        <TextInput v-model="form.label" type="text" class="mt-1 block w-full max-w-md" required />
                        <InputError :message="form.errors.label" class="mt-2" />
                    </div>

                    <!-- Champs d'entête -->
                    <div v-if="form.header.length" class="mt-6 rounded-xl bg-white p-5 shadow-soft dark:bg-gray-800">
                        <h3 class="mb-3 text-base font-semibold text-gray-900 dark:text-gray-100">
                            Champs d'entête
                        </h3>
                        <div class="space-y-3">
                            <div v-for="(h, i) in form.header" :key="i"
                                class="grid grid-cols-1 gap-2 rounded border border-gray-200 p-3 sm:grid-cols-12 dark:border-gray-700">
                                <div class="sm:col-span-4">
                                    <InputLabel value="Libellé" />
                                    <TextInput v-model="h.label" type="text" class="mt-1 block w-full" />
                                </div>
                                <div class="sm:col-span-4">
                                    <InputLabel value="Clé" />
                                    <TextInput :model-value="h.key" type="text" class="mt-1 block w-full bg-gray-50" disabled />
                                </div>
                                <div class="sm:col-span-4">
                                    <InputLabel value="Rempli par" />
                                    <TextInput :model-value="ownerLabel(h.owner)" type="text" class="mt-1 block w-full bg-gray-50" disabled />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sections -->
                    <div v-for="(section, si) in form.sections" :key="si"
                        class="mt-6 rounded-xl bg-white p-5 shadow-soft dark:bg-gray-800">
                        <div class="mb-4">
                            <InputLabel :value="`Titre de la section ${si + 1}`" />
                            <TextInput v-model="section.title" type="text" class="mt-1 block w-full" required />
                            <InputError :message="form.errors[`sections.${si}.title`]" class="mt-2" />
                        </div>

                        <div class="space-y-3">
                            <div v-for="(field, fi) in section.fields" :key="fi"
                                class="rounded border border-gray-200 p-3 dark:border-gray-700">
                                <div class="mb-2 flex items-center justify-between text-xs text-gray-500">
                                    <span class="flex items-center gap-2">
                                        <span class="rounded bg-gray-100 px-2 py-0.5 font-mono dark:bg-gray-700">{{ fieldTypeName(field.type) }}</span>
                                        <span class="rounded bg-brand-tertiary px-2 py-0.5 text-brand-primary">{{ ownerLabel(field.owner) }}</span>
                                    </span>
                                    <span class="font-mono text-gray-400">{{ field.key }}</span>
                                </div>

                                <!-- Question / libellé -->
                                <div v-if="field.question !== undefined">
                                    <InputLabel value="Question / libellé" />
                                    <textarea v-model="field.question" rows="2"
                                        class="mt-1 block w-full rounded border-gray-300 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" />
                                    <InputError :message="form.errors[`sections.${si}.fields.${fi}.question`]" class="mt-2" />
                                </div>

                                <!-- Hint -->
                                <div v-if="field.hint !== undefined" class="mt-2">
                                    <InputLabel value="Note d'aide" />
                                    <TextInput v-model="field.hint" type="text" class="mt-1 block w-full" />
                                </div>

                                <!-- Lignes de la grille de compétences -->
                                <div v-if="field.rows" class="mt-2">
                                    <InputLabel value="Lignes de la grille" />
                                    <div class="mt-1 space-y-1">
                                        <TextInput v-for="(row, ri) in field.rows" :key="ri"
                                            v-model="field.rows[ri]" type="text" class="block w-full" />
                                    </div>
                                </div>

                                <!-- Options d'évaluation -->
                                <div v-if="field.evaluation_options" class="mt-2">
                                    <InputLabel value="Options d'évaluation" />
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        <TextInput v-for="(opt, oi) in field.evaluation_options" :key="oi"
                                            v-model="field.evaluation_options[oi]" type="text" class="w-40" />
                                    </div>
                                </div>

                                <!-- Options de choix -->
                                <div v-if="field.options" class="mt-2">
                                    <InputLabel value="Options de choix" />
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        <TextInput v-for="(opt, oi) in field.options" :key="oi"
                                            v-model="field.options[oi]" type="text" class="w-40" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Barre d'actions -->
                    <div class="mt-6 flex justify-end gap-2">
                        <Link :href="route('templates.index')">
                            <SecondaryButton type="button">Annuler</SecondaryButton>
                        </Link>
                        <PrimaryButton :disabled="form.processing">
                            Enregistrer les modifications
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

```

---

## `resources/js/Pages/Profile/Partials/DeleteUserForm.vue`

```vue
<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.post(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Delete Account
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Once your account is deleted, all of its resources and data will
                be permanently deleted. Before deleting your account, please
                download any data or information that you wish to retain.
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion">Delete Account</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2
                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                >
                    Are you sure you want to delete your account?
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Once your account is deleted, all of its resources and data
                    will be permanently deleted. Please enter your password to
                    confirm you would like to permanently delete your account.
                </p>

                <div class="mt-6">
                    <InputLabel
                        for="password"
                        value="Password"
                        class="sr-only"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="Password"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>

                    <DangerButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Delete Account
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>

```

---

## `resources/js/Pages/Profile/Partials/UpdatePasswordForm.vue`

```vue
<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.post(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Update Password
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Ensure your account is using a long, random password to stay
                secure.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
                <InputLabel for="current_password" value="Current Password" />

                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                />

                <InputError
                    :message="form.errors.current_password"
                    class="mt-2"
                />
            </div>

            <div>
                <InputLabel for="password" value="New Password" />

                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />

                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />

                <InputError
                    :message="form.errors.password_confirmation"
                    class="mt-2"
                />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600 dark:text-gray-400"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>

```

---

## `resources/js/Pages/Profile/Partials/UpdateProfileInformationForm.vue`

```vue
<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Profile Information
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Update your account's profile information and email address.
            </p>
        </header>

        <form
            @submit.prevent="form.post(route('profile.update'))"
            class="mt-6 space-y-6"
        >
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800 dark:text-gray-200">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600 dark:text-green-400"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600 dark:text-gray-400"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>

```

---

# 13. CSS

## `resources/css/app.css`

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

@layer base {
    body {
        background-color: theme('colors.brand.cream');
        background-image: theme('backgroundImage.hero-splash');
        background-attachment: fixed;
        color: theme('colors.brand.dark');
    }
    .dark body {
        background-color: #0B1120;
        background-image: none;
        color: theme('colors.brand.cream');
    }
}

@layer components {
    .card {
        @apply rounded-xl bg-white shadow-soft transition-shadow duration-200 dark:bg-gray-800;
    }
    .card:hover {
        @apply shadow-lg;
    }
}

```

---

## `resources/js/app.js`

```js
import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

```

---

## `resources/js/bootstrap.js`

```js
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

```

---

# 14. Docker & Déploiement

## `Dockerfile`

```Dockerfile
# Cabinet Dentaire — Entretiens annuels : image Docker prête pour Render / Railway / Fly.io.
# Build monolithique : installe PHP + Node, crée vendor/, construit Vite,
# puis supprime les dev-deps pour garder l'image fine.

FROM php:8.4-apache

# ---------- Dépendances système + extensions PHP ----------
RUN apt-get update && apt-get install -y --no-install-recommends \
      git curl ca-certificates unzip gnupg \
      libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
      libpq-dev libonig-dev libsqlite3-dev libxml2-dev sqlite3 \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" \
        pdo_sqlite pdo_pgsql pdo_mysql \
        zip intl gd bcmath opcache mbstring exif pcntl \
 && a2enmod rewrite headers \
 && rm -rf /var/lib/apt/lists/*

# Node.js 22 (LTS) pour le build Vite
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
 && apt-get install -y --no-install-recommends nodejs \
 && rm -rf /var/lib/apt/lists/*

# Composer (image officielle)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Cache composer/npm : on installe d'abord avec les lock files seuls.
# Fallback composer update si composer.lock est en retard par rapport à
# composer.json (pratique quand on ajoute un package sans pouvoir regénérer
# le lock localement).
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction \
 || composer update --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

# ---------- Code source ----------
COPY . .

# Finalisation composer (autoload + scripts artisan post-install)
RUN composer dump-autoload --optimize --no-dev

# Build assets Vite (nécessite vendor/tightenco/ziggy donc on passe APRÈS composer)
RUN npm run build && rm -rf node_modules

# Permissions storage + cache
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R ug+rwx storage bootstrap/cache

# ---------- Apache ----------
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    SESSION_DRIVER=file \
    CACHE_STORE=file

EXPOSE 8080
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]

```

---

## `docker/entrypoint.sh`

```sh
#!/usr/bin/env bash
# Entrypoint conteneur : configure Apache sur $PORT, applique les migrations
# puis enchaîne sur la commande finale (apache2-foreground).
set -e

PORT=${PORT:-8080}

# Patch du vhost Apache avec le port réel
sed -i "s/PORT_PLACEHOLDER/${PORT}/g" /etc/apache2/sites-available/000-default.conf
echo "Listen ${PORT}" > /etc/apache2/ports.conf

# .env de base : Laravel le lit en priorité (env vars l'écrasent ensuite)
if [ ! -f .env ]; then
    cp .env.example .env 2>/dev/null || touch .env
fi

# APP_KEY : Laravel exige un format "base64:<32 bytes>". Render `generateValue`
# produit un hex brut incompatible avec AES-256-CBC. On valide et regénère
# si besoin, puis on exporte pour la suite du boot.
if ! echo "${APP_KEY:-}" | grep -qE '^base64:[A-Za-z0-9+/]+=*$'; then
    echo "APP_KEY manquante ou au mauvais format — génération d'une clé base64 valide."
    NEW_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
    export APP_KEY="$NEW_KEY"
    # Écrit aussi dans .env pour que key:generate / artisan le retrouvent
    if grep -q '^APP_KEY=' .env 2>/dev/null; then
        sed -i "s|^APP_KEY=.*|APP_KEY=${NEW_KEY}|" .env
    else
        echo "APP_KEY=${NEW_KEY}" >> .env
    fi
fi

# SQLite : crée le fichier si manquant
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    DB_PATH=${DB_DATABASE:-/var/www/html/database/database.sqlite}
    mkdir -p "$(dirname "$DB_PATH")"
    [ -f "$DB_PATH" ] || touch "$DB_PATH"
    chown www-data:www-data "$DB_PATH"
fi

# Nettoyage préalable des caches (config obsolète du build)
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Migrations + storage link
php artisan storage:link || true
php artisan migrate --force --graceful

# Re-cache une fois que tout est en place
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Seed uniquement au premier démarrage (sentinelle dans storage)
SENTINEL=/var/www/html/storage/app/.seeded
if [ ! -f "$SENTINEL" ]; then
    echo "Premier démarrage — exécution du seeder..."
    if php artisan db:seed --force; then
        touch "$SENTINEL"
    fi
fi

exec "$@"

```

---

## `docker/apache.conf`

```conf
# Cabinet Dentaire — Apache vhost pour Laravel sur Render / Railway / Fly.io
# Le port est fourni à l'exécution par la plateforme (PORT env var).
# Ce fichier est patché au démarrage par entrypoint.sh pour refléter $PORT.

<VirtualHost *:PORT_PLACEHOLDER>
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
        Options -Indexes +FollowSymLinks
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>

```

---

## `render.yaml`

```yaml
# Blueprint Render pour l'appli Entretiens Annuels — Cabinet Dentaire
# Créez un nouveau Blueprint sur https://dashboard.render.com
# et pointez-le sur ce repo ; Render provisionne automatiquement
# le service web Docker + la base PostgreSQL.
services:
  - type: web
    name: cabinet-entretiens
    runtime: docker
    plan: free
    region: frankfurt
    envVars:
      - key: APP_NAME
        value: "Cabinet Dentaire"
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: "false"
      - key: APP_URL
        sync: false              # à compléter avec l'URL Render après 1er déploiement
      - key: APP_LOCALE
        value: fr
      # APP_KEY non définie : l'entrypoint génère une clé base64 valide au 1er boot.
      # (Render `generateValue` produit un hex brut incompatible avec AES-256-CBC.)
      - key: LOG_CHANNEL
        value: stderr
      - key: SESSION_DRIVER
        value: file
      - key: CACHE_STORE
        value: file
      - key: DB_CONNECTION
        value: pgsql
      - key: DB_HOST
        fromDatabase:
          name: cabinet-db
          property: host
      - key: DB_PORT
        fromDatabase:
          name: cabinet-db
          property: port
      - key: DB_DATABASE
        fromDatabase:
          name: cabinet-db
          property: database
      - key: DB_USERNAME
        fromDatabase:
          name: cabinet-db
          property: user
      - key: DB_PASSWORD
        fromDatabase:
          name: cabinet-db
          property: password

databases:
  - name: cabinet-db
    plan: free
    databaseName: cabinet
    user: cabinet
    region: frankfurt
    postgresMajorVersion: "16"

```

---

## `.dockerignore`

```dockerignore
.git
.gitignore
.github
.vscode
.idea
node_modules
vendor
tests
*.md
storage/logs/*
storage/framework/cache/*
storage/framework/sessions/*
storage/framework/views/*
database/database.sqlite
public/build
public/storage
public/hot
.env
.env.*
!.env.example
npm-debug.log
yarn-error.log
Dockerfile
.dockerignore

```

---

## `.gitignore`

```gitignore
*.log
.DS_Store
.env
.env.backup
.env.production
.phpactor.json
.phpunit.result.cache
/.fleet
/.idea
/.nova
/.phpunit.cache
/.vscode
/.zed
/auth.json
/node_modules
/public/build
/public/hot
/public/storage
/storage/*.key
/storage/pail
/vendor
_ide_helper.php
Homestead.json
Homestead.yaml
Thumbs.db

```

---


# Fin du document
