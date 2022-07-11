@component('mail::message')
# Welcome To Zora, {{$name}}

{{$otp}}

@component('mail::button', ['url' => ''])
{{$otp}}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
