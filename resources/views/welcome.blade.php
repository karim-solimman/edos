<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>EDOS</title>
        <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
        <link rel="icon" href="{{ url('img/favicon.png') }}">
    </head>
    <body>
    <div id="app">
        <v-app>
            <app-vue/>
        </v-app>
    </div>
    <script src="{{ mix('js/app.js') }}"></script>
    </body>
</html>
