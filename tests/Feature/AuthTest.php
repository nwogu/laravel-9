<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_user_can_access_api_with_valid_api_token()
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password'),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->get('/api/user?api_token='.$token);

        $response->assertStatus(200);
    }

    public function test_user_cannot_access_api_with_invalid_api_token()
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password'),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;
        $token .= "|invalid";

        $response = $this->get('/api/user?api_token='.$token);

        $response->assertStatus(401);
    }
}
