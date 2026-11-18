<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return DB::transaction(function () use ($input): User {
            $bootstrap = DB::table('admin_bootstrap')
                ->where('id', 1)
                ->lockForUpdate()
                ->first();

            $role = 'operator';

            if ($bootstrap->admin_user_id === null) {
                $administrator = User::query()
                    ->where('role', 'admin')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->first();

                if ($administrator === null) {
                    $earliestUser = User::query()
                        ->orderBy('id')
                        ->lockForUpdate()
                        ->first();

                    if ($earliestUser !== null) {
                        $earliestUser->forceFill(['role' => 'admin'])->save();
                        $administrator = $earliestUser;
                    } else {
                        $role = 'admin';
                    }
                }
            }

            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);
            $user->forceFill(['role' => $role])->save();

            if ($bootstrap->admin_user_id === null) {
                DB::table('admin_bootstrap')
                    ->where('id', $bootstrap->id)
                    ->update([
                        'admin_user_id' => ($administrator ?? $user)->id,
                        'updated_at' => now(),
                    ]);
            }

            return $user;
        }, attempts: 5);
    }
}
