{{-- resources/views/partials/header.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- <title>Contact Us | Crime Chronicles</title> --}}

    <title>@yield('title') | Crime Chronicles</title>
    <meta name="description" content="Contact the Crime Chronicles investigation team - Report a case, share information, or ask questions.">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css'])
    
    @stack('styles')
</head>
<body>