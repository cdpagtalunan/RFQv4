<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>RFQv4</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('public/template/adminlte/css/adminlte.min.css') }}">
        <link rel="stylesheet" href="{{ asset('public/template/fontawesome/css/all.min.css') }}">

        <script src="{{ asset('public/template/adminlte/js/adminlte.min.js') }}" defer></script>
        <script src="{{ asset('public/js/common.js') }}" defer></script>


        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
        <link rel="stylesheet" href="{{ asset('public/css/app.css') }}">

    </head>
    <body>
        <div id="app">
            <router-view/>
        </div>
        <script src="{{ asset('public/js/app.js') }}"></script>
    </body>
    
</html>