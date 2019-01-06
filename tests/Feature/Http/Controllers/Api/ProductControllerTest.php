<?php

namespace Tests\Feature\Http\Controllers\Api;

use Faker\Factory;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function non_authenticated_users_cannot_access_the_following_endpoints_for_the_product_api()
    {
        $index = $this->json('GET', '/api/products');
        $index->assertStatus(401);

        $store = $this->json('POST', '/api/products');
        $store->assertStatus(401);

        $show = $this->json('GET', '/api/products/-1');
        $show->assertStatus(401);

        $update = $this->json('PUT', '/api/products/-1');
        $update->assertStatus(401);

        $destroy = $this->json('DELETE', '/api/products/-1');
        $destroy->assertStatus(401);
    }

    /**
     * @test
     */
    public function can_return_a_collection_of_paginated_products()
    {
        $product1 = $this->create('Product');
        $product2 = $this->create('Product');
        $product3 = $this->create('Product');

        $response = $this->actingAs($this->create('User', [], false), 'api')->json('GET', '/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug', 'price', 'created_at']
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => [
                    'current_page', 'last_page', 'from', 'to',
                    'path', 'per_page', 'total'
                ]
            ]);
    }

    /**
     * @test
     */
    public function will_fail_with_validation_errors_when_creating_a_product_with_wrong_inputs()
    {
        $product = $this->create('Product');

        $response = $this->actingAs($this->create('User', [], false), 'api')->json('POST', '/api/products', [
            'name' => $product->name,
            'price' => 'aaa'
        ]);

        $response->assertStatus(422)
            ->assertExactJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'price' => [
                        'The price must be an integer.'
                    ],

                    'name' => [
                        'The name has already been taken.'
                    ]
                ]
            ]);

        $response = $this->actingAs($this->create('User', [], false), 'api')->json('POST', '/api/products', [
            'name' => '',
            'price' => 100
        ]);

        $response->assertStatus(422)
            ->assertExactJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'name' => [
                        'The name field is required.'
                    ]
                ]
            ]);

        $response = $this->actingAs($this->create('User', [], false), 'api')->json('POST', '/api/products', [
            'name' => str_random(65),
            'price' => 100
        ]);

        $response->assertStatus(422)
            ->assertExactJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'name' => [
                        'The name may not be greater than 64 characters.'
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function will_fail_with_validation_errors_when_updating_a_product_with_wrong_inputs()
    {
        $product = $this->create('Product');
        $product2 = $this->create('Product');

        $response = $this->actingAs($this->create('User', [], false), 'api')->json('PUT', "/api/products/$product2->id", [
            'name' => $product->name,
            'price' => 'aaa'
        ]);

        $response->assertStatus(422)
            ->assertExactJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'price' => [
                        'The price must be an integer.'
                    ],

                    'name' => [
                        'The name has already been taken.'
                    ]
                ]
            ]);

        $response = $this->actingAs($this->create('User', [], false), 'api')->json('PUT', "/api/products/$product2->id", [
            'name' => '',
            'price' => 100
        ]);

        $response->assertStatus(422)
            ->assertExactJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'name' => [
                        'The name field is required.'
                    ]
                ]
            ]);

        $response = $this->actingAs($this->create('User', [], false), 'api')->json('PUT', "/api/products/$product2->id", [
            'name' => str_random(65),
            'price' => 100
        ]);

        $response->assertStatus(422)
            ->assertExactJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'name' => [
                        'The name may not be greater than 64 characters.'
                    ]
                ]
            ]);

        $product3 = $this->create('Product');

        $response = $this->actingAs($this->create('User', [], false), 'api')->json('PUT', "/api/products/$product3->id", [
            'name' => $product3->name,
            'price' => 100
        ]);

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function can_create_a_product()
    {
        $faker = Factory::create();

        Storage::fake('public');

        $image = UploadedFile::fake()->image('image.jpg');

        $response = $this->actingAs($this->create('User', [], false), 'api')->json('POST', '/api/products', [
            'name' => $name = $faker->company,
            'slug' => str_slug($name),
            'price' => $price = random_int(10, 100),
            'image' => $image
        ]);

        $response->assertJsonStructure([
            'id', 'image_id', 'name', 'slug', 'price', 'created_at'
        ])
        ->assertJson([
            'name' => $name,
            'slug' => str_slug($name),
            'price' => $price
        ])
        ->assertStatus(201);

        Storage::disk('public')->assertExists("product_images/{$image->hashName()}");

        $this->assertDatabaseHas('products', [
            'name' => $name,
            'slug' => str_slug($name),
            'price' => $price
        ]);
    }

    /**
     * @test
     */
    public function will_fail_with_a_404_if_product_is_not_found()
    {
        $response = $this->actingAs($this->create('User', [], false), 'api')->json('GET', 'api/products/-1');

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function can_return_a_product()
    {
        // Given
        $product = $this->create('Product');

        // When
        $response = $this->actingAs($this->create('User', [], false), 'api')->json('GET', "api/products/$product->id");

        // Then
        $response->assertStatus(200)
            ->assertExactJson([
                'id' => $product->id,
                'image_id' => null,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'created_at' => (string)$product->created_at
            ]);
    }

    /**
     * @test
     */
    public function will_fail_with_a_404_if_product_we_want_to_update_is_not_found()
    {
        $response = $this->actingAs($this->create('User', [], false), 'api')->json('PUT', 'api/products/-1', [
            'name' => 'test'
        ]);

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function can_update_a_product()
    {
        $product = $this->create('Product');

        $response = $this->actingAs($this->create('User', [], false), 'api')->json('PUT', "api/products/$product->id", [
            'name' => $product->name.'_updated',
            'slug' => str_slug($product->name.'_updated'),
            'price' => $product->price + 10
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'id' => $product->id,
                'name' => $product->name.'_updated',
                'slug' => str_slug($product->name.'_updated'),
                'price' => $product->price + 10,
                'created_at' => (string)$product->created_at
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $product->name.'_updated',
            'slug' => str_slug($product->name.'_updated'),
            'price' => $product->price + 10,
            'created_at' => (string)$product->created_at,
            'updated_at' => (string)$product->updated_at
        ]);
    }

    /**
     * @test
     */
    public function will_fail_with_a_404_if_product_we_want_to_delete_is_not_found()
    {
        $response = $this->actingAs($this->create('User', [], false), 'api')->json('DELETE', 'api/products/-1');

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function can_delete_a_product()
    {
        $product = $this->create('Product');

        $response = $this->actingAs($this->create('User', [], false), 'api')->json('DELETE', "api/products/$product->id");

        $response->assertStatus(204)
            ->assertSee(null);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
