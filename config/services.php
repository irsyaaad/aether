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

    'mailgun'  => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses'      => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'gpsid'    => [
        'base_url' => env('GPSID_BASE_URL', 'https://api.gpsid.id'),
        'username' => env('GPSID_USERNAME'),
        'password' => env('GPSID_PASSWORD'),
    ],

    'persada'  => [
        'hris_url' => env('PERSADA_HRIS_URL', 'http://hris.persada.test'),
        'erp_url'  => env('PERSADA_ERP_URL', 'http://persada.test'),
        'redirect_to_hris_on_expire' => env('PERSADA_REDIRECT_TO_HRIS_ON_EXPIRE', false),
        'daily_work_plan_lock' => [
            'enabled' => env('PERSADA_DAILY_WORK_PLAN_LOCK_ENABLED', false),
            'role_ids' => array_values(array_filter(array_map('trim', explode(',', (string) env('PERSADA_DAILY_WORK_PLAN_LOCK_ROLE_IDS', ''))), function ($v) {
                return $v !== '';
            })),
        ],
    ],

];
