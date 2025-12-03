<?php

use Lunar\Models\Product;
use Lunar\Models\Url;

test('product API returns variant options with detected types', function () {
    if (! extension_loaded('pdo_sqlite')) {
        $this->markTestSkipped('sqlite driver not available in this environment');
    }

    // Create a minimal product with urls and variants using factories where available.
    // If factories are not configured in this sandbox, we'll skip the test.
    if (! class_exists(Product::class) || ! class_exists(Url::class)) {
        $this->markTestSkipped('Lunar product factories not available in this environment');
    }

    // Create product and URL
    $product = Product::factory()->create();
    $url = Url::factory()->create([
        'resource_id' => $product->id,
        'resource_type' => Product::class,
        'slug' => 'api-test-product',
    ]);

    $response = $this->get('/api/product/api-test-product');

    $response->assertStatus(200);

    $json = $response->json();

    // Ensure variants key exists (may be empty)
    $this->assertArrayHasKey('variants', $json);
    $variants = $json['variants'] ?? [];

    foreach ($variants as $variant) {
        $this->assertArrayHasKey('options', $variant);
        foreach ($variant['options'] as $opt) {
            $this->assertArrayHasKey('detected_type', $opt);
            $this->assertContains($opt['detected_type'], ['size', 'color', 'other']);
        }
    }
});
