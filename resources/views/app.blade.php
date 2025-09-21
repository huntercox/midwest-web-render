<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Midwest Web LLC</title>

  {{-- Favicon --}}
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
  <link rel="manifest" href="/site.webmanifest">

  {{-- hCaptcha Script --}}
  <script src="https://js.hcaptcha.com/1/api.js" async defer></script>

  @routes
  @vite(['resources/js/app.js', 'resources/scss/app.scss'])
  @inertiaHead
</head>

<body>
  @inertia
</body>

</html>
