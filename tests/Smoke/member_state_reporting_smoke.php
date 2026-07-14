<?php

use App\Http\Middleware\EnsureMemberState;
use App\Models\AuMemberState;
use App\Models\User;
use Database\Seeders\MemberStateUserSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

$app = require __DIR__ . '/bootstrap.php';

$expectedCountries = [
    'COM' => ['name' => 'Comoros', 'region_name' => 'Eastern Africa', 'flag' => 'comoros.svg'],
    'KEN' => ['name' => 'Kenya', 'region_name' => 'Eastern Africa', 'flag' => 'kenya.svg'],
    'MWI' => ['name' => 'Malawi', 'region_name' => 'Southern Africa', 'flag' => 'malawi.svg'],
    'MOZ' => ['name' => 'Mozambique', 'region_name' => 'Southern Africa', 'flag' => 'mozambique.svg'],
    'SOM' => ['name' => 'Somalia', 'region_name' => 'Eastern Africa', 'flag' => 'somalia.svg'],
    'ETH' => ['name' => 'Ethiopia', 'region_name' => 'Eastern Africa', 'flag' => 'ethiopia.svg'],
    'TZA' => ['name' => 'Tanzania', 'region_name' => 'Eastern Africa', 'flag' => 'tanzania.svg'],
];

$countries = AuMemberState::query()->get()->keyBy('code');

if ($countries->keys()->sort()->values()->all() !== collect(array_keys($expectedCountries))->sort()->values()->all()) {
    fwrite(STDERR, "The member-state table does not contain exactly the seven configured reporting countries.\n");
    exit(1);
}

foreach ($expectedCountries as $code => $expected) {
    $country = $countries->get($code);

    if (! $country
        || $country->name !== $expected['name']
        || $country->region_name !== $expected['region_name']
        || $country->flag_path !== 'assets/images/member-states/flags/' . $expected['flag']
        || ! is_file(public_path($country->flag_path))) {
        fwrite(STDERR, "Country or regional-name mismatch for {$code}.\n");
        exit(1);
    }

    $credentials = MemberStateUserSeeder::CREDENTIALS[$code];
    $user = User::query()
        ->with('role')
        ->where('email', $credentials['email'])
        ->first();

    if (! $user
        || $user->user_type !== 'member_state'
        || $user->member_state_id !== $country->id
        || $user->role?->name !== 'Member State Focal Point'
        || $user->is_disabled
        || ! $user->must_change_password
        || ! Hash::check($credentials['password'], $user->password)) {
        fwrite(STDERR, "Credential or access-control mismatch for {$expected['name']}.\n");
        exit(1);
    }

    $request = Request::create('/member-state/dashboard', 'GET');
    $request->setUserResolver(fn () => $user);
    $response = $app->make(EnsureMemberState::class)->handle($request, fn () => response('ok'));

    if ($response->getStatusCode() !== 200) {
        fwrite(STDERR, "Member-state middleware rejected {$expected['name']}.\n");
        exit(1);
    }
}

$enabledMemberStateEmails = User::query()
    ->where('user_type', 'member_state')
    ->where('is_disabled', false)
    ->pluck('email')
    ->sort()
    ->values()
    ->all();
$expectedEmails = collect(MemberStateUserSeeder::CREDENTIALS)
    ->pluck('email')
    ->sort()
    ->values()
    ->all();

if ($enabledMemberStateEmails !== $expectedEmails) {
    fwrite(STDERR, "Unexpected enabled member-state accounts were found.\n");
    exit(1);
}

echo "MEMBER_STATE_REPORTING_OK\n";
