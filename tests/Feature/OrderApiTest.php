<?php

use App\Models\User;

test('unauthenticated user cannot access orders list', function () {
    $response = $this->getJson('/api/my-orders');
    $response->assertStatus(401);
});

test('authenticated user can access orders list and receives pagination meta', function () {
    if (! extension_loaded('pdo_sqlite')) {
        $this->markTestSkipped('sqlite driver not available in this environment');
    }

    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/my-orders');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data',
            'meta' => ['current_page', 'per_page', 'total', 'last_page'],
        ]);
});

test('authenticated user get order details returns 404 for missing order', function () {
    if (! extension_loaded('pdo_sqlite')) {
        $this->markTestSkipped('sqlite driver not available in this environment');
    }

    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/my-orders/999999');

    $response->assertStatus(404);
});
