<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Tests\TestCase;

class TaskTest extends TestCase
{
    private function loginUser()
    {
        return User::where('email', 'test@example.com')->first();
    }

    private function loginAdmin()
    {
        return User::where('email', 'admin@example.com')->first();
    }

    /**
     * A feature test for creating tasks.
     */
    public function test_create_task(): void
    {
        $user = $this->loginUser();
        $admin = $this->loginAdmin();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/tasks');

        $response->assertStatus(401);

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->post('/api/tasks');

        $response->assertStatus(422);

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->post('/api/tasks', [
                'title' => 'Task Title',
                'description' => 'Task Description',
                'due_date' => '2024-06-30',
            ]);

        $response->assertStatus(201);

        $data = $response->json();

        $this->assertEquals($data['user_id'], $user->id);

        $response = $this->actingAs($admin)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->post('/api/tasks', [
                'title' => 'Admin Task Title',
                'description' => 'Task Description',
                'due_date' => '2024-06-30',
            ]);

        $response->assertStatus(201);
    }

    /**
     * A feature test for showing tasks.
     */
    public function test_show_task(): void
    {
        $user = $this->loginUser();
        $admin = $this->loginAdmin();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/api/tasks');

        $response->assertStatus(401);

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->get('/api/tasks');

        $response->assertStatus(200);

        $this->assertLessThanOrEqual(Task::count(), count($response->json()));

        $response = $this->actingAs($admin)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->get('/api/tasks');

        $response->assertStatus(200);

        $this->assertEquals(count($response->json()), Task::count());

        $user_task = Task::where('user_id', $user->id)->first();
        $admin_task = Task::where('user_id', $admin->id)->first();

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->get('/api/tasks/'.$user_task->id);

        $response->assertStatus(200);

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->get('/api/tasks/'.$admin_task->id);

        $response->assertStatus(403);

        $response = $this->actingAs($admin)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->get('/api/tasks/'.$user_task->id);

        $response->assertStatus(200);

        $response = $this->actingAs($admin)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->get('/api/tasks/'.$admin_task->id);

        $response->assertStatus(200);
    }

    /**
     * A feature test for showing a task details.
     */
    public function test_show_task_details(): void
    {
        $user = $this->loginUser();
        $admin = $this->loginAdmin();

        $user_task = Task::where('user_id', $user->id)->first();
        $admin_task = Task::where('user_id', $admin->id)->first();

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->get('/api/tasks/'.$user_task->id);

        $response->assertStatus(200);

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->get('/api/tasks/'.$admin_task->id);

        $response->assertStatus(403);

        $response = $this->actingAs($admin)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->get('/api/tasks/'.$user_task->id);

        $response->assertStatus(200);

        $response = $this->actingAs($admin)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->get('/api/tasks/'.$admin_task->id);

        $response->assertStatus(200);
    }

    /**
     * A feature test for updating tasks.
     */
    public function test_update_task(): void
    {
        $user = $this->loginUser();
        $admin = $this->loginAdmin();

        $user_task = Task::where('user_id', $user->id)->first();
        $admin_task = Task::where('user_id', $admin->id)->first();

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->put('/api/tasks/'.$user_task->id, ['status' => 'done']);

        $response->assertStatus(200);

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->put('/api/tasks/'.$admin_task->id);

        $response->assertStatus(403);

        $response = $this->actingAs($admin)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->put('/api/tasks/'.$user_task->id, ['status' => 'pending']);

        $response->assertStatus(200);

        $response = $this->actingAs($admin)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->put('/api/tasks/'.$admin_task->id, ['status' => 'done']);

        $response->assertStatus(200);
    }

    /**
     * A feature test for deleting tasks.
     */
    public function test_delete_task(): void
    {
        $user = $this->loginUser();
        $admin = $this->loginAdmin();

        $user_task = Task::where('user_id', $user->id)->first();
        $admin_task = Task::where('user_id', $admin->id)->first();

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->delete('/api/tasks/'.$user_task->id);

        $response->assertStatus(200);

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->delete('/api/tasks/'.$admin_task->id);

        $response->assertStatus(403);

        $response = $this->actingAs($admin)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->delete('/api/tasks/'.$admin_task->id);

        $response->assertStatus(200);
    }

    /**
     * A feature test for deleting tasks.
     */
    public function test_show_deleted_tasks(): void
    {
        $user = $this->loginUser();
        $admin = $this->loginAdmin();

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->get('/api/tasks/deleted');

        $response->assertStatus(403);

        $response = $this->actingAs($admin)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->get('/api/tasks/deleted');

        $response->assertStatus(200);
    }
}
