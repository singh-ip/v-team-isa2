<?php

beforeEach(function () {
    $this->admin = createSuperAdmin(password: 'Password@12-3');
    $this->user = createUser(password: 'Password@12-3');
});

test('Super admin can see profile', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.profile.edit'))
        ->assertOk();
});

test('User cannot see profile', function () {
    $this->actingAs($this->user)
        ->get(route('admin.profile.edit'))
        ->assertForbidden();
});

test('Super Admin can update profile', function () {
    $this->actingAs($this->admin)
        ->patch(route('admin.profile.update'), [
            'first_name' => 'test_first_name',
            'last_name' => 'test_last_name'
        ]);

    expect($this->admin)
        ->first_name->toBe('test_first_name')
        ->last_name->toBe('test_last_name');
});

test('User cannot update profile', function () {
    $this->actingAs($this->user)
        ->patch(route('admin.profile.update'), [
            'first_name' => 'test123'
        ])
        ->assertForbidden();
    expect($this->user)->first_name->not->toBe('test123');
});

test('Super Admin can update profile e-mail', function () {
    $this->actingAs($this->admin)
        ->patch(route('admin.profile.update'), [
            'email' => 'test@founderandlightning.com',
        ]);
    expect($this->admin)
        ->email_verified_at->toBeNull()
        ->email->toBe('test@founderandlightning.com')
        ->not->toBeAuthenticated();
});

test('Super Admin can update profile password', function () {
    $response = $this->actingAs($this->admin)
        ->put(route('admin.password.update'), [
            'current_password' => 'Password@12-3',
            'password' => 'Password@12-34',
            'password_confirmation' => 'Password@12-34'
        ]);
    $response->assertValid(['current_password', 'password'], 'updatePassword');
});

test('User cannot update profile password', function () {
    $this->actingAs($this->user)
        ->put(route('admin.password.update'), [
            'current_password' => 'Password@12-3',
            'password' => 'Password@12-34',
            'password_confirmation' => 'Password@12-34'
        ])
        ->assertForbidden();
});

test('Super Admin cannot update profile password if not match', function () {
    $this->actingAs($this->admin)
        ->put(route('admin.password.update'), [
            'current_password' => 'Password@12-3',
            'password' => 'password',
            'password_confirmation' => 'password_not_match'
        ])
        ->assertInvalid(['password'], 'updatePassword');
});

test('Super Admin cannot update profile password if not pass requirements', function () {
    $this->actingAs($this->admin)
        ->put(route('admin.password.update'), [
            'current_password' => 'Password@12-3',
            'password' => 'password',
            'password_confirmation' => 'password'
        ])
        ->assertInvalid(['password'], 'updatePassword');
});

test('Super Admin cannot update profile password if current password is incorrect', function () {
    $this->actingAs($this->admin)
        ->put(route('admin.password.update'), [
            'current_password' => 'wrong_password',
            'password' => 'Password@12-312-3',
            'password_confirmation' => 'Password@12-312-3'
        ])
        ->assertInvalid(['current_password'], 'updatePassword');
});

test('Super Admin cannot update profile password if the same as current password', function () {
    $this->actingAs($this->admin)
        ->put(route('admin.password.update'), [
            'current_password' => 'Password@12-3',
            'password' => 'Password@12-3',
            'password_confirmation' => 'Password@12-3'
        ])
        ->assertInvalid(['password'], 'updatePassword');
});

test('Super Admin cannot update profile password if containing email username', function () {
    $email_prefix = explode('@', $this->admin->email)[0];
    $this->actingAs($this->admin)
        ->put(route('admin.password.update'), [
            'current_password' => 'Password@12-3',
            'password' => 'Ax-' . $email_prefix . '-1',
            'password_confirmation' => 'Ax-' . $email_prefix . '-1'
        ])
        ->assertInvalid(['password'], 'updatePassword');
});

test('Super Admin can delete profile', function () {
    $id = $this->admin->id;
    $this->actingAs($this->admin)
        ->delete(route('admin.profile.destroy'), [
            'password' => 'Password@12-3',
        ])
        ->assertRedirect(route('admin.login'));
    expect($this->isAuthenticated())->toBeFalse();
    $this->assertDatabaseMissing('users', ['id' => $id]);
});

test('Super Admin cannot delete profile if wrong password', function () {
    $this->actingAs($this->admin)
        ->delete(route('admin.profile.destroy'), [
            'password' => 'wrong_password',
        ])
        ->assertInvalid(['password'], 'userDeletion');
    $this->assertTrue($this->isAuthenticated());
});
