<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Rules\ValidHCaptcha;
use Inertia\Inertia;

class ContactController extends Controller
{
	public function store(Request $request)
	{
		// Log the full request to check if hCaptcha is being received
		Log::info('Form Request Data:', $request->all());

		// Validate form input, including hCaptcha
		$validated = $request->validate([
			'name' => 'required|string|max:255',
			'email' => 'nullable|email',
			'phone' => 'nullable|string|max:20|regex:/^[0-9\-\(\)\s]+$/',
			'message' => 'required|string|max:1000',
			'h-captcha-response' => ['required', new ValidHCaptcha()],
		]);

		// Custom validation: require either email or phone
		if (empty($validated['email']) && empty($validated['phone'])) {
			return back()->withErrors([
				'contact' => 'Please provide either an email address or phone number so we can contact you.'
			])->withInput();
		}

		// Log validated data
		Log::info('Validated Data:', $validated);

		try {
			$phoneInfo = $request->phone ? "\nPhone: {$request->phone}" : '';

			Mail::raw(
				"New Contact Form Submission\n\nName: {$request->name}\nEmail: {$request->email}{$phoneInfo}\nMessage: {$request->message}",
				function ($message) use ($request) {
					$message->to('info@midwest-web.com')
						->subject('New Contact Form Submission');
				}
			);
		} catch (\Throwable $e) {
			Log::error('Email sending failed: ' . $e->getMessage(), ['exception' => $e]);

			if ($request->wantsJson() || $request->header('X-Inertia')) {
				return back()->with('error', 'There was an error sending your message. Please try again later.');
			}

			return redirect('/')
				->withErrors(['email' => 'There was an error sending your message. Please try again later.']);
		}

		Log::debug('Request Data:', $request->all());

		// Return appropriate response based on request type
		if ($request->wantsJson() || $request->header('X-Inertia')) {
			return back()->with('success', 'Your message has been sent.');
		}

		// Redirect back with success message for traditional requests
		return redirect('/')->with('success', 'Your message has been sent.');
	}
}
