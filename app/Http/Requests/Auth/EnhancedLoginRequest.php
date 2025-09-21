<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'email' => ['required', 'string', 'email'],
      'password' => ['required', 'string', 'min:6'],
    ];
  }

  public function messages(): array
  {
    return [
      'email.required' => 'Email address is required',
      'email.email' => 'Please enter a valid email address',
      'password.required' => 'Password is required',
      'password.min' => 'Password must be at least 6 characters long',
    ];
  }

  // Auto-generate frontend validation
  public function frontendRules(): array
  {
    return [
      'email' => [
        'required' => true,
        'type' => 'email',
        'message' => 'Email address is required'
      ],
      'password' => [
        'required' => true,
        'minLength' => 6,
        'message' => 'Password must be at least 6 characters long'
      ]
    ];
  }
}
