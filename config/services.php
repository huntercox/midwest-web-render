<?php

return [

  /*
    |--------------------------------------------------------------------------
    | Stripe Payment Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration is for processing payments using Stripe. Add your
    | Stripe API keys to your .env file with STRIPE_KEY and STRIPE_SECRET.
    |
    */

  'stripe' => [
    'key' => env('STRIPE_KEY_TEST'),
    'secret' => env('STRIPE_SECRET_TEST'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET_LOCAL'),
  ],

  /*
    |--------------------------------------------------------------------------
    | Additional Services
    |--------------------------------------------------------------------------
    |
    | Uncomment the below configurations if you decide to use these services
    | in the future.
    |
    */

  // 'postmark' => [
  //     'token' => env('POSTMARK_TOKEN'),
  // ],

  // 'ses' => [
  //     'key' => env('AWS_ACCESS_KEY_ID'),
  //     'secret' => env('AWS_SECRET_ACCESS_KEY'),
  //     'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
  // ],

  // 'resend' => [
  //     'key' => env('RESEND_KEY'),
  // ],

  // 'slack' => [
  //     'notifications' => [
  //         'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
  //         'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
  //     ],
  // ],

];
