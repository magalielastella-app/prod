# OSCD Recrutement — Code source complet

Branche : `OSCD-devmagalie` · Généré le 25/05/2026

---

# Configuration

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

## `config/services.php`

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
    ],

];

```

---

## `config/session.php`

```php
<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Session Driver
    |--------------------------------------------------------------------------
    |
    | This option determines the default session driver that is utilized for
    | incoming requests. Laravel supports a variety of storage options to
    | persist session data. Database storage is a great default choice.
    |
    | Supported: "file", "cookie", "database", "memcached",
    |            "redis", "dynamodb", "array"
    |
    */

    'driver' => env('SESSION_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Session Lifetime
    |--------------------------------------------------------------------------
    |
    | Here you may specify the number of minutes that you wish the session
    | to be allowed to remain idle before it expires. If you want them
    | to expire immediately when the browser is closed then you may
    | indicate that via the expire_on_close configuration option.
    |
    */

    'lifetime' => (int) env('SESSION_LIFETIME', 240),

    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),

    /*
    |--------------------------------------------------------------------------
    | Session Encryption
    |--------------------------------------------------------------------------
    |
    | This option allows you to easily specify that all of your session data
    | should be encrypted before it's stored. All encryption is performed
    | automatically by Laravel and you may use the session like normal.
    |
    */

    'encrypt' => env('SESSION_ENCRYPT', false),

    /*
    |--------------------------------------------------------------------------
    | Session File Location
    |--------------------------------------------------------------------------
    |
    | When utilizing the "file" session driver, the session files are placed
    | on disk. The default storage location is defined here; however, you
    | are free to provide another location where they should be stored.
    |
    */

    'files' => storage_path('framework/sessions'),

    /*
    |--------------------------------------------------------------------------
    | Session Database Connection
    |--------------------------------------------------------------------------
    |
    | When using the "database" or "redis" session drivers, you may specify a
    | connection that should be used to manage these sessions. This should
    | correspond to a connection in your database configuration options.
    |
    */

    'connection' => env('SESSION_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Session Database Table
    |--------------------------------------------------------------------------
    |
    | When using the "database" session driver, you may specify the table to
    | be used to store sessions. Of course, a sensible default is defined
    | for you; however, you're welcome to change this to another table.
    |
    */

    'table' => env('SESSION_TABLE', 'sessions'),

    /*
    |--------------------------------------------------------------------------
    | Session Cache Store
    |--------------------------------------------------------------------------
    |
    | When using one of the framework's cache driven session backends, you may
    | define the cache store which should be used to store the session data
    | between requests. This must match one of your defined cache stores.
    |
    | Affects: "dynamodb", "memcached", "redis"
    |
    */

    'store' => env('SESSION_STORE'),

    /*
    |--------------------------------------------------------------------------
    | Session Sweeping Lottery
    |--------------------------------------------------------------------------
    |
    | Some session drivers must manually sweep their storage location to get
    | rid of old sessions from storage. Here are the chances that it will
    | happen on a given request. By default, the odds are 2 out of 100.
    |
    */

    'lottery' => [2, 100],

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Name
    |--------------------------------------------------------------------------
    |
    | Here you may change the name of the session cookie that is created by
    | the framework. Typically, you should not need to change this value
    | since doing so does not grant a meaningful security improvement.
    |
    */

    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug((string) env('APP_NAME', 'laravel')).'-session'
    ),

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Path
    |--------------------------------------------------------------------------
    |
    | The session cookie path determines the path for which the cookie will
    | be regarded as available. Typically, this will be the root path of
    | your application, but you're free to change this when necessary.
    |
    */

    'path' => env('SESSION_PATH', '/'),

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Domain
    |--------------------------------------------------------------------------
    |
    | This value determines the domain and subdomains the session cookie is
    | available to. By default, the cookie will be available to the root
    | domain without subdomains. Typically, this shouldn't be changed.
    |
    */

    'domain' => env('SESSION_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | HTTPS Only Cookies
    |--------------------------------------------------------------------------
    |
    | By setting this option to true, session cookies will only be sent back
    | to the server if the browser has a HTTPS connection. This will keep
    | the cookie from being sent to you when it can't be done securely.
    |
    */

    'secure' => env('SESSION_SECURE_COOKIE'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Access Only
    |--------------------------------------------------------------------------
    |
    | Setting this value to true will prevent JavaScript from accessing the
    | value of the cookie and the cookie will only be accessible through
    | the HTTP protocol. It's unlikely you should disable this option.
    |
    */

    'http_only' => env('SESSION_HTTP_ONLY', true),

    /*
    |--------------------------------------------------------------------------
    | Same-Site Cookies
    |--------------------------------------------------------------------------
    |
    | This option determines how your cookies behave when cross-site requests
    | take place, and can be used to mitigate CSRF attacks. By default, we
    | will set this value to "lax" to permit secure cross-site requests.
    |
    | See: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie#samesitesamesite-value
    |
    | Supported: "lax", "strict", "none", null
    |
    */

    'same_site' => env('SESSION_SAME_SITE', 'lax'),

    /*
    |--------------------------------------------------------------------------
    | Partitioned Cookies
    |--------------------------------------------------------------------------
    |
    | Setting this value to true will tie the cookie to the top-level site for
    | a cross-site context. Partitioned cookies are accepted by the browser
    | when flagged "secure" and the Same-Site attribute is set to "none".
    |
    */

    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),

    /*
    |--------------------------------------------------------------------------
    | Session Serialization
    |--------------------------------------------------------------------------
    |
    | This value controls the serialization strategy for session data, which
    | is JSON by default. Setting this to "php" allows the storage of PHP
    | objects in the session but can make an application vulnerable to
    | "gadget chain" serialization attacks if the APP_KEY is leaked.
    |
    | Supported: "json", "php"
    |
    */

    'serialization' => 'json',

];

```

---

# Routes

## `routes/web.php`

```php
<?php

use App\Http\Controllers\CandidateAssistantController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CvAnalysisController;
use App\Http\Controllers\CvDocumentController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\InterviewEventController;
use App\Http\Controllers\JobPositionController;
use App\Http\Controllers\RecruitmentCampaignController;
use App\Http\Controllers\InterviewReportController;
use App\Http\Controllers\InterviewScriptController;
use App\Http\Controllers\JobOfferController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecruitmentDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', RecruitmentDashboardController::class)->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profil (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/supprimer', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ---------- Métiers ----------
    Route::post('/metiers', [JobPositionController::class, 'store'])->name('positions.store');
    Route::post('/metiers/{jobPosition}/supprimer', [JobPositionController::class, 'destroy'])->name('positions.destroy');

    // ---------- Campagnes de recrutement ----------
    Route::get('/campagnes', [RecruitmentCampaignController::class, 'index'])->name('campaigns.index');
    Route::post('/campagnes', [RecruitmentCampaignController::class, 'store'])->name('campaigns.store');
    Route::post('/campagnes/{campaign}', [RecruitmentCampaignController::class, 'update'])->name('campaigns.update');
    Route::post('/campagnes/{campaign}/supprimer', [RecruitmentCampaignController::class, 'destroy'])->name('campaigns.destroy');

    // ---------- Assistant IA candidats ----------
    Route::post('/candidats/assistant', [CandidateAssistantController::class, 'ask'])->name('candidates.assistant');

    // ---------- Candidats + CVthèque ----------
    Route::get('/candidats', [CandidateController::class, 'index'])->name('candidates.index');
    Route::post('/candidats', [CandidateController::class, 'store'])->name('candidates.store');
    Route::get('/candidats/{candidate}', [CandidateController::class, 'show'])->name('candidates.show');
    Route::post('/candidats/{candidate}', [CandidateController::class, 'update'])->name('candidates.update');
    Route::post('/candidats/{candidate}/supprimer', [CandidateController::class, 'destroy'])->name('candidates.destroy');

    // ---------- CV upload/download ----------
    Route::post('/candidats/{candidate}/cv', [CvDocumentController::class, 'store'])->name('cv.store');
    Route::get('/cv/{cvDocument}/telecharger', [CvDocumentController::class, 'download'])->name('cv.download');
    Route::post('/cv/{cvDocument}/supprimer', [CvDocumentController::class, 'destroy'])->name('cv.destroy');

    // ---------- Analyses IA ----------
    Route::get('/analyses', [CvAnalysisController::class, 'index'])->name('analyses.index');
    Route::post('/analyses', [CvAnalysisController::class, 'store'])->name('analyses.store');
    Route::post('/analyses/comparer', [CvAnalysisController::class, 'compare'])->name('analyses.compare');
    Route::post('/analyses/{analysis}/supprimer', [CvAnalysisController::class, 'destroy'])->name('analyses.destroy');

    // ---------- Offres d'emploi ----------
    Route::get('/offres', [JobOfferController::class, 'index'])->name('offers.index');
    Route::post('/offres', [JobOfferController::class, 'store'])->name('offers.store');
    Route::get('/offres/{jobOffer}', [JobOfferController::class, 'show'])->name('offers.show');
    Route::post('/offres/{jobOffer}', [JobOfferController::class, 'update'])->name('offers.update');
    Route::post('/offres/{jobOffer}/archiver', [JobOfferController::class, 'archive'])->name('offers.archive');
    Route::post('/offres/{jobOffer}/supprimer', [JobOfferController::class, 'destroy'])->name('offers.destroy');

    // ---------- Comptes-rendus d'entretien ----------
    Route::get('/comptes-rendus', [InterviewReportController::class, 'index'])->name('reports.index');
    Route::post('/comptes-rendus', [InterviewReportController::class, 'store'])->name('reports.store');
    Route::post('/comptes-rendus/{report}', [InterviewReportController::class, 'update'])->name('reports.update');
    Route::post('/comptes-rendus/{report}/supprimer', [InterviewReportController::class, 'destroy'])->name('reports.destroy');

    // ---------- Scripts d'entretien ----------
    Route::get('/scripts', [InterviewScriptController::class, 'index'])->name('scripts.index');
    Route::post('/scripts', [InterviewScriptController::class, 'store'])->name('scripts.store');
    Route::get('/scripts/{script}', [InterviewScriptController::class, 'show'])->name('scripts.show');
    Route::post('/scripts/{script}', [InterviewScriptController::class, 'update'])->name('scripts.update');
    Route::post('/scripts/{script}/supprimer', [InterviewScriptController::class, 'destroy'])->name('scripts.destroy');

    // ---------- Emails ----------
    Route::get('/emails', [EmailTemplateController::class, 'index'])->name('emails.index');
    Route::post('/emails/modeles', [EmailTemplateController::class, 'storeTemplate'])->name('emails.templates.store');
    Route::post('/emails/modeles/{template}', [EmailTemplateController::class, 'updateTemplate'])->name('emails.templates.update');
    Route::post('/emails/modeles/{template}/supprimer', [EmailTemplateController::class, 'destroyTemplate'])->name('emails.templates.destroy');
    Route::post('/emails/envoyer', [EmailTemplateController::class, 'send'])->name('emails.send');

    // ---------- Agenda ----------
    Route::get('/agenda', [InterviewEventController::class, 'index'])->name('agenda.index');
    Route::post('/agenda', [InterviewEventController::class, 'store'])->name('agenda.store');
    Route::post('/agenda/{event}', [InterviewEventController::class, 'update'])->name('agenda.update');
    Route::post('/agenda/{event}/supprimer', [InterviewEventController::class, 'destroy'])->name('agenda.destroy');
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

# Modèles

## `app/Models/Candidate.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    public const STATUS_A_ANALYSER = 'a_analyser';
    public const STATUS_SELECTIONNE = 'selectionne';
    public const STATUS_REJETE = 'rejete';

    public const STATUSES = [
        self::STATUS_A_ANALYSER => 'À analyser',
        self::STATUS_SELECTIONNE => 'Sélectionné',
        self::STATUS_REJETE => 'Rejeté',
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'city',
        'status',
        'source',
        'notes',
        'campaign_id',
        'job_position_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(RecruitmentCampaign::class, 'campaign_id');
    }

    public function jobPosition(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class);
    }

    public function cvDocuments(): HasMany
    {
        return $this->hasMany(CvDocument::class);
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(CvAnalysis::class);
    }

    public function interviewReports(): HasMany
    {
        return $this->hasMany(InterviewReport::class);
    }

    public function interviewEvents(): HasMany
    {
        return $this->hasMany(InterviewEvent::class);
    }
}

