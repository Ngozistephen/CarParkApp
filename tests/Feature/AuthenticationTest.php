<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function testUserCanLoginWithCorrectCredentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/auth/login', [
            
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(201);
    }

    
    public function testUserCanNotLoginWithInCorrectCredentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/auth/login', [

            'email' => $user->email,
            'password' => 'Wrong_password',
        ]);

        $response->assertStatus(422);
    }

    public function testUserCanRegisterWithCorrectCredentials()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Uche Test',
            'email' => 'uche@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201)->assertJsonStructure([ 'access_token', ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Uche Test',
            'email' => 'uche@example.com',
        ]);
    }

    
    public function testUserCannotRegisterWithInCorrectCredentials()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Uche Test',
            'email' => 'uche@example.com',
            'password' => 'password',
            'password_confirmation' => 'wrong_password'
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseMissing('users', [
            'name' => 'Uche Test',
            'email' => 'uche@example.com',
        ]);
    }
}
