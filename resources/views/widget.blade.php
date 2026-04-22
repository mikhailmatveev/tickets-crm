<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tickets CRM | Форма обратной связи</title>
    @vite(['resources/assets/widget/styles/style.scss'])
</head>
<body>
    <div id="widget">
        <div class="container">
            @if ($errors->any())
                @include('partials.error')
            @elseif($ticket)
                @include('partials.ticket', [
                    'attachments' => $ticket->getMedia('attachments')
                ])
            @else
                @include('partials.form')
            @endif
        </div>
    </div>
    @vite(['resources/assets/widget/scripts/script.js'])
</body>
</html>