```

---

## `app/Models/CvAnalysis.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvAnalysis extends Model
{
    protected $table = 'cv_analyses';

    protected $fillable = [
        'candidate_id',
        'cv_document_id',
        'analysis',
        'prompt_used',
        'model_used',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function cvDocument(): BelongsTo
    {
        return $this->belongsTo(CvDocument::class);
    }
}

```

---

## `app/Models/CvDocument.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvDocument extends Model
{
    protected $fillable = [
        'candidate_id',
        'original_name',
        'file_path',
        'file_size',
        'mime_type',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}

```

---

## `app/Models/EmailTemplate.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = ['name', 'subject', 'body', 'description'];
}

```

---

## `app/Models/InterviewEvent.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewEvent extends Model
{
    protected $fillable = [
        'candidate_id',
        'job_offer_id',
        'interviewer_id',
        'title',
        'scheduled_at',
        'duration_minutes',
        'location',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'duration_minutes' => 'integer',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function jobOffer(): BelongsTo
    {
        return $this->belongsTo(JobOffer::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }
}

```

---

## `app/Models/InterviewReport.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewReport extends Model
{
    protected $fillable = [
        'candidate_id',
        'job_offer_id',
        'interviewer_id',
        'interview_date',
        'rating',
        'strengths',
        'weaknesses',
        'notes',
        'recommendation',
    ];

    protected function casts(): array
    {
        return [
            'interview_date' => 'date',
            'rating' => 'integer',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function jobOffer(): BelongsTo
    {
        return $this->belongsTo(JobOffer::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }
}

```

---

## `app/Models/InterviewScript.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewScript extends Model
{
    protected $fillable = [
        'title',
        'description',
        'sections',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'sections' => 'array',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

```

---

## `app/Models/JobOffer.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobOffer extends Model
{
    protected $fillable = [
        'title',
        'department',
        'location',
        'contract_type',
        'description',
        'requirements',
        'salary_range',
        'status',
        'published_at',
        'archived_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function interviewReports(): HasMany
    {
        return $this->hasMany(InterviewReport::class);
    }

    public function interviewEvents(): HasMany
    {
        return $this->hasMany(InterviewEvent::class);
    }
}

```

---

## `app/Models/JobPosition.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPosition extends Model
{
    protected $fillable = ['name', 'is_default'];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }
}

```

---

## `app/Models/RecruitmentCampaign.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecruitmentCampaign extends Model
{
    protected $fillable = ['title', 'description', 'job_offer_id', 'status'];

    public function jobOffer(): BelongsTo
    {
        return $this->belongsTo(JobOffer::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'campaign_id');
    }
}

```

---

## `app/Models/SentEmail.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SentEmail extends Model
{
    protected $fillable = [
        'candidate_id', 'email_template_id', 'to_email',
        'subject', 'body', 'status',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'email_template_id');
    }
}

```

---

## `app/Models/User.php`

```php
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'must_change_password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }
}

```

---

# Contrôleurs

## `app/Http/Controllers/CandidateAssistantController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CandidateAssistantController extends Controller
{
    /**
     * Assistant IA conversationnel : répond à des questions sur le pool
     * de candidats (ex : "qui habite le plus proche de La Tronche ?",
     * "combien de candidats sont sélectionnés ?", etc.).
     *
     * Les données candidats sont injectées comme contexte à Claude.
     */
    public function ask(Request $request): JsonResponse
    {
        $request->validate([
            'question' => ['required', 'string', 'max:2000'],
            'campaign_id' => ['nullable', 'exists:recruitment_campaigns,id'],
        ]);

        $apiKey = config('services.anthropic.api_key');
        if (empty($apiKey)) {
            return response()->json([
                'answer' => "L'assistant IA nécessite une clé API. Configurez ANTHROPIC_API_KEY dans les variables d'environnement.",
            ]);
        }

        // Récupère les candidats (filtrés par campagne si précisé)
        $query = Candidate::with('campaign:id,title');
        if ($request->campaign_id) {
            $query->where('campaign_id', $request->campaign_id);
        }
        $candidates = $query->orderBy('last_name')->get();

        // Formate les candidats comme contexte structuré
        $candidateContext = $candidates->map(function ($c) {
            return implode(' | ', array_filter([
                "Nom: {$c->full_name}",
                $c->email ? "Email: {$c->email}" : null,
                $c->phone ? "Tél: {$c->phone}" : null,
                $c->city ? "Ville: {$c->city}" : null,
                "Statut: " . (Candidate::STATUSES[$c->status] ?? $c->status),
                $c->source ? "Source: {$c->source}" : null,
                $c->campaign ? "Campagne: {$c->campaign->title}" : null,
                $c->notes ? "Notes: " . \Illuminate\Support\Str::limit($c->notes, 200) : null,
            ]));
        })->implode("\n");

        $systemPrompt = "Tu es un assistant RH pour une entreprise. Tu as accès à la base de candidats ci-dessous. "
            . "Réponds aux questions de l'utilisateur en te basant UNIQUEMENT sur ces données. "
            . "Si tu ne peux pas répondre avec certitude, dis-le. "
            . "Réponds en français, de manière concise et structurée.\n\n"
            . "CANDIDATS (" . $candidates->count() . " au total) :\n"
            . $candidateContext;

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 1024,
                'system' => $systemPrompt,
                'messages' => [
                    ['role' => 'user', 'content' => $request->question],
                ],
            ]);

            $result = $response->json();
            $answer = $result['content'][0]['text'] ?? 'Pas de réponse.';
        } catch (\Exception $e) {
            $answer = 'Erreur de communication avec Claude : ' . $e->getMessage();
        }

        return response()->json(['answer' => $answer]);
    }
}

```

---

## `app/Http/Controllers/CandidateController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\JobPosition;
use App\Models\RecruitmentCampaign;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        $query = Candidate::with(['campaign:id,title', 'jobPosition:id,name']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($campaignId = $request->get('campaign')) {
            $query->where('campaign_id', $campaignId);
        }

        $candidates = $query->latest()->paginate(20)->withQueryString();

        $campaigns = RecruitmentCampaign::orderBy('title')->get(['id', 'title', 'status']);
        $jobPositions = JobPosition::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Candidates/Index', [
            'candidates' => $candidates,
            'campaigns' => $campaigns,
            'jobPositions' => $jobPositions,
            'statuses' => Candidate::STATUSES,
            'filters' => [
                'search' => $request->get('search', ''),
                'status' => $request->get('status', ''),
                'campaign' => $request->get('campaign', ''),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'campaign_id' => 'nullable|exists:recruitment_campaigns,id',
            'job_position_id' => 'nullable|exists:job_positions,id',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $candidate = Candidate::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'city' => $validated['city'] ?? null,
            'source' => $validated['source'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'campaign_id' => $validated['campaign_id'] ?? null,
            'job_position_id' => $validated['job_position_id'] ?? null,
            'status' => Candidate::STATUS_A_ANALYSER,
        ]);

        // Si un CV a été uploadé en même temps que la création
        if ($request->hasFile('cv')) {
            $file = $request->file('cv');
            $path = $file->store('cvs', 'local');

            $candidate->cvDocuments()->create([
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }

        return redirect()->route('candidates.index')->with('success', 'Candidat ajouté avec succès.');
    }

    public function show(Candidate $candidate)
    {
        $candidate->load([
            'campaign:id,title',
            'jobPosition:id,name',
            'cvDocuments',
            'analyses.cvDocument',
            'interviewReports.jobOffer',
            'interviewReports.interviewer',
            'interviewEvents.jobOffer',
        ]);

        $campaigns = RecruitmentCampaign::where('status', 'active')
            ->orderBy('title')
            ->get(['id', 'title']);

        $jobPositions = JobPosition::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Candidates/Show', [
            'candidate' => $candidate,
            'campaigns' => $campaigns,
            'jobPositions' => $jobPositions,
            'statuses' => Candidate::STATUSES,
        ]);
    }

    public function update(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:a_analyser,selectionne,rejete',
            'campaign_id' => 'nullable|exists:recruitment_campaigns,id',
            'job_position_id' => 'nullable|exists:job_positions,id',
        ]);

        $candidate->update($validated);

        return redirect()->back()->with('success', 'Candidat mis à jour.');
    }

    public function destroy(Candidate $candidate)
    {
        $candidate->delete();
        return redirect()->route('candidates.index')->with('success', 'Candidat supprimé.');
    }
}

```

---

## `app/Http/Controllers/Controller.php`

```php
<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //
}

```

---

## `app/Http/Controllers/CvAnalysisController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CvAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class CvAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $analyses = CvAnalysis::with(['candidate', 'cvDocument'])
            ->latest()
            ->paginate(20);

        $candidates = Candidate::orderBy('last_name')->get(['id', 'first_name', 'last_name']);

        return Inertia::render('Analyses/Index', [
            'analyses' => $analyses,
            'candidates' => $candidates,
            'apiKeyConfigured' => !empty(config('services.anthropic.api_key')),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'cv_document_id' => 'nullable|exists:cv_documents,id',
            'prompt' => 'nullable|string',
        ]);

        $apiKey = config('services.anthropic.api_key');

        if (empty($apiKey)) {
            return redirect()->back()->withErrors([
                'api' => 'Configurez ANTHROPIC_API_KEY pour activer l\'analyse IA.',
            ]);
        }

        $candidate = Candidate::with('cvDocuments')->findOrFail($validated['candidate_id']);

        $prompt = $validated['prompt'] ?? "Analyse le profil de ce candidat et fournis un résumé de ses compétences, expériences et points forts. Candidat : {$candidate->full_name}";

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 2048,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $result = $response->json();
            $analysisText = $result['content'][0]['text'] ?? 'Aucune analyse disponible.';
            $modelUsed = $result['model'] ?? 'claude-sonnet-4-20250514';
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'api' => 'Erreur lors de l\'appel à l\'API Claude : ' . $e->getMessage(),
            ]);
        }

        CvAnalysis::create([
            'candidate_id' => $validated['candidate_id'],
            'cv_document_id' => $validated['cv_document_id'] ?? null,
            'analysis' => $analysisText,
            'prompt_used' => $prompt,
            'model_used' => $modelUsed,
        ]);

        return redirect()->route('analyses.index')->with('success', 'Analyse sauvegardée.');
    }

    /** Comparer plusieurs CV entre eux via Claude. */
    public function compare(Request $request)
    {
        $validated = $request->validate([
            'candidate_ids' => 'required|array|min:2|max:10',
            'candidate_ids.*' => 'exists:candidates,id',
            'job_context' => 'nullable|string',
        ]);

        $apiKey = config('services.anthropic.api_key');
        if (empty($apiKey)) {
            return redirect()->back()->withErrors([
                'api' => 'Configurez ANTHROPIC_API_KEY pour activer l\'analyse IA.',
            ]);
        }

        $candidates = Candidate::with('cvDocuments')
            ->whereIn('id', $validated['candidate_ids'])
            ->get();

        $candidateDescriptions = $candidates->map(function ($c) {
            $cvCount = $c->cvDocuments->count();
            return "- {$c->full_name}" . ($c->email ? " ({$c->email})" : '') . " — {$cvCount} CV enregistré(s)";
        })->implode("\n");

        $jobContext = $validated['job_context'] ?? '';
        $contextLine = $jobContext ? "\n\nContexte du poste : {$jobContext}" : '';

        $prompt = "Compare les candidats suivants pour un recrutement. Pour chacun, identifie les points forts et points faibles. Termine par une recommandation de classement.{$contextLine}\n\nCandidats :\n{$candidateDescriptions}";

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 4096,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $result = $response->json();
            $analysisText = $result['content'][0]['text'] ?? 'Aucune analyse disponible.';
            $modelUsed = $result['model'] ?? 'claude-sonnet-4-20250514';
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'api' => 'Erreur : ' . $e->getMessage(),
            ]);
        }

        // Sauvegarder une analyse pour le premier candidat de la liste (comme analyse comparative)
        CvAnalysis::create([
            'candidate_id' => $candidates->first()->id,
            'analysis' => "[COMPARAISON]\n\n" . $analysisText,
            'prompt_used' => $prompt,
            'model_used' => $modelUsed,
        ]);

        return redirect()->route('analyses.index')->with('success', 'Analyse comparative sauvegardée.');
    }

    public function destroy(CvAnalysis $analysis)
    {
        $analysis->delete();

        return redirect()->back()->with('success', 'Analyse supprimée.');
    }
}

