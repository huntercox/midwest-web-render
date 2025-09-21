<?php

return [
  'secret' => env('H_CAPTCHA_SECRET', 'default-secret-key'),
  'sitekey' => env('H_CAPTCHA_SITEKEY', 'default-site-key'),
  'options' => [
    'timeout' => 10,
  ],
];
