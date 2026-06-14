<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user  = User::factory()->create();
        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    private function auth(): static
    {
        return $this->withToken($this->token);
    }

    // --- Listing & Filtering ---

    public function test_can_list_products_with_pagination(): void
    {
        $category = Category::factory()->create();
        Product::factory(20)->create(['category_id' => $category->id]);

        $response = $this->auth()->getJson('/api/products?per_page=5');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'name', 'sku', 'price', 'formattedPrice', 'stockQuantity', 'category']],
            ])
            ->assertJsonCount(5, 'data');
    }

    public function test_can_filter_products_by_category(): void
    {
        $catA = Category::factory()->create();
        $catB = Category::factory()->create();
        Product::factory(3)->create(['category_id' => $catA->id]);
        Product::factory(2)->create(['category_id' => $catB->id]);

        $response = $this->auth()->getJson("/api/products?category_id={$catA->id}");

        $response->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_can_filter_products_by_price_range(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'price' => 10.00]);
        Product::factory()->create(['category_id' => $category->id, 'price' => 50.00]);
        Product::factory()->create(['category_id' => $category->id, 'price' => 200.00]);

        $response = $this->auth()->getJson('/api/products?price_min=20&price_max=100');

        $response->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_inactive_products_are_excluded_from_listing(): void
    {
        $category = Category::factory()->create();
        Product::factory(3)->create(['category_id' => $category->id, 'is_active' => true]);
        Product::factory(2)->create(['category_id' => $category->id, 'is_active' => false]);

        $this->auth()->getJson('/api/products')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_can_filter_by_low_stock(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'stock_quantity' => 5]);
        Product::factory()->create(['category_id' => $category->id, 'stock_quantity' => 100]);

        $this->auth()->getJson('/api/products?low_stock=1')
            ->assertOk() 
            ->assertJsonCount(1, 'data');
    }

    // --- CRUD ---

    public function test_can_create_product(): void
    {
        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        $payload = [
            'category_id'    => $category->id,
            'name'           => 'Test Widget',
            'sku'            => 'TW-0001',
            'price'          => 29.99,
            'stock_quantity' => 50,
            'supplier_ids'   => [$supplier->id],
        ];

        $response = $this->auth()->postJson('/api/products', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Test Widget')
            ->assertJsonPath('data.sku', 'TW-0001')
            ->assertJsonPath('data.formattedPrice', '$29.99');

        $this->assertDatabaseHas('products', ['sku' => 'TW-0001']);
        $this->assertDatabaseHas('product_supplier', ['supplier_id' => $supplier->id]);
    }

    public function test_create_product_validates_required_fields(): void
    {
        $this->auth()->postJson('/api/products', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['category_id', 'name', 'sku', 'price', 'stock_quantity']);
    }

    public function test_create_product_rejects_duplicate_sku(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'sku' => 'DUPE-001']);

        $this->auth()->postJson('/api/products', [
            'category_id'    => $category->id,
            'name'           => 'Another',
            'sku'            => 'DUPE-001',
            'price'          => 9.99,
            'stock_quantity' => 10,
        ])->assertStatus(422)->assertJsonValidationErrors(['sku']);
    }

    public function test_can_show_product(): void
    {
        $product = Product::factory()->create();

        $this->auth()->getJson("/api/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonStructure(['data' => ['id', 'name', 'sku', 'category', 'suppliers']]);
    }

    public function test_can_update_product(): void
    {
        $product  = Product::factory()->create(['price' => 10.00]);
        $category = Category::factory()->create();

        $this->auth()->putJson("/api/products/{$product->id}", [
            'category_id' => $category->id,
            'price'       => 99.99,
        ])->assertOk()
            ->assertJsonPath('data.price', '99.99');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'price' => 99.99]);
    }

    public function test_can_soft_delete_and_restore_product(): void
    {
        $product = Product::factory()->create();

        $this->auth()->deleteJson("/api/products/{$product->id}")
            ->assertOk()
            ->assertJson(['message' => 'Product deleted successfully.']);

        $this->assertSoftDeleted('products', ['id' => $product->id]);

        $this->auth()->postJson("/api/products/{$product->id}/restore")
            ->assertOk()
            ->assertJson(['message' => 'Product restored successfully.']);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'deleted_at' => null]);
    }

    public function test_sku_is_stored_uppercase(): void
    {
        $category = Category::factory()->create();

        $this->auth()->postJson('/api/products', [
            'category_id'    => $category->id,
            'name'           => 'Case Test',
            'sku'            => 'lower-sku-001',
            'price'          => 5.00,
            'stock_quantity' => 1,
        ])->assertStatus(201);

        $this->assertDatabaseHas('products', ['sku' => 'LOWER-SKU-001']);
    }

    // --- Supplier sync ---

    public function test_updating_supplier_ids_syncs_pivot(): void
    {
        $product   = Product::factory()->create();
        $supplierA = Supplier::factory()->create();
        $supplierB = Supplier::factory()->create();

        $product->suppliers()->attach($supplierA->id);

        $this->auth()->putJson("/api/products/{$product->id}", [
            'supplier_ids' => [$supplierB->id],
        ])->assertOk();

        $this->assertDatabaseMissing('product_supplier', ['product_id' => $product->id, 'supplier_id' => $supplierA->id]);
        $this->assertDatabaseHas('product_supplier', ['product_id' => $product->id, 'supplier_id' => $supplierB->id]);
    }
}