```

---

## `app/Http/Controllers/CvDocumentController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CvDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CvDocumentController extends Controller
{
    public function store(Request $request, Candidate $candidate)
    {
        $request->validate([
            'cv_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $file = $request->file('cv_file');
        $path = $file->store('cvs', 'local');

        $candidate->cvDocuments()->create([
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        return redirect()->back()->with('success', 'CV uploadé avec succès.');
    }

    public function download(CvDocument $cvDocument)
    {
        $path = Storage::disk('local')->path($cvDocument->file_path);

        return response()->download($path, $cvDocument->original_name);
    }

    public function destroy(CvDocument $cvDocument)
    {
        Storage::disk('local')->delete($cvDocument->file_path);
        $cvDocument->delete();

        return redirect()->back()->with('success', 'CV supprimé.');
    }
}

```

---

## `app/Http/Controllers/EmailTemplateController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\SentEmail;
use App\Models\Candidate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class EmailTemplateController extends Controller
{
    public function index(): Response
    {
        $templates = EmailTemplate::orderByDesc('updated_at')->get();
        $sentEmails = SentEmail::with(['candidate:id,first_name,last_name', 'template:id,name'])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(fn (SentEmail $e) => [
                'id' => $e->id,
                'candidate_name' => $e->candidate ? $e->candidate->first_name . ' ' . $e->candidate->last_name : '—',
                'template_name' => $e->template?->name,
                'to_email' => $e->to_email,
                'subject' => $e->subject,
                'status' => $e->status,
                'sent_at' => $e->created_at->format('d/m/Y H:i'),
            ]);

        return Inertia::render('Emails/Index', [
            'templates' => $templates,
            'sentEmails' => $sentEmails,
        ]);
    }

    public function storeTemplate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        EmailTemplate::create($data);

        return back()->with('success', 'Modèle d\'email créé');
    }

    public function updateTemplate(Request $request, EmailTemplate $template): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $template->update($data);

        return back()->with('success', 'Modèle mis à jour');
    }

    public function destroyTemplate(EmailTemplate $template): RedirectResponse
    {
        $template->delete();
        return back()->with('success', 'Modèle supprimé');
    }

    /** Envoyer un email à un candidat. */
    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'candidate_id' => ['required', 'exists:candidates,id'],
            'email_template_id' => ['nullable', 'exists:email_templates,id'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $candidate = Candidate::findOrFail($data['candidate_id']);

        if (! $candidate->email) {
            return back()->with('error', 'Ce candidat n\'a pas d\'adresse email.');
        }

        $status = 'sent';
        try {
            Mail::raw($data['body'], function ($message) use ($candidate, $data) {
                $message->to($candidate->email)
                    ->subject($data['subject']);
            });
        } catch (\Throwable $e) {
            $status = 'failed';
        }

        SentEmail::create([
            'candidate_id' => $candidate->id,
            'email_template_id' => $data['email_template_id'] ?? null,
            'to_email' => $candidate->email,
            'subject' => $data['subject'],
            'body' => $data['body'],
            'status' => $status,
        ]);

        return back()->with(
            $status === 'sent' ? 'success' : 'error',
            $status === 'sent' ? 'Email envoyé à ' . $candidate->email : 'Échec d\'envoi — vérifiez la configuration SMTP.'
        );
    }
}

```

---

## `app/Http/Controllers/InterviewEventController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\InterviewEvent;
use App\Models\JobOffer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InterviewEventController extends Controller
{
    public function index(Request $request)
    {
        $query = InterviewEvent::with(['candidate', 'jobOffer', 'interviewer']);

        if ($from = $request->get('from')) {
            $query->where('scheduled_at', '>=', Carbon::parse($from)->startOfDay());
        }

        if ($to = $request->get('to')) {
            $query->where('scheduled_at', '<=', Carbon::parse($to)->endOfDay());
        }

        $events = $query->orderBy('scheduled_at')->paginate(30)->withQueryString();

        $candidates = Candidate::orderBy('last_name')->get(['id', 'first_name', 'last_name']);
        $offers = JobOffer::where('status', 'active')->orderBy('title')->get(['id', 'title']);
        $interviewers = User::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Agenda/Index', [
            'events' => $events,
            'candidates' => $candidates,
            'offers' => $offers,
            'interviewers' => $interviewers,
            'filters' => [
                'from' => $request->get('from', ''),
                'to' => $request->get('to', ''),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'job_offer_id' => 'nullable|exists:job_offers,id',
            'interviewer_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        InterviewEvent::create($validated);

        return redirect()->route('agenda.index')->with('success', 'Entretien planifié.');
    }

    public function update(Request $request, InterviewEvent $event)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'job_offer_id' => 'nullable|exists:job_offers,id',
            'interviewer_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:scheduled,completed,cancelled',
        ]);

        $event->update($validated);

        return redirect()->back()->with('success', 'Événement mis à jour.');
    }

    public function destroy(InterviewEvent $event)
    {
        $event->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Événement annulé.');
    }
}

```

---

## `app/Http/Controllers/InterviewReportController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\InterviewReport;
use App\Models\JobOffer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InterviewReportController extends Controller
{
    public function index(Request $request)
    {
        $query = InterviewReport::with(['candidate', 'jobOffer', 'interviewer']);

        if ($candidateId = $request->get('candidate_id')) {
            $query->where('candidate_id', $candidateId);
        }

        $reports = $query->latest('interview_date')->paginate(20)->withQueryString();

        $candidates = Candidate::orderBy('last_name')->get(['id', 'first_name', 'last_name']);
        $offers = JobOffer::orderBy('title')->get(['id', 'title']);

        return Inertia::render('Reports/Index', [
            'reports' => $reports,
            'candidates' => $candidates,
            'offers' => $offers,
            'filters' => [
                'candidate_id' => $request->get('candidate_id', ''),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'job_offer_id' => 'nullable|exists:job_offers,id',
            'interview_date' => 'required|date',
            'rating' => 'nullable|integer|min:1|max:5',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'notes' => 'nullable|string',
            'recommendation' => 'nullable|in:hire,maybe,reject',
        ]);

        $validated['interviewer_id'] = $request->user()->id;

        InterviewReport::create($validated);

        return redirect()->route('reports.index')->with('success', 'Compte-rendu créé.');
    }

    public function update(Request $request, InterviewReport $report)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'job_offer_id' => 'nullable|exists:job_offers,id',
            'interview_date' => 'required|date',
            'rating' => 'nullable|integer|min:1|max:5',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'notes' => 'nullable|string',
            'recommendation' => 'nullable|in:hire,maybe,reject',
        ]);

        $report->update($validated);

        return redirect()->back()->with('success', 'Compte-rendu mis à jour.');
    }

    public function destroy(InterviewReport $report)
    {
        $report->delete();

        return redirect()->route('reports.index')->with('success', 'Compte-rendu supprimé.');
    }
}

```

---

## `app/Http/Controllers/InterviewScriptController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\InterviewScript;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InterviewScriptController extends Controller
{
    public function index()
    {
        $scripts = InterviewScript::with('createdBy')
            ->latest()
            ->paginate(20);

        return Inertia::render('Scripts/Index', [
            'scripts' => $scripts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sections' => 'required|array|min:1',
            'sections.*.title' => 'required|string|max:255',
            'sections.*.content' => 'nullable|string',
        ]);

        $validated['created_by'] = $request->user()->id;

        InterviewScript::create($validated);

        return redirect()->route('scripts.index')->with('success', 'Script créé avec succès.');
    }

    public function show(InterviewScript $script)
    {
        $script->load('createdBy');

        return Inertia::render('Scripts/Show', [
            'script' => $script,
        ]);
    }

    public function update(Request $request, InterviewScript $script)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sections' => 'required|array|min:1',
            'sections.*.title' => 'required|string|max:255',
            'sections.*.content' => 'nullable|string',
        ]);

        $script->update($validated);

        return redirect()->back()->with('success', 'Script mis à jour.');
    }

    public function destroy(InterviewScript $script)
    {
        $script->delete();

        return redirect()->route('scripts.index')->with('success', 'Script supprimé.');
    }
}

```

---

## `app/Http/Controllers/JobOfferController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JobOfferController extends Controller
{
    public function index(Request $request)
    {
        $query = JobOffer::query();

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $offers = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('Offers/Index', [
            'offers' => $offers,
            'filters' => [
                'status' => $request->get('status', ''),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'contract_type' => 'nullable|in:CDI,CDD,Stage,Alternance,Interim',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'salary_range' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,active,archived',
        ]);

        $validated['created_by'] = $request->user()->id;

        if (($validated['status'] ?? 'draft') === 'active') {
            $validated['published_at'] = Carbon::now();
        }

        JobOffer::create($validated);

        return redirect()->route('offers.index')->with('success', 'Offre créée avec succès.');
    }

    public function show(JobOffer $jobOffer)
    {
        $jobOffer->load([
            'interviewReports.candidate',
            'interviewEvents.candidate',
            'createdBy',
        ]);

        return Inertia::render('Offers/Show', [
            'offer' => $jobOffer,
        ]);
    }

    public function update(Request $request, JobOffer $jobOffer)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'contract_type' => 'nullable|in:CDI,CDD,Stage,Alternance,Interim',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'salary_range' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,active,archived',
        ]);

        if (($validated['status'] ?? $jobOffer->status) === 'active' && !$jobOffer->published_at) {
            $validated['published_at'] = Carbon::now();
        }

        $jobOffer->update($validated);

        return redirect()->back()->with('success', 'Offre mise à jour.');
    }

    public function archive(JobOffer $jobOffer)
    {
        $jobOffer->update([
            'status' => 'archived',
            'archived_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Offre archivée.');
    }

    public function destroy(JobOffer $jobOffer)
    {
        $jobOffer->delete();

        return redirect()->route('offers.index')->with('success', 'Offre supprimée.');
    }
}

```

---

## `app/Http/Controllers/JobPositionController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\JobPosition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JobPositionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:job_positions,name'],
        ]);

        JobPosition::create(['name' => $data['name'], 'is_default' => false]);

        return back()->with('success', 'Métier ajouté');
    }

    public function destroy(JobPosition $jobPosition): RedirectResponse
    {
        if ($jobPosition->is_default) {
            return back()->with('error', 'Impossible de supprimer un métier par défaut.');
        }
        $jobPosition->delete();
        return back()->with('success', 'Métier supprimé');
    }
}

```

---

## `app/Http/Controllers/ProfileController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

```

---

## `app/Http/Controllers/RecruitmentCampaignController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use App\Models\RecruitmentCampaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RecruitmentCampaignController extends Controller
{
    public function index(): Response
    {
        $campaigns = RecruitmentCampaign::withCount('candidates')
            ->with('jobOffer:id,title')
            ->orderByDesc('created_at')
            ->get();

        $offers = JobOffer::where('status', 'active')->orderBy('title')->get(['id', 'title']);

        return Inertia::render('Campaigns/Index', [
            'campaigns' => $campaigns,
            'offers' => $offers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'job_offer_id' => ['nullable', 'exists:job_offers,id'],
        ]);

        RecruitmentCampaign::create($data);

        return back()->with('success', 'Campagne créée');
    }

    public function update(Request $request, RecruitmentCampaign $campaign): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'job_offer_id' => ['nullable', 'exists:job_offers,id'],
            'status' => ['nullable', 'in:active,closed'],
        ]);

        $campaign->update($data);

        return back()->with('success', 'Campagne mise à jour');
    }

    public function destroy(RecruitmentCampaign $campaign): RedirectResponse
    {
        $campaign->delete();
        return back()->with('success', 'Campagne supprimée');
    }
}

```

---

## `app/Http/Controllers/RecruitmentDashboardController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CvAnalysis;
use App\Models\InterviewEvent;
use App\Models\JobOffer;
use Carbon\Carbon;
use Inertia\Inertia;

class RecruitmentDashboardController extends Controller
{
    public function __invoke()
    {
        $now = Carbon::now();
        $weekStart = $now->copy()->startOfWeek();
        $weekEnd = $now->copy()->endOfWeek();

        $stats = [
            'total_candidates' => Candidate::count(),
            'active_offers' => JobOffer::where('status', 'active')->count(),
            'interviews_this_week' => InterviewEvent::whereBetween('scheduled_at', [$weekStart, $weekEnd])->count(),
            'analyses_saved' => CvAnalysis::count(),
        ];

        $recentCandidates = Candidate::latest()
            ->take(5)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'full_name' => $c->full_name,
                'email' => $c->email,
                'status' => $c->status,
                'created_at' => $c->created_at->format('d/m/Y'),
            ]);

        $upcomingInterviews = InterviewEvent::with(['candidate', 'jobOffer'])
            ->where('scheduled_at', '>=', $now)
            ->where('status', 'scheduled')
            ->orderBy('scheduled_at')
            ->take(5)
            ->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'title' => $e->title,
                'candidate_name' => $e->candidate->full_name,
                'job_offer_title' => $e->jobOffer?->title,
                'scheduled_at' => $e->scheduled_at->format('d/m/Y H:i'),
                'location' => $e->location,
            ]);

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentCandidates' => $recentCandidates,
            'upcomingInterviews' => $upcomingInterviews,
        ]);
    }
}

```

---

## `app/Http/Controllers/Auth/AuthenticatedSessionController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

