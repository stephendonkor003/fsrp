@component('mail::message')
# Password Reset

Hello {{ $user->name ?? 'User' }},

Your FSRP account password has been reset by an administrator.

@component('mail::panel')
Email: {{ $user->email }}

Temporary password: {{ $plainPassword }}
@endcomponent

Please sign in and change this password immediately if the system asks you to do so.

@component('mail::button', ['url' => route('login')])
Sign in to FSRP
@endcomponent

If you did not request this reset, please contact the FSRP administrator.

Thanks,<br>
{{ config('app.name', 'FSRP') }}
@endcomponent
