<?php

test('product API returns 404 and JSON when product cannot be found', function () {
    // When DB drivers are unavailable in the test environment (for example
    // PDO sqlite is not installed), our app may throw a 500 when trying to
    // resolve models. Skip to avoid false negatives in those environments.
    if (! extension_loaded('pdo_sqlite')) {
        $this->markTestSkipped('sqlite driver not available in this environment');
    }

    $response = $this->get('/api/product/this-slug-does-not-exist');

    $response->assertStatus(404)
        ->assertJson(['message' => 'Product not found.']);
});