```

---

## `app/Http/Controllers/Auth/ConfirmablePasswordController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view.
     */
    public function show(): Response
    {
        return Inertia::render('Auth/ConfirmPassword');
    }

    /**
     * Confirm the user's password.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('dashboard', absolute: false));
    }
}

```

---

## `app/Http/Controllers/Auth/EmailVerificationNotificationController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}

```

---

## `app/Http/Controllers/Auth/EmailVerificationPromptController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|Response
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : Inertia::render('Auth/VerifyEmail', ['status' => session('status')]);
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

## `app/Http/Controllers/Auth/NewPasswordController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}

```

---

## `app/Http/Controllers/Auth/PasswordController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back();
    }
}

```

---

## `app/Http/Controllers/Auth/PasswordResetLinkController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
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
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}

```

---

## `app/Http/Controllers/Auth/VerifyEmailController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}

```

---

# Middleware

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

# Migrations

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
            $table->boolean('must_change_password')->default(false);
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

## `database/migrations/2026_05_25_100000_create_recruitment_tables.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->string('status', 20)->default('a_analyser'); // a_analyser / selectionne / rejete
            $table->string('source')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('cv_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->string('original_name');
            $table->string('file_path');
            $table->integer('file_size');
            $table->string('mime_type');
            $table->timestamps();
        });

        Schema::create('cv_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cv_document_id')->nullable()->constrained()->nullOnDelete();
            $table->longText('analysis');
            $table->text('prompt_used')->nullable();
            $table->string('model_used')->nullable();
            $table->timestamps();
        });

        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('department')->nullable();
            $table->string('location')->nullable();
            $table->string('contract_type')->nullable(); // CDI, CDD, Stage, Alternance, Interim
            $table->longText('description');
            $table->longText('requirements')->nullable();
            $table->string('salary_range')->nullable();
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('interview_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_offer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('interviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('interview_date');
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->longText('notes')->nullable();
            $table->string('recommendation')->nullable(); // hire, maybe, reject
            $table->timestamps();
        });

        Schema::create('interview_scripts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('sections');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('interview_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_offer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('interviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interview_events');
        Schema::dropIfExists('interview_scripts');
        Schema::dropIfExists('interview_reports');
        Schema::dropIfExists('job_offers');
        Schema::dropIfExists('cv_analyses');
        Schema::dropIfExists('cv_documents');
        Schema::dropIfExists('candidates');
    }
};

```

---

## `database/migrations/2026_05_25_100100_create_email_tables.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->longText('body');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('sent_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('email_template_id')->nullable()->constrained()->nullOnDelete();
            $table->string('to_email');
            $table->string('subject');
            $table->longText('body');
            $table->string('status')->default('sent'); // sent / failed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sent_emails');
        Schema::dropIfExists('email_templates');
    }
};

```

---

## `database/migrations/2026_05_25_100200_create_campaigns_and_update_candidates.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recruitment_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('job_offer_id')->nullable()->constrained('job_offers')->nullOnDelete();
            $table->string('status', 20)->default('active'); // active / closed
            $table->timestamps();
        });

        Schema::table('candidates', function (Blueprint $table) {
            $table->foreignId('campaign_id')->nullable()->after('id')
                ->constrained('recruitment_campaigns')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campaign_id');
        });
        Schema::dropIfExists('recruitment_campaigns');
    }
};

```

---

## `database/migrations/2026_05_25_100300_create_job_positions_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Métiers par défaut
        $defaults = [
            'Chirurgien-Dentiste',
            'Assistant(e) Dentaire',
            'Assistant(e) Administratif',
            'Office Manager',
            'Community Manager',
        ];
        foreach ($defaults as $name) {
            DB::table('job_positions')->insert([
                'name' => $name,
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Ajouter une colonne job_position_id aux candidats
        Schema::table('candidates', function (Blueprint $table) {
            $table->foreignId('job_position_id')->nullable()->after('campaign_id')
                ->constrained('job_positions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('job_position_id');
        });
        Schema::dropIfExists('job_positions');
    }
};

```

---

# Seeders

## `database/seeders/DatabaseSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Volontairement vide. L'application de recrutement ne nécessite
        // pas de données initiales : tout se crée depuis l'interface.
    }
}

```

---

# Vues Blade

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

# Composants Vue

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
 * Logo OSCD Recrutement — emoji sur fond teal.
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
                OSCD<br />
                Recrutement
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

# Layouts Vue

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
                                    aria-label="OSCD Recrutement — Tableau de bord">
                                    <BrandLogo variant="mark" size="sm" />
                                    <span class="hidden sm:block">
                                        <span class="block text-sm font-bold uppercase tracking-wide text-gray-900 dark:text-brand-cream">OSCD</span>
                                        <span class="block text-[10px] uppercase tracking-[0.2em] text-brand-primary">Recrutement</span>
                                    </span>
                                </Link>
                            </div>

                            <!-- Liens navigation -->
                            <div class="hidden space-x-4 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">Tableau de bord</NavLink>
                                <NavLink :href="route('campaigns.index')" :active="route().current('campaigns.*')">Campagnes</NavLink>
                                <NavLink :href="route('candidates.index')" :active="route().current('candidates.*')">CVth&egrave;que</NavLink>
                                <NavLink :href="route('analyses.index')" :active="route().current('analyses.*')">Analyses IA</NavLink>
                                <NavLink :href="route('offers.index')" :active="route().current('offers.*')">Offres</NavLink>
                                <NavLink :href="route('reports.index')" :active="route().current('reports.*')">Comptes-rendus</NavLink>
                                <NavLink :href="route('scripts.index')" :active="route().current('scripts.*')">Scripts</NavLink>
                                <NavLink :href="route('emails.index')" :active="route().current('emails.*')">Emails</NavLink>
                                <NavLink :href="route('agenda.index')" :active="route().current('agenda.*')">Agenda</NavLink>
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
                        <ResponsiveNavLink :href="route('campaigns.index')" :active="route().current('campaigns.*')">Campagnes</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('candidates.index')" :active="route().current('candidates.*')">CVth&egrave;que</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('analyses.index')" :active="route().current('analyses.*')">Analyses IA</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('offers.index')" :active="route().current('offers.*')">Offres</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('reports.index')" :active="route().current('reports.*')">Comptes-rendus</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('scripts.index')" :active="route().current('scripts.*')">Scripts</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('emails.index')" :active="route().current('emails.*')">Emails</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('agenda.index')" :active="route().current('agenda.*')">Agenda</ResponsiveNavLink>
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

# Pages Vue

## `resources/js/Pages/Dashboard.vue`

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: { type: Object, required: true },
    recentCandidates: { type: Array, default: () => [] },
    upcomingInterviews: { type: Array, default: () => [] },
});

const page = usePage();
const userName = computed(() => page.props.auth.user?.name || '');

const statusColor = (status) => {
    const map = {
        new: 'info',
        screening: 'warn',
        interview: 'warn',
        offer: 'ok',
        hired: 'ok',
        rejected: 'danger',
    };
    return map[status] || 'info';
};

const statusLabel = (status) => {
    const map = {
        new: 'Nouveau',
        screening: 'Tri',
        interview: 'Entretien',
        offer: 'Offre',
        hired: 'Embauché',
        rejected: 'Refusé',
    };
    return map[status] || status;
};

const statCards = computed(() => [
    {
        label: 'Candidats',
        value: props.stats.total_candidates,
        bgCard: 'bg-brand-tertiary',
        bgIcon: 'bg-white/70',
        iconColor: 'text-teal-700',
        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
    },
    {
        label: 'Offres actives',
        value: props.stats.active_offers,
        bgCard: 'bg-brand-lavender',
        bgIcon: 'bg-white/70',
        iconColor: 'text-violet-700',
        icon: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    },
    {
        label: 'Entretiens cette semaine',
        value: props.stats.interviews_this_week,
        bgCard: 'bg-brand-peach',
        bgIcon: 'bg-white/70',
        iconColor: 'text-orange-700',
        icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
    },
    {
        label: 'Analyses IA',
        value: props.stats.analyses_saved,
        bgCard: 'bg-brand-sky',
        bgIcon: 'bg-white/70',
        iconColor: 'text-sky-700',
        icon: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
    },
]);
</script>

<template>
    <Head title="Tableau de bord" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-brand-primary">
                    OSCD Recrutement
                </span>
                <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">
                    Bonjour {{ userName }}
                </h2>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Tableau de bord du recrutement
                </span>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

                <!-- Statistiques -->
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    <div v-for="(c, i) in statCards" :key="i"
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

                <div class="grid gap-6 lg:grid-cols-2">
                    <!-- Derniers candidats -->
                    <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900 dark:text-gray-100">
                                <span class="grid h-8 w-8 place-items-center rounded-lg bg-brand-tertiary text-brand-primary">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </span>
                                Derniers candidats
                            </h3>
                            <Link :href="route('candidates.index')"
                                class="text-sm font-medium text-brand-primary transition hover:text-brand-primary-dark hover:underline">
                                Voir tout
                            </Link>
                        </div>
                        <ul v-if="recentCandidates.length" class="divide-y divide-gray-100 dark:divide-gray-700">
                            <li v-for="c in recentCandidates" :key="c.id"
                                class="group flex items-center justify-between py-3 text-sm transition hover:bg-brand-tertiary/40 rounded -mx-2 px-2">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-9 w-9 place-items-center rounded-full bg-brand-tertiary text-xs font-bold uppercase text-brand-primary">
                                        {{ c.full_name.split(' ').map(w => w[0]).slice(0, 2).join('') }}
                                    </span>
                                    <div>
                                        <Link :href="route('candidates.show', c.id)"
                                            class="font-medium text-gray-900 group-hover:text-brand-primary dark:text-gray-100">
                                            {{ c.full_name }}
                                        </Link>
                                        <div class="text-xs text-gray-500">{{ c.email || 'Pas d\'email' }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-500">{{ c.created_at }}</span>
                                    <StatusBadge :label="statusLabel(c.status)" :cls="statusColor(c.status)" />
                                </div>
                            </li>
                        </ul>
                        <div v-else class="py-8 text-center">
                            <p class="text-sm italic text-gray-500">Aucun candidat pour le moment</p>
                        </div>
                    </div>

                    <!-- Prochains entretiens -->
                    <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900 dark:text-gray-100">
                                <span class="grid h-8 w-8 place-items-center rounded-lg bg-brand-peach text-orange-700">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                Prochains entretiens
                            </h3>
                            <Link :href="route('agenda.index')"
                                class="text-sm font-medium text-brand-primary transition hover:text-brand-primary-dark hover:underline">
                                Voir l'agenda
                            </Link>
                        </div>
                        <ul v-if="upcomingInterviews.length" class="divide-y divide-gray-100 dark:divide-gray-700">
                            <li v-for="e in upcomingInterviews" :key="e.id"
                                class="flex items-center justify-between py-3 text-sm rounded -mx-2 px-2 hover:bg-brand-peach/30 transition">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ e.title }}</div>
                                    <div class="text-xs text-gray-500">{{ e.candidate_name }} <span v-if="e.job_offer_title">- {{ e.job_offer_title }}</span></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-medium text-gray-700">{{ e.scheduled_at }}</div>
                                    <div v-if="e.location" class="text-xs text-gray-500">{{ e.location }}</div>
                                </div>
                            </li>
                        </ul>
                        <div v-else class="py-8 text-center">
                            <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-emerald-100 text-emerald-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="mt-3 text-sm italic text-gray-500">Aucun entretien planifié</p>
                        </div>
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

## `resources/js/Pages/Auth/ConfirmPassword.vue`

```vue
<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirm Password" />

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            This is a secure area of the application. Please confirm your
            password before continuing.
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    autofocus
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 flex justify-end">
                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Confirm
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
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

## `resources/js/Pages/Auth/ForgotPassword.vue`

```vue
<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Forgot your password? No problem. Just let us know your email
            address and we will email you a password reset link that will allow
            you to choose a new one.
        </div>

        <div
            v-if="status"
            class="mb-4 text-sm font-medium text-green-600 dark:text-green-400"
        >
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

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Email Password Reset Link
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

## `resources/js/Pages/Auth/Register.vue`

```vue
<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
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
                <InputLabel for="email" value="Email" />
                <TextInput id="email" type="email" class="mt-1 block w-full"
                    v-model="form.email" required autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
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

## `resources/js/Pages/Auth/ResetPassword.vue`

```vue
<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

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
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Reset Password
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

```

---

## `resources/js/Pages/Auth/VerifyEmail.vue`

```vue
<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Thanks for signing up! Before getting started, could you verify your
            email address by clicking on the link we just emailed to you? If you
            didn't receive the email, we will gladly send you another.
        </div>

        <div
            class="mb-4 text-sm font-medium text-green-600 dark:text-green-400"
            v-if="verificationLinkSent"
        >
            A new verification link has been sent to the email address you
            provided during registration.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Resend Verification Email
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                    >Log Out</Link
                >
            </div>
        </form>
    </GuestLayout>
</template>

```

---

## `resources/js/Pages/Agenda/Index.vue`

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    events: { type: Object, required: true },
    candidates: { type: Array, default: () => [] },
    offers: { type: Array, default: () => [] },
    interviewers: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const showModal = ref(false);

const form = useForm({
    candidate_id: '',
    job_offer_id: '',
    interviewer_id: '',
    title: '',
    scheduled_at: '',
    duration_minutes: 60,
    location: '',
    notes: '',
});

const statusColor = (status) => {
    const map = { scheduled: 'info', completed: 'ok', cancelled: 'danger' };
    return map[status] || 'info';
};

const statusLabel = (status) => {
    const map = { scheduled: 'Planifié', completed: 'Terminé', cancelled: 'Annulé' };
    return map[status] || status;
};

// Group events by date
const groupedEvents = computed(() => {
    const groups = {};
    for (const event of props.events.data) {
        const date = new Date(event.scheduled_at).toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        if (!groups[date]) groups[date] = [];
        groups[date].push(event);
    }
    return groups;
});

const submit = () => {
    form.post(route('agenda.store'), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
            form.duration_minutes = 60;
        },
    });
};

const cancel = (id) => {
    if (confirm('Annuler cet entretien ?')) {
        router.post(route('agenda.destroy', id));
    }
};
</script>

<template>
    <Head title="Agenda" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">Agenda des entretiens</h2>
                <PrimaryButton @click="showModal = true">+ Planifier un entretien</PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- Liste groupée par jour -->
                <div v-if="Object.keys(groupedEvents).length" class="space-y-6">
                    <div v-for="(dayEvents, date) in groupedEvents" :key="date">
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 mb-3 capitalize">{{ date }}</h3>
                        <div class="space-y-3">
                            <div v-for="e in dayEvents" :key="e.id"
                                class="flex items-center justify-between rounded-xl bg-white p-4 shadow-soft dark:bg-gray-800 transition hover:shadow-md">
                                <div class="flex items-center gap-4">
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-brand-primary">
                                            {{ new Date(e.scheduled_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ e.duration_minutes }} min</div>
                                    </div>
                                    <div class="border-l border-gray-200 pl-4 dark:border-gray-700">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ e.title }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            <span v-if="e.candidate">{{ e.candidate.first_name }} {{ e.candidate.last_name }}</span>
                                            <span v-if="e.job_offer"> - {{ e.job_offer.title }}</span>
                                        </div>
                                        <div v-if="e.location" class="text-xs text-gray-400 mt-0.5">{{ e.location }}</div>
                                        <div v-if="e.interviewer" class="text-xs text-gray-400">Interviewer : {{ e.interviewer.name }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <StatusBadge :label="statusLabel(e.status)" :cls="statusColor(e.status)" />
                                    <button v-if="e.status === 'scheduled'" @click="cancel(e.id)"
                                        class="text-xs text-pink-600 hover:underline">Annuler</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="rounded-xl bg-white p-8 shadow-soft text-center dark:bg-gray-800">
                    <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-brand-peach text-orange-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="mt-3 text-sm italic text-gray-500">Aucun entretien planifié</p>
                </div>

                <!-- Pagination -->
                <div v-if="events.links && events.links.length > 3" class="mt-6 flex justify-center gap-1">
                    <template v-for="link in events.links" :key="link.label">
                        <Link v-if="link.url" :href="link.url"
                            class="rounded px-3 py-1 text-sm transition"
                            :class="link.active ? 'bg-brand-primary text-white' : 'bg-white text-gray-700 hover:bg-brand-tertiary'"
                            v-html="link.label" />
                        <span v-else class="rounded px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>

        <!-- Modal planification -->
        <Modal :show="showModal" @close="showModal = false" max-width="2xl">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Planifier un entretien</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <InputLabel value="Titre *" />
                            <TextInput v-model="form.title" class="mt-1 w-full" required placeholder="Ex: Entretien technique" />
                            <InputError :message="form.errors.title" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel value="Candidat *" />
                            <select v-model="form.candidate_id" required
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">Sélectionner</option>
                                <option v-for="c in candidates" :key="c.id" :value="c.id">{{ c.first_name }} {{ c.last_name }}</option>
                            </select>
                            <InputError :message="form.errors.candidate_id" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel value="Offre d'emploi" />
                            <select v-model="form.job_offer_id"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">Aucune</option>
                                <option v-for="o in offers" :key="o.id" :value="o.id">{{ o.title }}</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel value="Date et heure *" />
                            <TextInput v-model="form.scheduled_at" type="datetime-local" class="mt-1 w-full" required />
                            <InputError :message="form.errors.scheduled_at" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel value="Durée (minutes)" />
                            <TextInput v-model.number="form.duration_minutes" type="number" min="15" max="480" class="mt-1 w-full" />
                        </div>
                        <div>
                            <InputLabel value="Lieu" />
                            <TextInput v-model="form.location" class="mt-1 w-full" placeholder="Salle, visio..." />
                        </div>
                        <div>
                            <InputLabel value="Interviewer" />
                            <select v-model="form.interviewer_id"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">Sélectionner</option>
                                <option v-for="u in interviewers" :key="u.id" :value="u.id">{{ u.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <InputLabel value="Notes" />
                        <textarea v-model="form.notes" rows="2"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                    </div>
                    <div class="flex justify-end gap-3">
                        <SecondaryButton @click="showModal = false">Annuler</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">Planifier</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

```

---

## `resources/js/Pages/Analyses/Index.vue`

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    analyses: { type: Object, required: true },
    candidates: { type: Array, default: () => [] },
    apiKeyConfigured: { type: Boolean, default: false },
});

const showModal = ref(false);

const form = useForm({
    candidate_id: '',
    cv_document_id: '',
    prompt: '',
});

const submit = () => {
    form.post(route('analyses.store'), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
};

const destroy = (id) => {
    if (confirm('Supprimer cette analyse ?')) {
        router.post(route('analyses.destroy', id));
    }
};
</script>

<template>
    <Head title="Analyses IA" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">Analyses IA</h2>
                <PrimaryButton @click="showModal = true">+ Nouvelle analyse</PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Avertissement API key -->
                <div v-if="!apiKeyConfigured" class="rounded-xl bg-brand-peach p-4 shadow-soft">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-orange-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <p class="text-sm font-medium text-orange-800">
                            Configurez ANTHROPIC_API_KEY pour activer l'analyse IA
                        </p>
                    </div>
                </div>

                <!-- Liste des analyses -->
                <div class="space-y-4">
                    <div v-for="a in analyses.data" :key="a.id"
                        class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                    {{ a.candidate?.first_name }} {{ a.candidate?.last_name }}
                                </h4>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ new Date(a.created_at).toLocaleDateString('fr-FR') }}
                                    <span v-if="a.model_used"> - Modèle : {{ a.model_used }}</span>
                                    <span v-if="a.cv_document"> - CV : {{ a.cv_document.original_name }}</span>
                                </div>
                            </div>
                            <button @click="destroy(a.id)" class="text-sm text-pink-600 hover:underline">Supprimer</button>
                        </div>
                        <div class="rounded-lg bg-brand-tertiary/50 p-4">
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ a.analysis }}</p>
                        </div>
                        <div v-if="a.prompt_used" class="mt-3">
                            <details class="text-xs text-gray-500">
                                <summary class="cursor-pointer hover:text-brand-primary">Voir le prompt utilisé</summary>
                                <p class="mt-2 rounded bg-gray-50 p-2 dark:bg-gray-700">{{ a.prompt_used }}</p>
                            </details>
                        </div>
                    </div>

                    <div v-if="!analyses.data.length" class="rounded-xl bg-white p-8 shadow-soft text-center dark:bg-gray-800">
                        <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-brand-sky text-sky-700">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm italic text-gray-500">Aucune analyse sauvegardée</p>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="analyses.links && analyses.links.length > 3" class="flex justify-center gap-1">
                    <template v-for="link in analyses.links" :key="link.label">
                        <a v-if="link.url" :href="link.url"
                            class="rounded px-3 py-1 text-sm transition"
                            :class="link.active ? 'bg-brand-primary text-white' : 'bg-white text-gray-700 hover:bg-brand-tertiary'"
                            v-html="link.label" />
                        <span v-else class="rounded px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>

        <!-- Modal nouvelle analyse -->
        <Modal :show="showModal" @close="showModal = false">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Lancer une analyse IA</h3>

                <div v-if="!apiKeyConfigured" class="mb-4 rounded-lg bg-brand-peach p-3">
                    <p class="text-sm text-orange-800">Configurez ANTHROPIC_API_KEY pour activer l'analyse IA</p>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <InputLabel value="Candidat *" />
                        <select v-model="form.candidate_id" required
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            <option value="">Sélectionner un candidat</option>
                            <option v-for="c in candidates" :key="c.id" :value="c.id">
                                {{ c.first_name }} {{ c.last_name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.candidate_id" class="mt-1" />
                    </div>

                    <div>
                        <InputLabel value="Prompt personnalisé (optionnel)" />
                        <textarea v-model="form.prompt" rows="4" placeholder="Laissez vide pour utiliser le prompt par défaut..."
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                        <InputError :message="form.errors.prompt" class="mt-1" />
                    </div>

                    <InputError :message="form.errors.api" class="mt-1" />

                    <div class="flex justify-end gap-3">
                        <SecondaryButton @click="showModal = false">Annuler</SecondaryButton>
                        <PrimaryButton :disabled="form.processing || !apiKeyConfigured">
                            Analyser avec Claude
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

```

---

## `resources/js/Pages/Campaigns/Index.vue`

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    campaigns: { type: Array, default: () => [] },
    offers: { type: Array, default: () => [] },
});

const showModal = ref(false);
const editingCampaign = ref(null);

const form = useForm({
    title: '',
    description: '',
    job_offer_id: '',
    status: 'active',
});

const openCreate = () => {
    editingCampaign.value = null;
    form.reset();
    form.status = 'active';
    showModal.value = true;
};

const openEdit = (c) => {
    editingCampaign.value = c;
    form.title = c.title;
    form.description = c.description || '';
    form.job_offer_id = c.job_offer_id || '';
    form.status = c.status;
    showModal.value = true;
};

const submit = () => {
    const url = editingCampaign.value
        ? route('campaigns.update', editingCampaign.value.id)
        : route('campaigns.store');
    form.post(url, {
        onSuccess: () => { showModal.value = false; form.reset(); },
    });
};

const destroy = (c) => {
    if (!confirm(`Supprimer la campagne « ${c.title} » et dissocier ses candidats ?`)) return;
    useForm({}).post(route('campaigns.destroy', c.id));
};
</script>

<template>
    <Head title="Campagnes de recrutement" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Campagnes de recrutement
                </h2>
                <PrimaryButton @click="openCreate">+ Nouvelle campagne</PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
                <div v-for="c in campaigns" :key="c.id"
                    class="overflow-hidden rounded-xl bg-white p-5 shadow-soft dark:bg-gray-800 transition hover:shadow-md">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-bold text-gray-900">{{ c.title }}</h3>
                                <StatusBadge
                                    :label="c.status === 'active' ? 'Active' : 'Clôturée'"
                                    :cls="c.status === 'active' ? 'ok' : 'info'" />
                            </div>
                            <p v-if="c.description" class="mt-1 text-sm text-gray-600">{{ c.description }}</p>
                            <div class="mt-2 flex flex-wrap gap-4 text-xs text-gray-500">
                                <span v-if="c.job_offer">Offre : <strong>{{ c.job_offer.title }}</strong></span>
                                <span>{{ c.candidates_count }} candidat{{ c.candidates_count > 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <Link :href="route('candidates.index') + '?campaign=' + c.id"
                                class="text-sm text-brand-primary hover:underline">Voir candidats</Link>
                            <button class="text-sm text-brand-primary hover:underline" @click="openEdit(c)">Modifier</button>
                            <button class="text-sm text-red-600 hover:underline" @click="destroy(c)">Supprimer</button>
                        </div>
                    </div>
                </div>

                <div v-if="!campaigns.length" class="py-16 text-center text-sm italic text-gray-500">
                    Aucune campagne de recrutement. Créez-en une pour regrouper vos candidats.
                </div>
            </div>
        </div>

        <Modal :show="showModal" max-width="lg" @close="showModal = false">
            <form class="p-6" @submit.prevent="submit">
                <h3 class="text-lg font-semibold text-gray-900">
                    {{ editingCampaign ? 'Modifier la campagne' : 'Nouvelle campagne' }}
                </h3>
                <div class="mt-4 space-y-4">
                    <div>
                        <InputLabel value="Titre de la campagne" />
                        <TextInput v-model="form.title" type="text" class="mt-1 block w-full" required
                            placeholder="ex : Recrutement assistante dentaire juin 2025" />
                        <InputError :message="form.errors.title" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Offre d'emploi associée (optionnel)" />
                        <select v-model="form.job_offer_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary">
                            <option value="">— aucune —</option>
                            <option v-for="o in offers" :key="o.id" :value="o.id">{{ o.title }}</option>
                        </select>
                    </div>
                    <div>
                        <InputLabel value="Description (optionnel)" />
                        <textarea v-model="form.description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary" />
                    </div>
                    <div v-if="editingCampaign">
                        <InputLabel value="Statut" />
                        <select v-model="form.status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary">
                            <option value="active">Active</option>
                            <option value="closed">Clôturée</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <SecondaryButton type="button" @click="showModal = false">Annuler</SecondaryButton>
                    <PrimaryButton :disabled="form.processing">
                        {{ editingCampaign ? 'Enregistrer' : 'Créer' }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>

```

---

## `resources/js/Pages/Candidates/Index.vue`

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    candidates: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const showModal = ref(false);
const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    city: '',
    source: '',
    notes: '',
});

const statuses = [
    { value: '', label: 'Tous les statuts' },
    { value: 'new', label: 'Nouveau' },
    { value: 'screening', label: 'Tri' },
    { value: 'interview', label: 'Entretien' },
    { value: 'offer', label: 'Offre' },
    { value: 'hired', label: 'Embauché' },
    { value: 'rejected', label: 'Refusé' },
];

const statusColor = (status) => {
    const map = { new: 'info', screening: 'warn', interview: 'warn', offer: 'ok', hired: 'ok', rejected: 'danger' };
    return map[status] || 'info';
};

const statusLabel = (status) => {
    const map = { new: 'Nouveau', screening: 'Tri', interview: 'Entretien', offer: 'Offre', hired: 'Embauché', rejected: 'Refusé' };
    return map[status] || status;
};

let searchTimeout = null;
watch([search, statusFilter], () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('candidates.index'), {
            search: search.value || undefined,
            status: statusFilter.value || undefined,
        }, { preserveState: true, replace: true });
    }, 300);
});

const submit = () => {
    form.post(route('candidates.store'), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
};

const destroy = (id) => {
    if (confirm('Supprimer ce candidat ?')) {
        router.post(route('candidates.destroy', id));
    }
};
</script>

<template>
    <Head title="CVthèque" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">CVthèque</h2>
                <PrimaryButton @click="showModal = true">+ Ajouter un candidat</PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Filtres -->
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center">
                    <TextInput v-model="search" placeholder="Rechercher un candidat..." class="w-full sm:w-80" />
                    <select v-model="statusFilter"
                        class="rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                    </select>
                </div>

                <!-- Tableau -->
                <div class="overflow-hidden rounded-xl bg-white shadow-soft dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-brand-tertiary/50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Téléphone</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="c in candidates.data" :key="c.id" class="hover:bg-brand-tertiary/30 transition">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ c.first_name }} {{ c.last_name }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ c.email || '—' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ c.phone || '—' }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <StatusBadge :label="statusLabel(c.status)" :cls="statusColor(c.status)" />
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ new Date(c.created_at).toLocaleDateString('fr-FR') }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm space-x-2">
                                    <Link :href="route('candidates.show', c.id)" class="text-brand-primary hover:underline">Voir</Link>
                                    <button @click="destroy(c.id)" class="text-pink-600 hover:underline">Supprimer</button>
                                </td>
                            </tr>
                            <tr v-if="!candidates.data.length">
                                <td colspan="6" class="px-6 py-8 text-center text-sm italic text-gray-500">Aucun candidat trouvé</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="candidates.links && candidates.links.length > 3" class="mt-4 flex justify-center gap-1">
                    <template v-for="link in candidates.links" :key="link.label">
                        <Link v-if="link.url" :href="link.url"
                            class="rounded px-3 py-1 text-sm transition"
                            :class="link.active ? 'bg-brand-primary text-white' : 'bg-white text-gray-700 hover:bg-brand-tertiary'"
                            v-html="link.label" />
                        <span v-else class="rounded px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>

        <!-- Modal ajout -->
        <Modal :show="showModal" @close="showModal = false">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Ajouter un candidat</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <InputLabel for="first_name" value="Prénom *" />
                            <TextInput id="first_name" v-model="form.first_name" class="mt-1 w-full" required />
                            <InputError :message="form.errors.first_name" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel for="last_name" value="Nom *" />
                            <TextInput id="last_name" v-model="form.last_name" class="mt-1 w-full" required />
                            <InputError :message="form.errors.last_name" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel for="email" value="Email" />
                            <TextInput id="email" v-model="form.email" type="email" class="mt-1 w-full" />
                            <InputError :message="form.errors.email" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel for="phone" value="Téléphone" />
                            <TextInput id="phone" v-model="form.phone" class="mt-1 w-full" />
                            <InputError :message="form.errors.phone" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel for="city" value="Ville" />
                            <TextInput id="city" v-model="form.city" class="mt-1 w-full" />
                            <InputError :message="form.errors.city" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel for="source" value="Source" />
                            <TextInput id="source" v-model="form.source" class="mt-1 w-full" placeholder="LinkedIn, Indeed..." />
                            <InputError :message="form.errors.source" class="mt-1" />
                        </div>
                    </div>
                    <div>
                        <InputLabel for="notes" value="Notes" />
                        <textarea id="notes" v-model="form.notes" rows="3"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                        <InputError :message="form.errors.notes" class="mt-1" />
                    </div>
                    <div class="flex justify-end gap-3">
                        <SecondaryButton @click="showModal = false">Annuler</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">Ajouter</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

```

---

## `resources/js/Pages/Candidates/Show.vue`

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    candidate: { type: Object, required: true },
});

const editing = ref(false);

const form = useForm({
    first_name: props.candidate.first_name,
    last_name: props.candidate.last_name,
    email: props.candidate.email || '',
    phone: props.candidate.phone || '',
    city: props.candidate.city || '',
    status: props.candidate.status,
    source: props.candidate.source || '',
    notes: props.candidate.notes || '',
});

const cvForm = useForm({ cv_file: null });
const fileInput = ref(null);

const statuses = [
    { value: 'new', label: 'Nouveau' },
    { value: 'screening', label: 'Tri' },
    { value: 'interview', label: 'Entretien' },
    { value: 'offer', label: 'Offre' },
    { value: 'hired', label: 'Embauché' },
    { value: 'rejected', label: 'Refusé' },
];

const statusColor = (status) => {
    const map = { new: 'info', screening: 'warn', interview: 'warn', offer: 'ok', hired: 'ok', rejected: 'danger' };
    return map[status] || 'info';
};

const statusLabel = (status) => {
    const s = statuses.find(st => st.value === status);
    return s ? s.label : status;
};

const updateCandidate = () => {
    form.post(route('candidates.update', props.candidate.id), {
        onSuccess: () => { editing.value = false; },
    });
};

const changeStatus = (status) => {
    router.post(route('candidates.update', props.candidate.id), {
        ...form.data(),
        status,
    });
};

const uploadCv = () => {
    if (!fileInput.value?.files[0]) return;
    cvForm.cv_file = fileInput.value.files[0];
    cvForm.post(route('cv.store', props.candidate.id), {
        onSuccess: () => {
            cvForm.reset();
            if (fileInput.value) fileInput.value.value = '';
        },
    });
};

const deleteCv = (cvId) => {
    if (confirm('Supprimer ce CV ?')) {
        router.post(route('cv.destroy', cvId));
    }
};

const recommendationLabel = (r) => {
    const map = { hire: 'Embaucher', maybe: 'Peut-être', reject: 'Refuser' };
    return map[r] || r;
};

const recommendationColor = (r) => {
    const map = { hire: 'ok', maybe: 'warn', reject: 'danger' };
    return map[r] || 'info';
};
</script>

<template>
    <Head :title="`${candidate.first_name} ${candidate.last_name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <Link :href="route('candidates.index')" class="text-sm text-brand-primary hover:underline mb-1 inline-block">&larr; Retour à la CVthèque</Link>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">
                        {{ candidate.first_name }} {{ candidate.last_name }}
                    </h2>
                </div>
                <StatusBadge :label="statusLabel(candidate.status)" :cls="statusColor(candidate.status)" />
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- Info section -->
                <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Informations</h3>
                        <SecondaryButton v-if="!editing" @click="editing = true">Modifier</SecondaryButton>
                    </div>

                    <form v-if="editing" @submit.prevent="updateCandidate" class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <InputLabel value="Prénom *" />
                                <TextInput v-model="form.first_name" class="mt-1 w-full" required />
                                <InputError :message="form.errors.first_name" class="mt-1" />
                            </div>
                            <div>
                                <InputLabel value="Nom *" />
                                <TextInput v-model="form.last_name" class="mt-1 w-full" required />
                                <InputError :message="form.errors.last_name" class="mt-1" />
                            </div>
                            <div>
                                <InputLabel value="Email" />
                                <TextInput v-model="form.email" type="email" class="mt-1 w-full" />
                            </div>
                            <div>
                                <InputLabel value="Téléphone" />
                                <TextInput v-model="form.phone" class="mt-1 w-full" />
                            </div>
                            <div>
                                <InputLabel value="Ville" />
                                <TextInput v-model="form.city" class="mt-1 w-full" />
                            </div>
                            <div>
                                <InputLabel value="Source" />
                                <TextInput v-model="form.source" class="mt-1 w-full" />
                            </div>
                            <div>
                                <InputLabel value="Statut" />
                                <select v-model="form.status"
                                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <InputLabel value="Notes" />
                            <textarea v-model="form.notes" rows="3"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                        </div>
                        <div class="flex gap-3">
                            <PrimaryButton :disabled="form.processing">Enregistrer</PrimaryButton>
                            <SecondaryButton @click="editing = false">Annuler</SecondaryButton>
                        </div>
                    </form>

                    <div v-else class="grid grid-cols-1 gap-3 sm:grid-cols-2 text-sm">
                        <div><span class="font-medium text-gray-500">Email :</span> {{ candidate.email || '—' }}</div>
                        <div><span class="font-medium text-gray-500">Téléphone :</span> {{ candidate.phone || '—' }}</div>
                        <div><span class="font-medium text-gray-500">Ville :</span> {{ candidate.city || '—' }}</div>
                        <div><span class="font-medium text-gray-500">Source :</span> {{ candidate.source || '—' }}</div>
                        <div class="sm:col-span-2"><span class="font-medium text-gray-500">Notes :</span> {{ candidate.notes || '—' }}</div>
                    </div>
                </div>

                <!-- Changement de statut rapide -->
                <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Changer le statut</h3>
                    <div class="flex flex-wrap gap-2">
                        <button v-for="s in statuses" :key="s.value"
                            @click="changeStatus(s.value)"
                            :class="['rounded-full px-4 py-2 text-sm font-medium transition',
                                candidate.status === s.value
                                    ? 'bg-brand-primary text-white'
                                    : 'bg-brand-tertiary text-gray-700 hover:bg-brand-primary/20']">
                            {{ s.label }}
                        </button>
                    </div>
                </div>

                <!-- CV Section -->
                <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Documents CV</h3>

                    <div class="mb-4 flex items-center gap-3">
                        <input ref="fileInput" type="file" accept=".pdf,.doc,.docx" @change="uploadCv"
                            class="text-sm text-gray-500 file:mr-4 file:rounded-full file:border-0 file:bg-brand-primary file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-primary-dark" />
                        <InputError :message="cvForm.errors.cv_file" />
                    </div>

                    <ul v-if="candidate.cv_documents?.length" class="divide-y divide-gray-100 dark:divide-gray-700">
                        <li v-for="cv in candidate.cv_documents" :key="cv.id" class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ cv.original_name }}</div>
                                    <div class="text-xs text-gray-500">{{ (cv.file_size / 1024).toFixed(0) }} Ko - {{ new Date(cv.created_at).toLocaleDateString('fr-FR') }}</div>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a :href="route('cv.download', cv.id)" class="text-sm text-brand-primary hover:underline">Télécharger</a>
                                <button @click="deleteCv(cv.id)" class="text-sm text-pink-600 hover:underline">Supprimer</button>
                            </div>
                        </li>
                    </ul>
                    <p v-else class="text-sm italic text-gray-500">Aucun CV uploadé</p>
                </div>

                <!-- Analyses Section -->
                <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Analyses IA</h3>
                        <Link :href="route('analyses.index')" class="text-sm text-brand-primary hover:underline">Nouvelle analyse</Link>
                    </div>
                    <ul v-if="candidate.analyses?.length" class="space-y-3">
                        <li v-for="a in candidate.analyses" :key="a.id" class="rounded-lg border border-gray-100 p-4 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs text-gray-500">{{ new Date(a.created_at).toLocaleDateString('fr-FR') }} - {{ a.model_used || 'Claude' }}</span>
                                <span v-if="a.cv_document" class="text-xs text-brand-primary">CV: {{ a.cv_document.original_name }}</span>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap line-clamp-4">{{ a.analysis }}</p>
                        </li>
                    </ul>
                    <p v-else class="text-sm italic text-gray-500">Aucune analyse sauvegardée</p>
                </div>

                <!-- Interview Reports -->
                <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Comptes-rendus d'entretien</h3>
                    <ul v-if="candidate.interview_reports?.length" class="space-y-3">
                        <li v-for="r in candidate.interview_reports" :key="r.id" class="rounded-lg border border-gray-100 p-4 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ new Date(r.interview_date).toLocaleDateString('fr-FR') }}</span>
                                <div class="flex items-center gap-2">
                                    <span v-if="r.rating" class="text-sm text-yellow-600">{{ '★'.repeat(r.rating) }}{{ '☆'.repeat(5 - r.rating) }}</span>
                                    <StatusBadge v-if="r.recommendation" :label="recommendationLabel(r.recommendation)" :cls="recommendationColor(r.recommendation)" />
                                </div>
                            </div>
                            <div v-if="r.job_offer" class="text-xs text-gray-500 mb-1">Offre : {{ r.job_offer.title }}</div>
                            <div v-if="r.interviewer" class="text-xs text-gray-500 mb-2">Interviewer : {{ r.interviewer.name }}</div>
                            <p v-if="r.notes" class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">{{ r.notes }}</p>
                        </li>
                    </ul>
                    <p v-else class="text-sm italic text-gray-500">Aucun compte-rendu</p>
                </div>

                <!-- Scheduled Events -->
                <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Entretiens planifiés</h3>
                    <ul v-if="candidate.interview_events?.length" class="space-y-3">
                        <li v-for="e in candidate.interview_events" :key="e.id" class="flex items-center justify-between rounded-lg border border-gray-100 p-4 dark:border-gray-700">
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ e.title }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ new Date(e.scheduled_at).toLocaleString('fr-FR') }}
                                    <span v-if="e.location"> - {{ e.location }}</span>
                                </div>
                                <div v-if="e.job_offer" class="text-xs text-gray-500">Offre : {{ e.job_offer.title }}</div>
                            </div>
                            <StatusBadge
                                :label="e.status === 'scheduled' ? 'Planifié' : e.status === 'completed' ? 'Terminé' : 'Annulé'"
                                :cls="e.status === 'scheduled' ? 'info' : e.status === 'completed' ? 'ok' : 'danger'" />
                        </li>
                    </ul>
                    <p v-else class="text-sm italic text-gray-500">Aucun entretien planifié</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

```

---

## `resources/js/Pages/Emails/Index.vue`

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    templates: { type: Array, default: () => [] },
    sentEmails: { type: Array, default: () => [] },
});

const tab = ref('templates'); // 'templates' | 'history'

// --- Modèle d'email ---
const showTemplateModal = ref(false);
const editingTemplate = ref(null);

const templateForm = useForm({
    name: '',
    subject: '',
    body: '',
    description: '',
});

const openCreateTemplate = () => {
    editingTemplate.value = null;
    templateForm.reset();
    showTemplateModal.value = true;
};

const openEditTemplate = (t) => {
    editingTemplate.value = t;
    templateForm.name = t.name;
    templateForm.subject = t.subject;
    templateForm.body = t.body;
    templateForm.description = t.description || '';
    showTemplateModal.value = true;
};

const submitTemplate = () => {
    const url = editingTemplate.value
        ? route('emails.templates.update', editingTemplate.value.id)
        : route('emails.templates.store');
    templateForm.post(url, {
        onSuccess: () => { showTemplateModal.value = false; templateForm.reset(); },
    });
};

const destroyTemplate = (t) => {
    if (!confirm(`Supprimer le modèle « ${t.name} » ?`)) return;
    useForm({}).post(route('emails.templates.destroy', t.id));
};
</script>

<template>
    <Head title="Emails" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Emails
                </h2>
                <PrimaryButton @click="openCreateTemplate">+ Nouveau modèle</PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- Onglets -->
                <div class="flex gap-2">
                    <button @click="tab = 'templates'"
                        :class="['rounded-lg px-4 py-2 text-sm font-semibold transition',
                            tab === 'templates' ? 'bg-brand-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50']">
                        Modèles d'email ({{ templates.length }})
                    </button>
                    <button @click="tab = 'history'"
                        :class="['rounded-lg px-4 py-2 text-sm font-semibold transition',
                            tab === 'history' ? 'bg-brand-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50']">
                        Historique d'envoi ({{ sentEmails.length }})
                    </button>
                </div>

                <!-- Modèles -->
                <div v-if="tab === 'templates'" class="space-y-4">
                    <div v-for="t in templates" :key="t.id"
                        class="rounded-xl bg-white p-5 shadow-soft dark:bg-gray-800">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">{{ t.name }}</h3>
                                <p class="mt-1 text-sm text-gray-500">Objet : {{ t.subject }}</p>
                                <p v-if="t.description" class="mt-1 text-xs text-gray-400 italic">{{ t.description }}</p>
                            </div>
                            <div class="flex gap-2">
                                <button class="text-sm text-brand-primary hover:underline" @click="openEditTemplate(t)">Modifier</button>
                                <button class="text-sm text-red-600 hover:underline" @click="destroyTemplate(t)">Supprimer</button>
                            </div>
                        </div>
                        <div class="mt-3 rounded bg-gray-50 p-3 text-sm text-gray-700 whitespace-pre-wrap dark:bg-gray-900 dark:text-gray-300">
                            {{ t.body }}
                        </div>
                    </div>
                    <div v-if="!templates.length" class="py-10 text-center text-sm italic text-gray-500">
                        Aucun modèle d'email. Créez-en un pour commencer.
                    </div>
                </div>

                <!-- Historique -->
                <div v-if="tab === 'history'" class="overflow-hidden rounded-xl bg-white shadow-soft dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Candidat</th>
                                <th class="px-4 py-3">Objet</th>
                                <th class="px-4 py-3">Modèle</th>
                                <th class="px-4 py-3">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="e in sentEmails" :key="e.id" class="text-sm text-gray-700 dark:text-gray-200">
                                <td class="px-4 py-3">{{ e.sent_at }}</td>
                                <td class="px-4 py-3 font-medium">{{ e.candidate_name }}</td>
                                <td class="px-4 py-3">{{ e.subject }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ e.template_name || '—' }}</td>
                                <td class="px-4 py-3">
                                    <StatusBadge :label="e.status === 'sent' ? 'Envoyé' : 'Échec'" :cls="e.status === 'sent' ? 'ok' : 'danger'" />
                                </td>
                            </tr>
                            <tr v-if="!sentEmails.length">
                                <td colspan="5" class="px-4 py-10 text-center text-sm italic text-gray-500">
                                    Aucun email envoyé pour le moment.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal modèle -->
        <Modal :show="showTemplateModal" max-width="2xl" @close="showTemplateModal = false">
            <form class="p-6" @submit.prevent="submitTemplate">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ editingTemplate ? 'Modifier le modèle' : 'Nouveau modèle d\'email' }}
                </h3>
                <div class="mt-4 space-y-4">
                    <div>
                        <InputLabel value="Nom du modèle (usage interne)" />
                        <TextInput v-model="templateForm.name" type="text" class="mt-1 block w-full" required />
                        <InputError :message="templateForm.errors.name" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Objet de l'email" />
                        <TextInput v-model="templateForm.subject" type="text" class="mt-1 block w-full" required />
                        <InputError :message="templateForm.errors.subject" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Corps du message" />
                        <p class="text-xs text-gray-500">Variables disponibles : {prenom}, {nom}, {poste}, {date}, {heure}, {lieu}</p>
                        <textarea v-model="templateForm.body" rows="10"
                            class="mt-1 block w-full rounded border-gray-300 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                            required />
                        <InputError :message="templateForm.errors.body" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Description (optionnel)" />
                        <TextInput v-model="templateForm.description" type="text" class="mt-1 block w-full" />
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <SecondaryButton type="button" @click="showTemplateModal = false">Annuler</SecondaryButton>
                    <PrimaryButton :disabled="templateForm.processing">
                        {{ editingTemplate ? 'Enregistrer' : 'Créer' }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>

```

---

## `resources/js/Pages/Offers/Index.vue`

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    offers: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const showModal = ref(false);
const statusFilter = ref(props.filters.status || '');

const form = useForm({
    title: '',
    department: '',
    location: '',
    contract_type: '',
    description: '',
    requirements: '',
    salary_range: '',
    status: 'draft',
});

const statusOptions = [
    { value: '', label: 'Tous les statuts' },
    { value: 'draft', label: 'Brouillon' },
    { value: 'active', label: 'Active' },
    { value: 'archived', label: 'Archivée' },
];

const contractTypes = ['CDI', 'CDD', 'Stage', 'Alternance', 'Interim'];

const statusColor = (status) => {
    const map = { draft: 'warn', active: 'ok', archived: 'info' };
    return map[status] || 'info';
};

const statusLabel = (status) => {
    const map = { draft: 'Brouillon', active: 'Active', archived: 'Archivée' };
    return map[status] || status;
};

watch(statusFilter, () => {
    router.get(route('offers.index'), {
        status: statusFilter.value || undefined,
    }, { preserveState: true, replace: true });
});

const submit = () => {
    form.post(route('offers.store'), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
};

const destroy = (id) => {
    if (confirm('Supprimer cette offre ?')) {
        router.post(route('offers.destroy', id));
    }
};
</script>

<template>
    <Head title="Offres d'emploi" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">Offres d'emploi</h2>
                <PrimaryButton @click="showModal = true">+ Créer une offre</PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Filtre -->
                <div class="mb-6">
                    <select v-model="statusFilter"
                        class="rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option v-for="s in statusOptions" :key="s.value" :value="s.value">{{ s.label }}</option>
                    </select>
                </div>

                <!-- Grille d'offres -->
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div v-for="o in offers.data" :key="o.id"
                        class="rounded-xl bg-white p-5 shadow-soft dark:bg-gray-800 transition hover:shadow-md">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <Link :href="route('offers.show', o.id)" class="text-base font-semibold text-gray-900 dark:text-gray-100 hover:text-brand-primary">
                                    {{ o.title }}
                                </Link>
                                <div class="text-xs text-gray-500 mt-1">
                                    <span v-if="o.department">{{ o.department }}</span>
                                    <span v-if="o.department && o.location"> - </span>
                                    <span v-if="o.location">{{ o.location }}</span>
                                </div>
                            </div>
                            <StatusBadge :label="statusLabel(o.status)" :cls="statusColor(o.status)" />
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-500 mb-3">
                            <span v-if="o.contract_type" class="rounded-full bg-brand-lavender px-2 py-0.5 text-violet-800 font-medium">{{ o.contract_type }}</span>
                            <span v-if="o.salary_range">{{ o.salary_range }}</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">{{ o.description }}</p>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">{{ new Date(o.created_at).toLocaleDateString('fr-FR') }}</span>
                            <div class="flex gap-2">
                                <Link :href="route('offers.show', o.id)" class="text-brand-primary hover:underline">Voir</Link>
                                <button @click="destroy(o.id)" class="text-pink-600 hover:underline">Supprimer</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="!offers.data.length" class="rounded-xl bg-white p-8 shadow-soft text-center dark:bg-gray-800 mt-4">
                    <p class="text-sm italic text-gray-500">Aucune offre trouvée</p>
                </div>

                <!-- Pagination -->
                <div v-if="offers.links && offers.links.length > 3" class="mt-6 flex justify-center gap-1">
                    <template v-for="link in offers.links" :key="link.label">
                        <Link v-if="link.url" :href="link.url"
                            class="rounded px-3 py-1 text-sm transition"
                            :class="link.active ? 'bg-brand-primary text-white' : 'bg-white text-gray-700 hover:bg-brand-tertiary'"
                            v-html="link.label" />
                        <span v-else class="rounded px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>

        <!-- Modal création -->
        <Modal :show="showModal" @close="showModal = false" max-width="2xl">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Créer une offre d'emploi</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <InputLabel value="Titre du poste *" />
                            <TextInput v-model="form.title" class="mt-1 w-full" required />
                            <InputError :message="form.errors.title" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel value="Département" />
                            <TextInput v-model="form.department" class="mt-1 w-full" />
                        </div>
                        <div>
                            <InputLabel value="Localisation" />
                            <TextInput v-model="form.location" class="mt-1 w-full" />
                        </div>
                        <div>
                            <InputLabel value="Type de contrat" />
                            <select v-model="form.contract_type"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">Sélectionner</option>
                                <option v-for="ct in contractTypes" :key="ct" :value="ct">{{ ct }}</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel value="Fourchette salariale" />
                            <TextInput v-model="form.salary_range" class="mt-1 w-full" placeholder="Ex: 35-45k EUR" />
                        </div>
                    </div>
                    <div>
                        <InputLabel value="Description *" />
                        <textarea v-model="form.description" rows="4" required
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                        <InputError :message="form.errors.description" class="mt-1" />
                    </div>
                    <div>
                        <InputLabel value="Prérequis" />
                        <textarea v-model="form.requirements" rows="3"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                    </div>
                    <div class="flex justify-end gap-3">
                        <SecondaryButton @click="showModal = false">Annuler</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">Créer l'offre</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

```

---

## `resources/js/Pages/Offers/Show.vue`

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    offer: { type: Object, required: true },
});

const editing = ref(false);

const form = useForm({
    title: props.offer.title,
    department: props.offer.department || '',
    location: props.offer.location || '',
    contract_type: props.offer.contract_type || '',
    description: props.offer.description,
    requirements: props.offer.requirements || '',
    salary_range: props.offer.salary_range || '',
    status: props.offer.status,
});

const contractTypes = ['CDI', 'CDD', 'Stage', 'Alternance', 'Interim'];

const statusColor = (status) => {
    const map = { draft: 'warn', active: 'ok', archived: 'info' };
    return map[status] || 'info';
};

const statusLabel = (status) => {
    const map = { draft: 'Brouillon', active: 'Active', archived: 'Archivée' };
    return map[status] || status;
};

const updateOffer = () => {
    form.post(route('offers.update', props.offer.id), {
        onSuccess: () => { editing.value = false; },
    });
};

const archive = () => {
    if (confirm('Archiver cette offre ?')) {
        router.post(route('offers.archive', props.offer.id));
    }
};
</script>

<template>
    <Head :title="offer.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <Link :href="route('offers.index')" class="text-sm text-brand-primary hover:underline mb-1 inline-block">&larr; Retour aux offres</Link>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">{{ offer.title }}</h2>
                </div>
                <div class="flex items-center gap-3">
                    <StatusBadge :label="statusLabel(offer.status)" :cls="statusColor(offer.status)" />
                    <SecondaryButton v-if="offer.status !== 'archived'" @click="archive">Archiver</SecondaryButton>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- Détails de l'offre -->
                <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Détails de l'offre</h3>
                        <SecondaryButton v-if="!editing" @click="editing = true">Modifier</SecondaryButton>
                    </div>

                    <form v-if="editing" @submit.prevent="updateOffer" class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <InputLabel value="Titre *" />
                                <TextInput v-model="form.title" class="mt-1 w-full" required />
                                <InputError :message="form.errors.title" class="mt-1" />
                            </div>
                            <div>
                                <InputLabel value="Département" />
                                <TextInput v-model="form.department" class="mt-1 w-full" />
                            </div>
                            <div>
                                <InputLabel value="Localisation" />
                                <TextInput v-model="form.location" class="mt-1 w-full" />
                            </div>
                            <div>
                                <InputLabel value="Type de contrat" />
                                <select v-model="form.contract_type"
                                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    <option value="">Sélectionner</option>
                                    <option v-for="ct in contractTypes" :key="ct" :value="ct">{{ ct }}</option>
                                </select>
                            </div>
                            <div>
                                <InputLabel value="Fourchette salariale" />
                                <TextInput v-model="form.salary_range" class="mt-1 w-full" />
                            </div>
                            <div>
                                <InputLabel value="Statut" />
                                <select v-model="form.status"
                                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    <option value="draft">Brouillon</option>
                                    <option value="active">Active</option>
                                    <option value="archived">Archivée</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <InputLabel value="Description *" />
                            <textarea v-model="form.description" rows="5" required
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                            <InputError :message="form.errors.description" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel value="Prérequis" />
                            <textarea v-model="form.requirements" rows="4"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                        </div>
                        <div class="flex gap-3">
                            <PrimaryButton :disabled="form.processing">Enregistrer</PrimaryButton>
                            <SecondaryButton @click="editing = false">Annuler</SecondaryButton>
                        </div>
                    </form>

                    <div v-else class="space-y-4">
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 text-sm">
                            <div><span class="font-medium text-gray-500">Département :</span> {{ offer.department || '—' }}</div>
                            <div><span class="font-medium text-gray-500">Localisation :</span> {{ offer.location || '—' }}</div>
                            <div><span class="font-medium text-gray-500">Type de contrat :</span> {{ offer.contract_type || '—' }}</div>
                            <div><span class="font-medium text-gray-500">Salaire :</span> {{ offer.salary_range || '—' }}</div>
                            <div v-if="offer.published_at"><span class="font-medium text-gray-500">Publiée le :</span> {{ new Date(offer.published_at).toLocaleDateString('fr-FR') }}</div>
                            <div v-if="offer.created_by"><span class="font-medium text-gray-500">Créée par :</span> {{ offer.created_by.name }}</div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-1">Description</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ offer.description }}</p>
                        </div>
                        <div v-if="offer.requirements">
                            <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-1">Prérequis</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ offer.requirements }}</p>
                        </div>
                    </div>
                </div>

                <!-- Candidats liés (via interview_reports) -->
                <div class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Candidats liés</h3>
                    <ul v-if="offer.interview_reports?.length" class="divide-y divide-gray-100 dark:divide-gray-700">
                        <li v-for="r in offer.interview_reports" :key="r.id" class="flex items-center justify-between py-3">
                            <div>
                                <Link :href="route('candidates.show', r.candidate.id)" class="text-sm font-medium text-gray-900 hover:text-brand-primary dark:text-gray-100">
                                    {{ r.candidate.first_name }} {{ r.candidate.last_name }}
                                </Link>
                                <div class="text-xs text-gray-500">Entretien le {{ new Date(r.interview_date).toLocaleDateString('fr-FR') }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span v-if="r.rating" class="text-sm text-yellow-600">{{ '★'.repeat(r.rating) }}{{ '☆'.repeat(5 - r.rating) }}</span>
                                <StatusBadge v-if="r.recommendation"
                                    :label="r.recommendation === 'hire' ? 'Embaucher' : r.recommendation === 'maybe' ? 'Peut-être' : 'Refuser'"
                                    :cls="r.recommendation === 'hire' ? 'ok' : r.recommendation === 'maybe' ? 'warn' : 'danger'" />
                            </div>
                        </li>
                    </ul>
                    <p v-else class="text-sm italic text-gray-500">Aucun candidat lié à cette offre</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

```

---

## `resources/js/Pages/Reports/Index.vue`

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    reports: { type: Object, required: true },
    candidates: { type: Array, default: () => [] },
    offers: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const showModal = ref(false);
const candidateFilter = ref(props.filters.candidate_id || '');

const form = useForm({
    candidate_id: '',
    job_offer_id: '',
    interview_date: '',
    rating: '',
    strengths: '',
    weaknesses: '',
    notes: '',
    recommendation: '',
});

watch(candidateFilter, () => {
    router.get(route('reports.index'), {
        candidate_id: candidateFilter.value || undefined,
    }, { preserveState: true, replace: true });
});

const recommendationLabel = (r) => {
    const map = { hire: 'Embaucher', maybe: 'Peut-être', reject: 'Refuser' };
    return map[r] || r;
};

const recommendationColor = (r) => {
    const map = { hire: 'ok', maybe: 'warn', reject: 'danger' };
    return map[r] || 'info';
};

const submit = () => {
    form.post(route('reports.store'), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
};

const destroy = (id) => {
    if (confirm('Supprimer ce compte-rendu ?')) {
        router.post(route('reports.destroy', id));
    }
};
</script>

<template>
    <Head title="Comptes-rendus" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">Comptes-rendus d'entretien</h2>
                <PrimaryButton @click="showModal = true">+ Nouveau compte-rendu</PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Filtre -->
                <div class="mb-6">
                    <select v-model="candidateFilter"
                        class="rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="">Tous les candidats</option>
                        <option v-for="c in candidates" :key="c.id" :value="c.id">{{ c.first_name }} {{ c.last_name }}</option>
                    </select>
                </div>

                <!-- Tableau -->
                <div class="overflow-hidden rounded-xl bg-white shadow-soft dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-brand-tertiary/50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Candidat</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Offre</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Note</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Recommandation</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="r in reports.data" :key="r.id" class="hover:bg-brand-tertiary/30 transition">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ new Date(r.interview_date).toLocaleDateString('fr-FR') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    <Link v-if="r.candidate" :href="route('candidates.show', r.candidate.id)" class="hover:text-brand-primary">
                                        {{ r.candidate.first_name }} {{ r.candidate.last_name }}
                                    </Link>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ r.job_offer?.title || '—' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-yellow-600">
                                    <span v-if="r.rating">{{ '★'.repeat(r.rating) }}{{ '☆'.repeat(5 - r.rating) }}</span>
                                    <span v-else class="text-gray-400">—</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <StatusBadge v-if="r.recommendation" :label="recommendationLabel(r.recommendation)" :cls="recommendationColor(r.recommendation)" />
                                    <span v-else class="text-sm text-gray-400">—</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                    <button @click="destroy(r.id)" class="text-pink-600 hover:underline">Supprimer</button>
                                </td>
                            </tr>
                            <tr v-if="!reports.data.length">
                                <td colspan="6" class="px-6 py-8 text-center text-sm italic text-gray-500">Aucun compte-rendu</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="reports.links && reports.links.length > 3" class="mt-4 flex justify-center gap-1">
                    <template v-for="link in reports.links" :key="link.label">
                        <Link v-if="link.url" :href="link.url"
                            class="rounded px-3 py-1 text-sm transition"
                            :class="link.active ? 'bg-brand-primary text-white' : 'bg-white text-gray-700 hover:bg-brand-tertiary'"
                            v-html="link.label" />
                        <span v-else class="rounded px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>

        <!-- Modal création -->
        <Modal :show="showModal" @close="showModal = false" max-width="2xl">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Nouveau compte-rendu</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <InputLabel value="Candidat *" />
                            <select v-model="form.candidate_id" required
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">Sélectionner</option>
                                <option v-for="c in candidates" :key="c.id" :value="c.id">{{ c.first_name }} {{ c.last_name }}</option>
                            </select>
                            <InputError :message="form.errors.candidate_id" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel value="Offre d'emploi" />
                            <select v-model="form.job_offer_id"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">Aucune</option>
                                <option v-for="o in offers" :key="o.id" :value="o.id">{{ o.title }}</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel value="Date de l'entretien *" />
                            <TextInput v-model="form.interview_date" type="date" class="mt-1 w-full" required />
                            <InputError :message="form.errors.interview_date" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel value="Note (1-5)" />
                            <select v-model="form.rating"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">Sans note</option>
                                <option v-for="n in 5" :key="n" :value="n">{{ '★'.repeat(n) }} ({{ n }}/5)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <InputLabel value="Points forts" />
                        <textarea v-model="form.strengths" rows="2"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                    </div>
                    <div>
                        <InputLabel value="Points faibles" />
                        <textarea v-model="form.weaknesses" rows="2"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                    </div>
                    <div>
                        <InputLabel value="Notes" />
                        <textarea v-model="form.notes" rows="3"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                    </div>
                    <div>
                        <InputLabel value="Recommandation" />
                        <select v-model="form.recommendation"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            <option value="">Sans recommandation</option>
                            <option value="hire">Embaucher</option>
                            <option value="maybe">Peut-être</option>
                            <option value="reject">Refuser</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3">
                        <SecondaryButton @click="showModal = false">Annuler</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">Créer</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

```

---

## `resources/js/Pages/Scripts/Index.vue`

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    scripts: { type: Object, required: true },
});

const showModal = ref(false);

const form = useForm({
    title: '',
    description: '',
    sections: [{ title: '', content: '' }],
});

const addSection = () => {
    form.sections.push({ title: '', content: '' });
};

const removeSection = (index) => {
    if (form.sections.length > 1) {
        form.sections.splice(index, 1);
    }
};

const submit = () => {
    form.post(route('scripts.store'), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
            form.sections = [{ title: '', content: '' }];
        },
    });
};

const destroy = (id) => {
    if (confirm('Supprimer ce script ?')) {
        router.post(route('scripts.destroy', id));
    }
};
</script>

<template>
    <Head title="Scripts d'entretien" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">Scripts d'entretien</h2>
                <PrimaryButton @click="showModal = true">+ Créer un script</PrimaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div v-for="s in scripts.data" :key="s.id"
                        class="rounded-xl bg-white p-5 shadow-soft dark:bg-gray-800 transition hover:shadow-md">
                        <div class="flex items-start justify-between mb-3">
                            <Link :href="route('scripts.show', s.id)" class="text-base font-semibold text-gray-900 dark:text-gray-100 hover:text-brand-primary">
                                {{ s.title }}
                            </Link>
                        </div>
                        <p v-if="s.description" class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">{{ s.description }}</p>
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>{{ s.sections?.length || 0 }} section(s)</span>
                            <span>{{ new Date(s.created_at).toLocaleDateString('fr-FR') }}</span>
                        </div>
                        <div class="mt-3 flex justify-end gap-2 text-sm">
                            <Link :href="route('scripts.show', s.id)" class="text-brand-primary hover:underline">Voir</Link>
                            <button @click="destroy(s.id)" class="text-pink-600 hover:underline">Supprimer</button>
                        </div>
                    </div>
                </div>

                <div v-if="!scripts.data.length" class="rounded-xl bg-white p-8 shadow-soft text-center dark:bg-gray-800 mt-4">
                    <p class="text-sm italic text-gray-500">Aucun script créé</p>
                </div>

                <!-- Pagination -->
                <div v-if="scripts.links && scripts.links.length > 3" class="mt-6 flex justify-center gap-1">
                    <template v-for="link in scripts.links" :key="link.label">
                        <Link v-if="link.url" :href="link.url"
                            class="rounded px-3 py-1 text-sm transition"
                            :class="link.active ? 'bg-brand-primary text-white' : 'bg-white text-gray-700 hover:bg-brand-tertiary'"
                            v-html="link.label" />
                        <span v-else class="rounded px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>

        <!-- Modal création -->
        <Modal :show="showModal" @close="showModal = false" max-width="2xl">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Créer un script d'entretien</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <InputLabel value="Titre *" />
                        <TextInput v-model="form.title" class="mt-1 w-full" required />
                        <InputError :message="form.errors.title" class="mt-1" />
                    </div>
                    <div>
                        <InputLabel value="Description" />
                        <textarea v-model="form.description" rows="2"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                    </div>

                    <!-- Sections dynamiques -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <InputLabel value="Sections *" />
                            <button type="button" @click="addSection" class="text-sm text-brand-primary hover:underline">+ Ajouter une section</button>
                        </div>
                        <div v-for="(section, idx) in form.sections" :key="idx"
                            class="mb-3 rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-medium text-gray-500">Section {{ idx + 1 }}</span>
                                <button v-if="form.sections.length > 1" type="button" @click="removeSection(idx)"
                                    class="text-xs text-pink-600 hover:underline">Supprimer</button>
                            </div>
                            <TextInput v-model="section.title" placeholder="Titre de la section" class="w-full mb-2" />
                            <textarea v-model="section.content" rows="3" placeholder="Questions / contenu..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm" />
                        </div>
                        <InputError :message="form.errors.sections" class="mt-1" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <SecondaryButton @click="showModal = false">Annuler</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">Créer</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

```

---

## `resources/js/Pages/Scripts/Show.vue`

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    script: { type: Object, required: true },
});

const editing = ref(false);

const form = useForm({
    title: props.script.title,
    description: props.script.description || '',
    sections: props.script.sections ? [...props.script.sections] : [{ title: '', content: '' }],
});

const addSection = () => {
    form.sections.push({ title: '', content: '' });
};

const removeSection = (index) => {
    if (form.sections.length > 1) {
        form.sections.splice(index, 1);
    }
};

const save = () => {
    form.post(route('scripts.update', props.script.id), {
        onSuccess: () => { editing.value = false; },
    });
};
</script>

<template>
    <Head :title="script.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <Link :href="route('scripts.index')" class="text-sm text-brand-primary hover:underline mb-1 inline-block">&larr; Retour aux scripts</Link>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">{{ script.title }}</h2>
                </div>
                <SecondaryButton v-if="!editing" @click="editing = true">Modifier</SecondaryButton>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- Mode édition -->
                <div v-if="editing" class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                    <form @submit.prevent="save" class="space-y-4">
                        <div>
                            <InputLabel value="Titre *" />
                            <TextInput v-model="form.title" class="mt-1 w-full" required />
                            <InputError :message="form.errors.title" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel value="Description" />
                            <textarea v-model="form.description" rows="2"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                        </div>

                        <!-- Sections dynamiques -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <InputLabel value="Sections" />
                                <button type="button" @click="addSection" class="text-sm text-brand-primary hover:underline">+ Ajouter une section</button>
                            </div>
                            <div v-for="(section, idx) in form.sections" :key="idx"
                                class="mb-3 rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Section {{ idx + 1 }}</span>
                                    <button v-if="form.sections.length > 1" type="button" @click="removeSection(idx)"
                                        class="text-xs text-pink-600 hover:underline">Supprimer</button>
                                </div>
                                <TextInput v-model="section.title" placeholder="Titre de la section" class="w-full mb-2" />
                                <textarea v-model="section.content" rows="4" placeholder="Questions / contenu..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm" />
                            </div>
                            <InputError :message="form.errors.sections" class="mt-1" />
                        </div>

                        <div class="flex gap-3">
                            <PrimaryButton :disabled="form.processing">Enregistrer</PrimaryButton>
                            <SecondaryButton @click="editing = false">Annuler</SecondaryButton>
                        </div>
                    </form>
                </div>

                <!-- Mode lecture -->
                <div v-else class="space-y-4">
                    <div v-if="script.description" class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ script.description }}</p>
                    </div>

                    <div v-for="(section, idx) in script.sections" :key="idx"
                        class="rounded-xl bg-white p-6 shadow-soft dark:bg-gray-800">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                            <span class="grid h-7 w-7 place-items-center rounded-full bg-brand-lavender text-xs font-bold text-violet-800">{{ idx + 1 }}</span>
                            {{ section.title }}
                        </h3>
                        <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap pl-9">{{ section.content }}</div>
                    </div>

                    <div v-if="!script.sections?.length" class="rounded-xl bg-white p-8 shadow-soft text-center dark:bg-gray-800">
                        <p class="text-sm italic text-gray-500">Aucune section définie</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

```

---

## `resources/js/Pages/Profile/Edit.vue`

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <DeleteUserForm class="max-w-xl" />
                </div>
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

# CSS & JS

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

# Docker & Déploiement

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

# Documentation

## `OSCD_RECRUTEMENT_DOCUMENTATION.md`

```md
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

```

---


# Fin du document
