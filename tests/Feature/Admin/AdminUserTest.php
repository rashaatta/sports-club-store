<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->admin = User::factory()->superAdmin()->create();
});

function adminUserPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'موظف جديد',
        'email' => 'staff@example.test',
        'role' => 'inventory_manager',
        'password' => 'StaffPass123',
        'password_confirmation' => 'StaffPass123',
    ], $overrides);
}

test('an admin can create a staff account with a hashed password', function () {
    $this->actingAs($this->admin)->post(route('admin.users.store'), adminUserPayload())
        ->assertRedirect(route('admin.users.index'));

    $user = User::query()->where('email', 'staff@example.test')->firstOrFail();

    expect($user->role->value)->toBe('inventory_manager')
        ->and(Hash::check('StaffPass123', $user->password))->toBeTrue()
        ->and($user->password)->not->toBe('StaffPass123');
});

test('user creation validates unique email and password strength', function () {
    User::factory()->create(['email' => 'taken@example.test']);

    $this->actingAs($this->admin)->post(route('admin.users.store'), adminUserPayload([
        'email' => 'taken@example.test',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]))->assertSessionHasErrors(['email', 'password']);

    $this->actingAs($this->admin)->post(route('admin.users.store'), adminUserPayload([
        'email' => 'wrong-role@example.test',
        'role' => 'owner',
    ]))->assertSessionHasErrors('role');
});

test('an admin cannot demote or delete their own account', function () {
    $this->actingAs($this->admin)->put(route('admin.users.update', $this->admin), adminUserPayload([
        'name' => $this->admin->name,
        'email' => $this->admin->email,
        'role' => 'store_manager',
        'password' => '',
        'password_confirmation' => '',
    ]))->assertSessionHasErrors('role');

    expect($this->admin->refresh()->role->value)->toBe('super_admin');

    $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin))
        ->assertSessionHas('error');

    $this->assertModelExists($this->admin);
});

test('updating a user without a password keeps the existing hash', function () {
    $user = User::factory()->inventoryManager()->create();
    $originalHash = $user->password;

    $this->actingAs($this->admin)->put(route('admin.users.update', $user), adminUserPayload([
        'name' => 'اسم محدث',
        'email' => $user->email,
        'role' => 'inventory_manager',
        'password' => '',
        'password_confirmation' => '',
    ]))->assertSessionHasNoErrors();

    expect($user->refresh()->name)->toBe('اسم محدث')
        ->and($user->password)->toBe($originalHash);
});

test('an admin can change another user role and delete the account', function () {
    $user = User::factory()->inventoryManager()->create();

    $this->actingAs($this->admin)->put(route('admin.users.update', $user), adminUserPayload([
        'name' => $user->name,
        'email' => $user->email,
        'role' => 'content_manager',
        'password' => '',
        'password_confirmation' => '',
    ]))->assertSessionHasNoErrors();

    expect($user->refresh()->role->value)->toBe('content_manager');

    $this->actingAs($this->admin)->delete(route('admin.users.destroy', $user))
        ->assertRedirect(route('admin.users.index'));

    $this->assertModelMissing($user);
});

test('store managers cannot manage users', function () {
    $storeManager = User::factory()->storeManager()->create();

    $this->actingAs($storeManager)->get(route('admin.users.index'))->assertForbidden();

    $this->actingAs($storeManager)
        ->post(route('admin.users.store'), adminUserPayload(['email' => 'another@example.test']))
        ->assertForbidden();
});
