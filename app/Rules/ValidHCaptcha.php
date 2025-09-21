<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ValidHCaptcha implements Rule
{
  public function __construct()
  {
    //
  }

  public function passes($attribute, $value)
  {
    $data = [
      'secret'   => config('captcha.secret'),
      'response' => $value,
      'remoteip' => request()->ip(),
    ];

    Log::debug('hCaptcha token received:', ['token' => $value]);

    $verify = curl_init();
    curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
    curl_setopt($verify, CURLOPT_POST, true);
    curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($verify);

    if ($response === false) {
      Log::error('cURL error during hCaptcha verification: ' . curl_error($verify));
      curl_close($verify);
      return false;
    }

    $responseData = json_decode($response);

    if (is_null($responseData)) {
      Log::error('Failed to decode hCaptcha response', ['response' => $response]);
      curl_close($verify);
      return false;
    }

    if (!$responseData->success) {
      Log::error('hCaptcha verification unsuccessful', [
        'response' => $responseData,
        'error-codes' => $responseData->{'error-codes'} ?? 'none',
      ]);
      curl_close($verify);
      return false;
    }

    curl_close($verify);
    return true;
  }

  public function message()
  {
    return 'Invalid CAPTCHA. You need to prove you are human.';
  }
}
