<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $user;
    private $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and get JWT token
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->token = $response->json('data.token');
    }

    #[Test]

    public function it_can_list_products()
    {
        Product::factory()->count(5)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Success',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'price',
                            'stock',
                            'image',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                ],
            ]);
    }

    #[Test]

    public function it_can_create_a_product()
    {
        Storage::fake('public');

        $data = [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'stock' => $this->faker->numberBetween(1, 100),
            'image' => UploadedFile::fake()->image('product.jpg'),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/products', $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'stock',
                    'image',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('products', [
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock'],
        ]);

        Storage::disk('local')->assertExists($response->json('data.image'));
    }

    #[Test]

    public function it_can_show_a_product()
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => (string) $product->price,
                    'stock' => $product->stock,
                ],
            ]);
    }

    #[Test]

    public function it_can_update_a_product()
    {
        $product = Product::factory()->create();
        Storage::fake('public');

        $data = [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'stock' => $this->faker->numberBetween(1, 100),
            'image' => UploadedFile::fake()->image('updated-product.jpg'),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/products/{$product->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock'],
        ]);
    }

    #[Test]

    public function it_can_delete_a_product()
    {
        $product = Product::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Product deleted successfully',
            ]);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    #[Test]

    public function it_validates_required_fields_when_creating_product()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/products', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price', 'stock', 'image']);
    }

    #[Test]

    public function it_requires_authentication_for_protected_routes()
    {
        // Try to create without token
        $response = $this->postJson('/api/products', [
            'name' => 'Test Product',
        ]);
        $response->assertStatus(401);

        // Try to update without token
        $product = Product::factory()->create();
        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Product',
        ]);
        $response->assertStatus(401);

        // Try to delete without token
        $response = $this->deleteJson("/api/products/{$product->id}");
        $response->assertStatus(401);
    }
}
