@component('mail::message')
# Your FSRP Partner Portal Is Ready

Hello {{ $user->name ?? $member->name }},

You have been added to the FSRP FSRP Partner Portal.

@component('mail::panel')
Think tank: {{ $member->name }}

Consortium: {{ $consortium->name }}

Role: {{ ucfirst(str_replace('_', ' ', $member->role ?? 'member')) }}

Country: {{ $member->country ?? 'Not specified' }}

Login email: {{ $user->email }}

@if($temporaryPassword)
Temporary password: {{ $temporaryPassword }}
@endif
@endcomponent

@if($temporaryPassword)
Use the temporary password above to sign in. For security, you may be asked to change it after your first login.
@else
Use your existing FSRP account password to sign in.
@endif

@component('mail::button', ['url' => $loginUrl])
Open FSRP Partner Portal
@endcomponent

Thanks,<br>
{{ config('app.name', 'FSRP') }}
@endcomponent
