<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    //=========================Generate Token Tests====================================//

    /**
     * @return void
     * Function to test generation of token used to authenticate API requests
     */
    public function test_successful_token_generation(): void
    {
        $test_email     = 'test@example.com';
        $test_password  = 'password123';
        $test_user      = User::factory()->create(
                            [
                                'email'     => $test_email,
                                'password'  => hash('sha256', $test_password),   //using sha256 because Auth::createToken() uses sha256
                            ]
                        );

        $response       = $this->postJson(
                                route('generate_token'),
                                [
                                    'email'     => $test_email,
                                    'password'  => $test_password,
                                ]
                        );

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'token']);
        $response->assertJson(['message' => 'Login successful']);
    }

    /**
     * @return void
     * Function to test generation of token used to authenticate API requests
     */
    public function test_failed_login_due_to_invalid_credentials(): void
    {
        $test_email     = 'test@example.com';
        $test_password  = 'password123';
        $wrong_password = 'password1234';
        $test_user      = User::factory()->create(
                            [
                                'email'     => $test_email,
                                'password'  => hash('sha256', $test_password),   //using sha256 because Auth::createToken() uses sha256
                            ]
                        );

        $response       = $this->postJson(
                                route('generate_token'),
                                [
                                    'email'     => $test_email,
                                    'password'  => $wrong_password,
                                ]
                        );

        $response->assertStatus(401);
        $response->assertJsonStructure(['message']);
    }


    /**
     * @return void
     * Function to validate token generation
     */
    public function test_failed_token_gen_missing_fields(): void
    {
        $response = $this->postJson(route('generate_token'), [
            'email' => 'test@example.com',                                      // Missing fields
        ]);
        $response->assertStatus(422);                                     // Validation error
        $response->assertJsonValidationErrors(['password']);
    }


    //=========================Index Tests====================================//
    /**
     * @return void
     * Function to check the successful user fetch of tasks by making a GET request to index route
     */
    public function test_successful_user_fetch_tasks(): void
    {
        $test_token = 'TEST_TOKEN_VALUE';
        $response   = $this->withHeaders(['Authorization' => 'Bearer '.$test_token])->getJson(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['tasks']);
    }

    /**
     * @return void
     * Function to check the successful fetch of tasks where count = 0 by making a GET request to index route
     */
    public function test_successful_user_fetch_empty_tasks(): void
    {
        $test_token = 'TEST_TOKEN_VALUE';
        $response   = $this->withHeaders(['Authorization' => 'Bearer '.$test_token])->getJson(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['tasks']);
        $response->assertJsonCount(0, 'tasks');                                        // Check that exactly $no_of_tasks tasks are returned
    }


    /**
     * @return void
     * Function to check the successful fetch of tasks where count = 0 by making a GET request to index route
     */
    public function test_unauthenticated_user_access(): void
    {
        $test_token = 'TEST_TOKEN_VALUE';
        $response   = $this->withHeaders(['Authorization' => 'Bearer '.$test_token])->getJson(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['tasks']);
        $response->assertJsonCount(0, 'tasks');                                        // Check that exactly 0 tasks are returned
    }


    /**
     * @return void
     * Function to test non existent user or wrong bearer token
     */
    public function test_nonexistent_user_returns_error(): void
    {
        $test_token = 'TEST_TOKEN_VALUE';
        $response   = $this->withHeaders(['Authorization' => 'Bearer '.$test_token])->getJson(route('tasks.index'));

        $response->assertStatus(404);
        $response->assertJsonStructure(['message']);
    }


    //=========================Task Creation Tests====================================//
    /**
     * @return void
     * Function to check successful user task creation
     */
    public function test_successful_user_task_creation(): void
    {
        $test_token = 'TEST_TOKEN_VALUE';
        $test_data  = [
            'title'         => 'New Task',
            'description'   => 'This is a test description.',
            'due_date'      => '2025-03-21',
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$test_token])->postJson(route('tasks.store'), $test_data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', $test_data);
    }

    /**
     * @return void
     * Function to test validation upon task creation
     */
    public function test_task_creation_validation_errors(): void
    {

        $test_token = 'TEST_TOKEN_VALUE';
        $invalid_test_data  = [
            'title'         => '',
            'description'   => 'This is fine',
            'due_date'      => 'not-a-date',
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$test_token])->postJson(route('tasks.store'), $invalid_test_data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'due_date']);
        $this->assertDatabaseCount('tasks', 0);                                 // No tasks should be created
    }

    /**
     * @return void
     * Function to simulate a mock case of throwing exception and handling it while creating task
     */
    public function test_task_creation_exception_errors(): void
    {

        $test_token = 'TEST_TOKEN_VALUE';
        $test_data  = [
            'title'         => 'New Task',
            'description'   => 'This is a test description.',
            'due_date'      => '2025-03-21',
        ];

        $this->mock(Task::class, function ($mock) {                                 //Simulating throwing of an exception
            $mock->shouldReceive('create')->andThrow(new \Exception('Error while creating task!'));
        });

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$test_token])->postJson(route('tasks.store'), $test_data);
        $response->assertStatus(400);
        $response->assertJson(['message' => 'Error while creating task!']);
        $this->assertDatabaseCount('tasks', 0);                                 // No tasks should be created
    }

    /**
     * @return void
     * Function to check how task creation task handles requests without bearer token
     */
    public function test_unauthenticated_user_task_creation(): void
    {
        $test_token = 'TEST_TOKEN_VALUE';
        $invalid_test_data  = [
            'title'         => '',
            'description'   => 'This is fine',
            'due_date'      => 'not-a-date',
        ];

        $response = $this->postJson(route('tasks.store'), $invalid_test_data);         //No Authorization header given
        $response->assertStatus(401);                                                  //request denied without auth bearer token
    }


    //=========================Task Fetching Single Row Tests====================================//
    /**
     * @return void
     * Test fetching a single task of same user
     */
    public function test_user_getting_one_task(): void
    {
        $test_token     = 'TEST_TOKEN_VALUE';
        $test_task_id   = 1;
        $test_user_id   = 1;
        $response       = $this->withHeaders(['Authorization' => 'Bearer '.$test_token])->getJson(route('tasks.show', ['task' => $test_task_id]));

        $response->assertStatus(200);
        $response->assertJson(
            [
                'single_task' => [
                    'id'            => $test_task_id,
                    'title'         => "Random Title",
                    'description'   => "Random Description",
                    'due_date'      => "Random Date",
                    'user_id'       => $test_user_id
                ]
            ]
        );
    }


    /**
     * @return void
     * Test fetching a single task of same user
     */
    public function test_user_getting_one_task_of_another(): void
    {
        $test_token     = 'TEST_TOKEN_VALUE';
        $test_task_id   = 1;
        $response       = $this->withHeaders(['Authorization' => 'Bearer '.$test_token])->getJson(route('tasks.show', ['task' => $test_task_id]));

        $response->assertStatus(403);
    }


    /**
     * @return void
     * Test fetching a single task of same user
     */
    public function test_unauthenticated_user_fetch_single_task(): void
    {
        $test_token     = 'TEST_TOKEN_VALUE';
        $test_task_id   = 1;
        $response       = $this->getJson(route('tasks.show', ['task' => $test_task_id]));

        $response->assertStatus(401);   //validation error
    }


    //=========================Task update single row Tests====================================//
    /**
     * @return void
     * Function to test successful update of task for a validated user
     */
    public function test_success_update_of_task_for_valid_user()
    {
        $test_token     = 'TEST_TOKEN_VALUE';
        $test_task_id   = 1;
        $test_data      = [
            'title'         => 'New Task',
            'description'   => 'This is a test description.',
            'due_date'      => '2025-03-21',
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$test_token])->putJson(route('tasks.update', ['task' => $test_task_id]),$test_data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', $test_data);
    }


    /**
     * @return void
     * Function to test updating with invalid data
     */
    public function test_validation_fails_with_invalid_data()
    {
        $test_token     = 'TEST_TOKEN_VALUE';
        $test_task_id   = 1;
        $test_data      = [
            'title'         => '',
            'description'   => 'This is a test description.',
            'due_date'      => '',
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$test_token])->putJson(route('tasks.update', ['task' => $test_task_id]),$test_data);
        $response->assertStatus(422);
        $response->assertJsonStructure(['message']);
    }


    /**
     * @return void
     * Function to test if auth bearer token is not sent
     */
    public function test_update_fails_when_user_is_not_authorized()
    {
        $test_token     = 'TEST_TOKEN_VALUE';
        $test_task_id   = 1;
        $test_data      = [
            'title'         => 'New Task',
            'description'   => 'This is a test description.',
            'due_date'      => '2025-03-21',
        ];

        $response = $this->putJson(route('tasks.update', ['tasks' => $test_task_id]),$test_data);
        $response->assertStatus(404);
        $response->assertJsonStructure(['message']);
    }

    /**
     * @return void
     * Function to check if task id does not exist
     */
    public function test_update_fails_when_task_does_not_exist()
    {
        $test_token     = 'TEST_TOKEN_VALUE';
        $test_task_id   = 1;
        $response = $this->withHeaders(['Authorization' => 'Bearer '.$test_token])->putJson(route('tasks.update', ['task' => $test_task_id]));
        $response->assertStatus(404);
        $response->assertJsonStructure(['message']);
    }


    //=========================Delete task Tests====================================//
    /**
     * @return void
     * Function to check for successful task deletion
     */
    public function test_successful_task_deletion_by_authorized_user()
    {
        $test_token     = 'TEST_TOKEN_VALUE';
        $test_task_id   = 1;
        $response       = $this->withHeaders(['Authorization' => 'Bearer '.$test_token])->deleteJson(route('tasks.destroy', ['task' => $test_task_id]));

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
        $this->assertDatabaseMissing('tasks', ['id' => $test_task_id]);
    }

    /**
     * @return void
     * Function to check for handling unauthorized user deletion
     */
    public function test_unauthorized_user_task_delete()
    {
        $test_token     = 'TEST_TOKEN_VALUE';
        $test_task_id   = 1;
        $response       = $this->deleteJson(route('tasks.destroy', ['task' => $test_task_id]));

        $response->assertStatus(403);
        $response->assertJsonStructure(['message']);
        $this->assertDatabaseHas('tasks', ['id' => $test_task_id]);
    }

    /**
     * @return void
     * Function to check for handling non existent task deletion
     */
    public function test_task_delete_not_existing()
    {
        $test_token     = 'TEST_TOKEN_VALUE';
        $test_task_id   = 1;
        $response       = $this->withHeaders(['Authorization' => 'Bearer '.$test_token])->deleteJson(route('tasks.destroy', ['task' => $test_task_id]));

        $response->assertStatus(404);
        $response->assertJsonStructure(['message']);
        $this->assertDatabaseMissing('tasks', ['id' => $test_task_id]);
    }

}

