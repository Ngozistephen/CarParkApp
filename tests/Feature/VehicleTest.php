<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VehicleTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function testUserCanGetTheirOwnVehicles()
    {
        $ngozi = User::factory()->create();

        $vehicleForNgozi = Vehicle::factory()->create([
            'user_id' => $ngozi->id
        ]);

        $chi = User::factory()->create();

        $vehicleForChi = Vehicle::factory()->create([
            'user_id' => $chi->id
        ]);

        $response = $this->actingAs($ngozi)->getJson('/api/v1/vehicles');

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.plate_number', $vehicleForNgozi->plate_number)
            ->assertJsonMissing($vehicleForChi->toArray());
    }

    public function testUserCanCreateVehicles()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/vehicles', [
            'plate_number' => 'AAA222454',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [ '0' => 'plate_number'],
            ])
            ->assertJsonPath('data.plate_number', 'AAA222454');

            $this->assertDatabaseHas('vehicles', [
                'plate_number' => 'AAA222454',
            ]);
    }
    public function testUserCanUpdateTheirVehicles()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
 
        $response = $this->actingAs($user)->putJson('/api/v1/vehicles/' . $vehicle->id, [
            'plate_number' => 'AAA12345',
        ]);
 
        $response->assertStatus(202)
            ->assertJsonStructure(['plate_number'])
            ->assertJsonPath('plate_number', 'AAA12345');
 
        $this->assertDatabaseHas('vehicles', [
            'plate_number' => 'AAA12345',
        ]);
    }

    public function testUserCanDeleteTheirVehicle()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
 
        $response = $this->actingAs($user)->deleteJson('/api/v1/vehicles/' . $vehicle->id);
 
        $response->assertNoContent();
 
        $this->assertDatabaseMissing('vehicles', [
            'id' => $vehicle->id,
            'deleted_at' => NULL
        ])->assertDatabaseCount('vehicles', 1); // For soft delete
    }



}
