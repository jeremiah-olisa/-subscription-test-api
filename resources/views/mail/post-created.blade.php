@component('mail::message')
# {{$post['title']}}

{{$post['description']}}



@component('mail::button', ['url' => $post['url']])
View
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
