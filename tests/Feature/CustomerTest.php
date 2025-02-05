<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\Customer;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_customer_can_have_projects(): void
    {
        $this->authorize();
        // $customer = Customer::factory()
        //     ->for($this->user)
        //     ->has(Project::factory()->count(3))
        //     ->create();

        //$customer = Customer::factory()->standard($this->user->id)->create();

        $customer = Customer::factory()->for($this->user)->premium()->withCurrentProjectsAndIntervals(3)->create();

        $response = $this->getJson('/api/customers/' . $customer->id);
        $response
            ->assertStatus(200)
            ->dump()
            ->assertJson(['message' => 'Encontrado un cliente'])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'name',
                    'email',
                    'projects'
                ]
            ]);
        $this->assertCount(3, $response->json('data.projects'));

    }
}
