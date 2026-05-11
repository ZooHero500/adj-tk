<x-mail::message>
# {{ $greeting }}

@if (!empty($bodyLines))
@foreach ($bodyLines as $line)
{{ $line }}
@endforeach
@else
Your application to join our community has been approved.

Your account is ready to setup!
@endif

<x-mail::button :url="$magicLink">
Setup my new PornTk account
</x-mail::button>

<small>This link will expire after 14 days, so make sure you setup your account before then, or you will need to re-apply.</small>

@lang('Regards,')<br>
[{{ parse_url(config('app.url'), PHP_URL_HOST) }}]({{config('app.url')}})
</x-mail::message>
