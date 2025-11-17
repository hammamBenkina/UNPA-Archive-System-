<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    // protected $middleware = [
    //     // مثال لبعض الميدلوير العالمية
    //     \App\Http\Middleware\TrustProxies::class,
    //     \Fruitcake\Cors\HandleCors::class,
    //     \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
    //     \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
    //     \App\Http\Middleware\TrimStrings::class,
    //     \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    // ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    // protected $middlewareGroups = [
    //     'web' => [
    //         \App\Http\Middleware\EncryptCookies::class,
    //         \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    //         \Illuminate\Session\Middleware\StartSession::class,
    //         \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    //         \App\Http\Middleware\VerifyCsrfToken::class,
    //         \Illuminate\Routing\Middleware\SubstituteBindings::class,
    //     ],

    //     'api' => [
    //         // 'throttle:api', // يمكنك تفعيلها إذا أردت الحد من الطلبات
    //         \Illuminate\Routing\Middleware\SubstituteBindings::class,
    //     ],
    // ];

    /**
     * Route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
      

        // ✅ أضفنا middleware الخاص بك هنا
        'accessibility' => \App\Http\Middleware\Accessibility::class,
    ];
}
