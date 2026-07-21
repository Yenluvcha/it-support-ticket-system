<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the application's administrator account.
     */
    public function run(): void
    {
        $admin = [
            'name' => config('admin.name'),
            'email' => config('admin.email'),
            'password' => config('admin.password'),
        ];

        $validator = Validator::make($admin, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException(
                'Admin provisioning requires valid ADMIN_NAME, ADMIN_EMAIL, and ADMIN_PASSWORD values. '
                .$validator->errors()->first(),
            );
        }

        $user = User::query()->firstOrNew(['email' => $admin['email']]);
        $user->name = $admin['name'];
        $user->forceFill([
            'password' => $admin['password'],
            'role' => UserRole::Admin,
        ]);
        $user->save();
    }
}
