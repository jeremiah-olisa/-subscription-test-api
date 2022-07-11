@component('mail::message')
# Alert !!

New subscriber {{$subscriber}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
