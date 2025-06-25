<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
// Pastikan ini diimpor:
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Auth\Middleware\Authenticate; // Perbaiki ini
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Ini adalah tempat kamu mendaftarkan middleware.
        // Laravel 11 secara otomatis mendaftarkan beberapa middleware dasar.

        // Jika kamu ingin mendaftarkan alias middleware (yang kita gunakan untuk 'role:admin', dll.)
        // tambahkan alias di sini:
        $middleware->alias([
            'auth' => Authenticate::class, // Ini sudah ada secara default, pastikan ada
            'role' => RoleMiddleware::class, // Tambahkan alias untuk RoleMiddleware kita
        ]);

        // Kamu juga bisa menambahkan middleware global atau ke grup web/api di sini,
        // tapi untuk kasus kita, cukup alias di atas.
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();