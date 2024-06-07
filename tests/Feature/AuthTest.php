<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * A feature test for the register endpoint.
     */
    public function test_register_feature(): void
    {
        User::where('email', 'test@example.com')->delete();
        $response = $this->post('/api/register');

        $response->assertStatus(422);

        $response = $this->post('/api/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'test123',
            'password_confirmation' => 'test123',
        ]);

        $response->assertStatus(200);
    }

    public function test_login_feature(): void
    {
        $response = $this->post('/api/login');

        $response->assertStatus(422);

        $response = $this->post('/api/login', [
            'email' => 'test@example.com',
            'password' => 'test123',
        ]);
        $response->assertStatus(200);

        $data = $response->json();
        $this->assertNotNull($data['token']);
        $this->assertEquals($data['user']['role'], 'user');
    }
}
