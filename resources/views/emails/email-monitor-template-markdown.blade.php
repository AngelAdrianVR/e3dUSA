<x-mail::message>

{{ $content }}

{{-- <x-mail::button :url="''">
Button Text
</x-mail::button> --}}

Saludos,<br>
{{ config('app.name') }}
</x-mail::message>
