@component('mail::message')
# Welcome To Substribe, {{$name}}

{{$otp}}

@component('mail::button', ['url' => ''])
{{$otp}}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
