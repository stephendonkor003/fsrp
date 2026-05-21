@component('mail::message')
# Your FSRP Account Has Been Created

Hello {{ $user->name ?? 'User' }},

An account has been created for you on the Western and Central Africa - West Africa Food System Resilience Program (FSRP) system.

@component('mail::panel')
Email: {{ $user->email }}

Temporary password: {{ $plainPassword }}
@endcomponent

Use these credentials to sign in. For security, you may be asked to change your password after your first login.

@component('mail::button', ['url' => route('login')])
Sign in to FSRP
@endcomponent

Thanks,<br>
{{ config('app.name', 'FSRP') }}
@endcomponent
