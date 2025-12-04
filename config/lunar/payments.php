<?php

return [

    'default' => env('PAYMENTS_TYPE', 'cash-in-hand'),

    'types' => [
        'cash-in-hand' => [
            'driver' => 'offline',
            'authorized' => 'payment-offline',
        ],
        'cash-on-delivery' => [
            'driver' => 'offline',
            'authorized' => 'cash-on-delivery',
        ],
    ],

];
