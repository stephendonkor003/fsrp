<?php

namespace Database\Seeders;

use App\Models\AuMemberState;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class MemberStateUserSeeder extends Seeder
{
    public const CREDENTIALS = [
        'COM' => ['email' => 'comoros.memberstate@fsrp.test', 'password' => 'Comoros@FSRP2026!'],
        'KEN' => ['email' => 'kenya.memberstate@fsrp.test', 'password' => 'Kenya@FSRP2026!'],
        'MWI' => ['email' => 'malawi.memberstate@fsrp.test', 'password' => 'Malawi@FSRP2026!'],
        'MOZ' => ['email' => 'mozambique.memberstate@fsrp.test', 'password' => 'Mozambique@FSRP2026!'],
        'SOM' => ['email' => 'somalia.memberstate@fsrp.test', 'password' => 'Somalia@FSRP2026!'],
        'ETH' => ['email' => 'ethiopia.memberstate@fsrp.test', 'password' => 'Ethiopia@FSRP2026!'],
        'TZA' => ['email' => 'tanzania.memberstate@fsrp.test', 'password' => 'Tanzania@FSRP2026!'],
    ];

    public function run(): void
    {
        $role = Role::where('name', 'Member State Focal Point')->first();

        if (! $role) {
            throw new RuntimeException('The Member State Focal Point role must be seeded before member-state users.');
        }

        $memberStates = AuMemberState::query()
            ->whereIn('code', array_keys(self::CREDENTIALS))
            ->get()
            ->keyBy('code');

        if ($memberStates->count() !== count(self::CREDENTIALS)) {
            throw new RuntimeException('All seven FSRP member states must be seeded before their user accounts.');
        }

        foreach (self::CREDENTIALS as $code => $credentials) {
            $memberState = $memberStates->get($code);

            User::updateOrCreate(
                ['email' => $credentials['email']],
                [
                    'name' => $memberState->name . ' Member State Focal Point',
                    'password' => Hash::make($credentials['password']),
                    'user_type' => 'member_state',
                    'role_id' => $role->id,
                    'member_state_id' => $memberState->id,
                    'must_change_password' => true,
                    'password_changed_at' => null,
                    'otp_verified_at' => null,
                    'is_disabled' => false,
                    'disabled_at' => null,
                    'disabled_until' => null,
                    'disabled_reason' => null,
                    'is_blacklisted' => false,
                    'blacklisted_at' => null,
                    'blacklisted_reason' => null,
                ]
            );
        }

        User::query()
            ->where('user_type', 'member_state')
            ->whereNotIn('email', array_column(self::CREDENTIALS, 'email'))
            ->update([
                'is_disabled' => true,
                'disabled_at' => now(),
                'disabled_reason' => 'Country is not configured as an FSRP reporting member state.',
            ]);
    }
}
