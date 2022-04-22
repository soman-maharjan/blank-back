@component('mail::message')
    Email Updated!

    Your email has been updated to {{$data['email']}}.

    Thanks,
    {{ config('app.name') }}
@endcomponent
