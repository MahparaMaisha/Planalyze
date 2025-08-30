@component('mail::message')
@if($greetingName)
# Hi {{ $greetingName }},
@endif

{!! nl2br(e($body)) !!}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
