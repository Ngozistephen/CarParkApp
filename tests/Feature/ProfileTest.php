<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function testUserCanGetTheirProfile()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/profile');

        $response->assertStatus(200)
            ->assertJsonStructure(['name', 'email'])
            ->assertJsonCount(2)
            ->assertJsonFragment(['name' => $user->name]);
    }

    public function testUserCanUpdateEmailAndName()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson('api/v1/profile', [
            'name' => 'Uche Update',
            'email' => 'uche_update@eample.com',
        ]);

        $response->assertStatus(202)
            ->assertJsonStructure(['name', 'email'])
            ->assertJsonCount(2)
            ->assertJsonFragment(['name' => 'Uche Update']);

        $this->assertDatabaseHas('users', [ 
            'name' => 'Uche Update',
            'email' => 'uche_update@eample.com',
        ]);
    }


    public function testUserCanChangePassword()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson('api/v1/password', [
            'current_password' => 'password',
            'password' => 'y12345678',
            'password_confirmation' => 'y12345678',
        ]);

        $response->assertStatus(202);
    }

    // Assignment to do the negative side
}
