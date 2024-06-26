<?php

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class
)->beforeEach(function () {
    $this->seed();
});

test('Can see member count - with route url', function () {
    $this->withHeader('Accept', 'application/json')
        ->get('api/v1/members/count')->assertUnauthorized();
});

test('Can see member count - with route name', function () {
    $this->withHeader('Accept', 'application/json')
        ->get(route('members.count'))
        ->assertUnauthorized();
});

test('Can see member count', function () {
    $user = createUser();
    $response = $this->actingAs($user)
        ->withHeader('Accept', 'application/json')
        ->get('api/v1/members/count')
        ->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'active',
                'pending'
            ]
        ]);
    $this->assertCount(2, $response['data']);
    $this->assertEquals(2, $response['data']['active']);
    $this->assertEquals(0, $response['data']['pending']);
});
