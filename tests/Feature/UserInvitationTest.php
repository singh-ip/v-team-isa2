<?php

use App\Services\UserInvitationService;

test('Get user invitations list', function () {
    $this->actingAs(createSuperAdmin())
        ->get(route('invitations.index'))
        ->assertOk()
        ->assertResourcePagination();
});

test('Cannot get user invitations list without permission', function () {
    $this->roleUser = getRoleUser();
    $this->roleUser->revokePermissionTo('view users');

    $this->actingAs(createUser())
        ->get(route('invitations.index'))
        ->assertForbidden();
});

test('New invitation shows on list', function () {
    $this->be(createSuperAdmin());
    $email = fake()->email;

    $this->post(route('invitations.store'), [
        'email' => $email,
        'role' => 'user'
    ])
        ->assertValid();

    $this->assertDatabaseHas('user_invitations', ['email' => $email]);

    $this->get(route('invitations.index'))
        ->assertOk()
        ->assertJsonFragment([
            'email' => $email
        ]);
});

test('Can verify invitation with correct signature', function () {
    $this->be(createSuperAdmin());

    $invitation = (new UserInvitationService())->create([
        'email' => 'test@laravel.com',
        'role' => 'user',
    ]);

    Auth::logout();

    $this->get(route('users.invitation.verify', $invitation->signature))
        ->assertRedirectContains(config('frontend.invitation_success_redirect'))
        ->assertRedirectContains($invitation->signature);
});

test('Cannot verify invitation with incorrect signature', function () {
    $this->get(route('users.invitation.verify', 'non-existing-signature'))
        ->assertRedirectContains(config('frontend.invitation_fail_redirect'));
});

test('Cannot verify expired invitation', function () {
    $this->be(createSuperAdmin());

    $invitation = (new UserInvitationService())->create([
        'email' => 'test@laravel.com',
        'role' => 'user',
    ]);

    $invitation->expires_at = (new DateTime())->modify('-1second');
    $invitation->save();

    Auth::logout();

    $this->get(route('users.invitation.verify', $invitation->signature))
        ->assertRedirectContains(config('frontend.invitation_fail_redirect'));
});

test('Unverified user can resend invite', function () {
    $user = createUser();
    $user->email_verified_at = null;
    $user->save();
    $this->actingAs($user)
        ->post(route('invitations.resend'))
        ->assertOk()
        ->assertJsonFragment([
            'data' => null,
            'message' => __('messages.invitation.sent')
        ]);
});

test('Already verified user can not resend invite', function () {
    $this->actingAs(createUser())
        ->post(route('invitations.resend'))
        ->assertOk()
        ->assertJsonFragment([
            'data' => null,
            'message' => __('messages.user.email_already_verified')
        ]);
});

test('Unverified user can not resend invite without logged in', function () {
    $this->withHeader('Accept', 'application/json')
        ->post(route('invitations.resend'))
        ->assertUnauthorized();
});

test('Resend invite - with route url', function () {
    createUser();
    $this->withHeader('Accept', 'application/json')
        ->post('api/v1/invitations/resend')
        ->assertUnauthorized();
});
