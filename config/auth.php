<?php

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'cuentas'), // ✅ Cambiar a 'cuentas'
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'cuentas', // ✅ Debe coincidir con el provider
        ],
    ],

    'providers' => [
        'cuentas' => [ // ✅ Este nombre debe coincidir con el guard
            'driver' => 'eloquent',
            'model' => App\Models\Cuenta::class, // ✅ Modelo correcto
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'cuentas', // ✅ Cambiar a 'cuentas'
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];