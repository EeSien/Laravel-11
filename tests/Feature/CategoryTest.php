<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->token = User::factory()->create()->createToken('test')->plainTextToken;
    }

    public function test_can_list_categories(): void
    {
        Category::factory(3)->create();

        $this->withToken($this->token)
            ->getJson('/api/categories')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_category(): void
    {
        $this->withToken($this->token)
            ->postJson('/api/categories', [
                'name'        => 'Electronics',
                'slug'        => 'electronics',
                'description' => 'Electronic goods',
            ])
            ->assertStatus(201)
            ->assertJsonPath('data.name', 'Electronics');

        $this->assertDatabaseHas('categories', ['slug' => 'electronics']);
    }

    public function test_can_update_category(): void
    {
        $category = Category::factory()->create();

        $this->withToken($this->token)
            ->putJson("/api/categories/{$category->id}", ['name' => 'Updated Name'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated Name');
    }

    public function test_can_delete_category(): void
    {
        $category = Category::factory()->create();

        $this->withToken($this->token)
            ->deleteJson("/api/categories/{$category->id}")
            ->assertOk();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
