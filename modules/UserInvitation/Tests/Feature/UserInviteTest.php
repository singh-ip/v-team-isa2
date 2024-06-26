<?php

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class
)->beforeEach(function () {
    $this->seed();
});

test('User can resend invite immediately after signup - with route url', function () {
    $this->withHeader('Accept', 'application/json')
        ->post('api/v1/invitations/resend/'. fake()->safeEmail)->assertOk();
});

test('User can resend invite immediately after signup - with route name', function () {
    $this->withHeader('Accept', 'application/json')
        ->post(route('invite.resend', ['email' => fake()->safeEmail]))
        ->assertOk();
});