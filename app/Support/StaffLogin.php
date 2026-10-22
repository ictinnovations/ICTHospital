<?php
/**
 * ICTHospital - sign-in accounts for doctors and staff.
 *
 * A doctor or staff record and a login are separate rows: the record is who the person
 * is on the register, the users row is how they sign in and which permission column
 * applies (users.group). The record's ion_user_id links the two. The doctor and staff
 * forms create or update the login through this class, so an admin can hand over a
 * username and password as soon as the person is added.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StaffLogin
{
    /** The login linked to a record, if there is one. */
    public static function userFor(?Model $person): ?User
    {
        $id = $person ? (string) $person->ion_user_id : '';

        return ctype_digit($id) && $id !== '0' ? User::find((int) $id) : null;
    }

    /**
     * Validate the optional login fields. A new login needs both a username and a
     * password; an existing one can change either.
     */
    public static function validate(Request $request, ?Model $person): array
    {
        $user = self::userFor($person);

        return $request->validate([
            'login_username' => [
                $user ? 'nullable' : 'required_with:login_password',
                'nullable', 'string', 'max:20', 'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('users', 'login')->ignore($user?->id),
            ],
            'login_password' => [$user ? 'nullable' : 'required_with:login_username', 'nullable', 'string', 'min:8', 'max:64'],
        ], [
            'login_username.regex' => 'Use letters, numbers, dots, dashes or underscores for the username.',
            'login_username.unique' => 'That username is already taken.',
            'login_username.required_with' => 'Enter a username for the new login.',
            'login_password.required_with' => 'Enter a password for the new login.',
        ]);
    }

    /**
     * Create or update the login after the record is saved. Returns a sentence for the
     * success message, or null when nothing changed.
     */
    public static function apply(Model $person, string $group, array $fields): ?string
    {
        $username = trim((string) ($fields['login_username'] ?? ''));
        $password = (string) ($fields['login_password'] ?? '');
        $user = self::userFor($person);

        if ($user) {
            $changed = [];
            if ($username !== '' && $username !== $user->login) {
                $user->login = $username;
                $changed[] = 'username';
            }
            if ($password !== '') {
                $user->password = bcrypt($password);
                $changed[] = 'password';
            }
            if ($changed) {
                $user->save();

                return 'Login ' . implode(' and ', $changed) . ' updated.';
            }

            return null;
        }

        if ($username === '') {
            return null;
        }

        [$first, $last] = array_pad(explode(' ', trim((string) $person->name), 2), 2, '');
        $user = User::create([
            'firstname' => mb_substr($first ?: $username, 0, 20),
            'lastname' => mb_substr($last ?: '-', 0, 20),
            'login' => $username,
            'email' => (string) ($person->email ?? ''),
            'group' => $group,
            'desc' => mb_substr($group . ' login for ' . $person->name, 0, 200),
            'password' => bcrypt($password),
        ]);
        $person->ion_user_id = (string) $user->id;
        $person->save();

        return 'Login created: ' . $username . '.';
    }
}
