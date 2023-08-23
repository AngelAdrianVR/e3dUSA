{{-- <!DOCTYPE html>
<html>

<head>
    <title>{{ $subject }}</title>
    <style>
        body {
            margin: 0;
            padding: 15px;
            background-color: #D9D9D9;
            font-family: Arial, sans-serif;
        }

        .container {
            margin-top: 15px background-color: #FFFFFF;
            margin: 0 auto;
            padding: 20px;
            max-width: 600px;
            border: solid #D9D9D9 1px;
            border-radius: 20px;

        }

    </style>
</head>

<body>
    <div class="container">
        <h1>{{ $greeting }}</h1>
        <p>{{ $intro }}</p>
        <p>{{ $outro }}</p>
        <p>{{ $salutation }}</p>
        <a as='button' href="{{ $url }}">Ver en ERP</a>
    </div>
</body>

</html> --}}


{{-- resources/views/emails/custom-approval.blade.php --}}
@component('mail::message')
# {{ $greeting }}

{{ $intro }}

{{ $outro }}

{{ $salutation }}

@component('mail::button', ['url' => $url])
Ver en ERP
@endcomponent
@endcomponent
