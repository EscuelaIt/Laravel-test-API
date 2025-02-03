<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function unauthorized_user_canot_get_categories_list(): void
    {
        $response = $this->getJson('/api/categories');
        $response->assertStatus(401);
    }

    #[Test]
    public function authorized_user_can_get_categories_list(): void
    {
        $this->authorize();
        Category::factory()->for($this->user)->count(3)->create();
        $response = $this->getJson('/api/categories');
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['message','data'])
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function it_returns_empty_category_list_if_categories_are_owned_by_other_users() {
        Category::factory()->count(3)->create();
        $response = $this->authorize()->getJson('api/categories');
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['message','data'])
            ->assertJsonCount(0, 'data');
    }

    #[Test]
    public function it_can_show_a_category() {
        $this->authorize();
        $category = Category::factory()->for($this->user)->create();
        $response = $this->getJson('/api/categories/' . $category->id);
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['message','data'])
            ->assertJsonFragment(['name'=> $category->name])
            ->assertJsonFragment(['user_id'=> $this->user->id]);
    }

    #[Test]
    public function it_canot_show_a_category_of_other_user() {
        $category = Category::factory()->create();
        $response = $this->authorize()->getJson('api/categories/' . $category->id);
        $response
            ->assertStatus(403)
            ->assertJson([
                "message" => "No estás autorizado para realizar esta acción"
            ]);
    }

    #[Test]
    public function it_creates_a_category(): void
    {
        $response = $this->authorize()->postJson('/api/categories', [
            'name' => 'computers',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['message','data'])
            ->assertJsonFragment(['name' => 'computers'])
            ->assertJsonFragment(['user_id' => $this->user->id]);
    }

    #[Test]
    public function it_returns_validation_error_with_incomplete_data() {
        $response = $this->authorize()->postJson('/api/categories', []);

        $response->assertStatus(400);
        $response->assertInvalid('name');
    }

    #[Test]
    public function it_can_update_a_category() {
        $newCategoryName = 'Un nuevo nombre';
        $this->authorize();
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        $response = $this->putJson('api/categories/' . $category->id, ['name' => $newCategoryName]);
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['message','data'])
            ->assertJsonFragment(['name' => $newCategoryName])
            ->assertJsonFragment(['user_id' => $this->user->id]);
    }

    #[Test]
    public function it_canot_update_a_category_owned_by_other_user() {
        $newCategoryName = 'Un nuevo nombre';
        $category = Category::factory()->create();
        $response = $this->authorize()->putJson('api/categories/' . $category->id, ['name' => $newCategoryName]);
        $response->assertStatus(403);
        $this->assertDatabaseHas('categories', [
            'name' => $category->name,
            'user_id' => $category->user_id,
        ]);
    }

    #[Test]
    public function it_returns_validation_error_with_incomplete_data_on_update() {
        $this->authorize();
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        $response = $this->putJson('api/categories/' . $category->id, []);

        $response->assertStatus(400);
        $response->assertInvalid('name');
    }

    #[Test]
    public function it_can_delete_a_category() {
        $this->authorize();
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        $response = $this->deleteJson('api/categories/' . $category->id);
        $response
        ->dump()
            ->assertStatus(200)
            ->assertJsonStructure(['message','data'])
            ->assertJson([
                'message' => "Categoría borrada",
                'data' => null,
            ]);
    }

    #[Test]
    public function it_canot_delete_a_category_owned_by_other_user() {
        $category = Category::factory()->create();
        $response = $this->authorize()->deleteJson('api/categories/' . $category->id);
        $response->assertStatus(403);
    }
}
