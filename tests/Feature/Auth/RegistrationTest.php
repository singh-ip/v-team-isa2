<?php

use App\Services\UserInvitationService;

test('New users can register', function () {
    $email = fake()->freeEmail;
    $response = $this->post(route('register'), [
        'email' => $email,
        'password' => 'Admin@12-3',
        'password_confirmation' => 'Admin@12-3',
    ]);

    $response->assertCreated();
    $response->assertSee(__('messages.user.registered'));
    $this->assertDatabaseHas('users', [
        'email' => $email
    ]);
});

test('Users with invitation can register', function () {
    $this->be(createSuperAdmin());
    $email = fake()->freeEmail;

    $invitation = (new UserInvitationService())->create([
        'email' => $email,
        'role' => 'user',
    ]);

    Auth::logout();

    $this->post(route('register'), [
        'invitation_key' => $invitation->signature,
        'password' => 'Admin@12-3',
        'password_confirmation' => 'Admin@12-3',
    ])
        ->assertCreated()
        ->assertSee(__('messages.user.registered'));

    $this->assertDatabaseHas('users', [
        'email' => $email
    ]);
});

test('Password format validations', function () {
    $email = fake()->freeEmail;

    $responseLowerCasePassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => $email,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $responseLowerCasePassword->assertInvalid();

    $responseUpperCasePassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => $email,
        'password' => 'PASSWORD',
        'password_confirmation' => 'PASSWORD',
    ]);

    $responseUpperCasePassword->assertInvalid();

    $responseMixedCasePassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => $email,
        'password' => 'PASSword',
        'password_confirmation' => 'PASSword',
    ]);

    $responseMixedCasePassword->assertInvalid();

    $responseMixedCaseAndNumberPassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => $email,
        'password' => 'PASSword1',
        'password_confirmation' => 'PASSword1',
    ]);

    $responseMixedCaseAndNumberPassword->assertInvalid();

    $responseSmallPassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => $email,
        'password' => 'Sword1@',
        'password_confirmation' => 'Sword1@',
    ]);

    $responseSmallPassword->assertInvalid();

    $responseConsecutiveCharsPassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => $email,
        'password' => 'P@sssssword1',
        'password_confirmation' => 'P@sssssword1',
    ]);

    $responseConsecutiveCharsPassword->assertInvalid();

    $responseSequentialIncCharsPassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => $email,
        'password' => 'P@ssword12345',
        'password_confirmation' => 'P@sssword12345',
    ]);

    $responseSequentialIncCharsPassword->assertInvalid();

    $responseSequentialDecCharsPassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => $email,
        'password' => 'P@ssword54321',
        'password_confirmation' => 'P@sssword54321',
    ]);

    $responseSequentialDecCharsPassword->assertInvalid();

    $responseSequentialIncKeyboardCharsPassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => $email,
        'password' => 'P@ssword1qwer',
        'password_confirmation' => 'P@sssword1qwer',
    ]);

    $responseSequentialIncKeyboardCharsPassword->assertInvalid();

    $responseSequentialDecKeyboardCharsPassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => $email,
        'password' => 'P@ssword1/.,m',
        'password_confirmation' => 'P@sssword1/.,m',
    ]);

    $responseSequentialDecKeyboardCharsPassword->assertInvalid();

    $responsePasswordContainingEmail = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => $email,
        'password' => 'PAss-' . explode('@', $email)[0] . '-word@12-3',
        'password_confirmation' => 'PAss-test-word@12-3',
    ]);

    $responsePasswordContainingEmail->assertInvalid();

    $responseValidPassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => $email,
        'password' => 'PASSword@12-3',
        'password_confirmation' => 'PASSword@12-3',
    ]);

    $responseValidPassword->assertCreated();
    $responseValidPassword->assertSee(__('messages.user.registered'));
});
