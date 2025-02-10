<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\Customer;
use Database\Factories\CustomerFactory;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

class CustomerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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

    #[Test]
    public function unautorized_user_is_unable_to_create_a_customer(): void
    {
        $response = $this->postJson('/api/customers', []);
        $response
            ->assertStatus(401)
            ->assertJson(fn (AssertableJson $json) =>
                $json
                    ->has('message')
                    ->where('message', 'Unauthenticated.')
                    ->missing('data')
            );
    }

    #[Test]
    public function it_creates_a_customer(): void
    {
        $this->authorize();
        $customer = Customer::factory()->raw([
            'email' => 'xxx@example.com'
        ]);
        // $customerName = $this->faker->name();
        // $customerEmail = $this->faker->email();
        // $customerTelephone = $this->faker->phoneNumber();

        $response = $this->postJson('/api/customers', $customer);

        $response
            ->assertStatus(200)
            ->dump()
            ->assertJson(fn (AssertableJson $json) =>
                $json
                    ->has('message')
                    ->has('data', fn(AssertableJson $data) =>
                        $data
                            ->has('updated_at')
                            ->where('email', fn(string $email) => str($email)->endsWith('@example.com'))
                            ->where('telephone', $customer['telephone'])
                            ->where('user_id', $this->user->id)
                            ->etc()
                    )
                    ->etc()
        );

    }

    #[Test]
    public function a_customer_can_have_projects_fluent(): void
    {
        $this->authorize();

        $customer = Customer::factory()->for($this->user)->premium()->withCurrentProjectsAndIntervals(3)->create();

        $response = $this->getJson('/api/customers/' . $customer->id);
        $response
            ->assertStatus(200)
            ->assertJson(['message' => 'Encontrado un cliente'])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'name',
                    'email',
                    'projects'
                ]
            ])
            ->assertJson(fn (AssertableJson $json) =>
                    $json
                        ->has('data.projects', 3)
                        ->has('data.projects.0', fn (AssertableJson $project) =>
                            $project->where('name', $customer->projects[0]->name)
                                    ->etc()
                            )
                        ->etc()
            );

    }

}
