<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public registration cannot create an administrator', function () {
    $response = $this->post('/register', [
        'name' => 'Regular User',
        'email' => 'regular@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => UserRole::Admin->value,
    ]);

    $response->assertRedirect('/');

    $user = User::query()->where('email', 'regular@example.com')->firstOrFail();

    expect($user->role)->toBe(UserRole::User);
    $this->assertAuthenticatedAs($user);
});

test('normal users cannot access admin routes', function () {
    $this->actingAs(User::factory()->create(['role' => UserRole::User]));

    $this->get(route('admin.placeholder'))->assertForbidden();
});

test('administrators can access admin routes', function () {
    $this->actingAs(User::factory()->create(['role' => UserRole::Admin]));

    $this->get(route('admin.placeholder'))->assertNoContent();
});

test('admin seeding requires environment-backed configuration', function () {
    config()->set('admin.name', null);
    config()->set('admin.email', null);
    config()->set('admin.password', null);

    expect(fn () => $this->seed(AdminUserSeeder::class))
        ->toThrow(InvalidArgumentException::class, 'Admin provisioning requires valid ADMIN_NAME, ADMIN_EMAIL, and ADMIN_PASSWORD values.');
});

test('admin seeding is repeatable and promotes the configured account', function () {
    config()->set('admin.name', 'Support Administrator');
    config()->set('admin.email', 'admin@example.com');
    config()->set('admin.password', 'secure-password');

    $this->seed(AdminUserSeeder::class);
    $this->seed(AdminUserSeeder::class);

    $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

    expect(User::query()->where('email', 'admin@example.com')->count())->toBe(1)
        ->and($admin->name)->toBe('Support Administrator')
        ->and($admin->role)->toBe(UserRole::Admin)
        ->and($admin->password)->not->toBe('secure-password');
});
