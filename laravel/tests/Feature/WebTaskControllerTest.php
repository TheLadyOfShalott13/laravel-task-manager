<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WebTaskControllerTest extends TestCase
{

    use RefreshDatabase;

    //=============================START OF INDEX TESTS========================================//

    /**
     * @test
     * @return void
     * Function to check if authenticated users can only access their tasks and returns all their tasks correctly
     */
    public function can_authenticated_user_access_their_full_tasks_list(): void
    {
        $user1 = User::factory()->create();                                          // Create user1
        $user2 = User::factory()->create();                                          // Create user2

        Task::factory()->count(3)->create([                                    // Create 3 tasks for this particular user ID
            'user_id' => $user1->id,
        ]);
        Task::factory()->count(5)->create([                                    // Create 5 tasks for this particular user ID
            'user_id' => $user2->id,
        ]);

        $response1 = $this->actingAs($user1)->get(route('tasks.index'));       // Simulate the user being authenticated and making a GET request
        $response2 = $this->actingAs($user2)->get(route('tasks.index'));       // Simulate the user being authenticated and making a GET request

        $response1->assertStatus(200);                                         // Assert that user1's request returns status 200 and correct tasks
        $response1->assertViewHas('tasks', function ($tasks) use ($user1) {
            if ($tasks->count() !== 3)                                               // Check the total count is exactly 3
                return false;
            return $tasks->every(fn($task) => $task->user_id === $user1->id);        // Verify all tasks belong to user1
        });

        $response2->assertStatus(200);                                         // Assert that user1's request returns status 200 and correct tasks
        $response2->assertViewHas('tasks', function ($tasks) use ($user2) {
            if ($tasks->count() !== 5)                                               // Check the total count is exactly 3
                return false;
            return $tasks->every(fn($task) => $task->user_id === $user2->id);        // Verify all tasks belong to user1
        });
    }


    /**
     * @test
     * @return void
     * Test to make sure no non logged in users can access tasks and redirection works
     */
    public function can_guest_access_tasks_page(): void
    {
        $response = $this->get(route('tasks.index'));                       // Trying to access without authentication
        $response->assertRedirect(route('login'));                          // Force redirect to login
    }

    //=============================END OF INDEX TESTS========================================//

    //=============================START OF CREATE TESTS========================================//

    /**
     * @test
     * @return void
     * Function to check if create() path works
     */
    public function can_authenticated_user_access_create_task_page(): void
    {
        $user = User::factory()->create();                                          // Create a user
        $response = $this->actingAs($user)->get(route('tasks.create'));       // Simulate the user being authenticated and making a GET request
        $response->assertStatus(200);                                        // Assert the response status is 200 (OK)
        $response->assertViewIs('tasks.create');                             // Assert the `tasks.create` view is returned
    }


    /**
     * @test
     * @return void
     * Function to test that the User's completed tasks are being fetched correctly
     */
    public function can_authenticated_user_get_completed_and_pending_tasks(): void
    {
        $user = User::factory()->create();                                          //Create a new user

        $completed_no = 3;
        Task::factory()->count($completed_no)->create([                             //Creating completed tasks
            'user_id' => $user->id,
            'completed' => now(),
        ]);

        $pending_no = 2;
        Task::factory()->count($pending_no)->create([                               //Created pending tasks
            'user_id' => $user->id,
            'completed' => null,
        ]);

        // Fetch tasks for the user as completed and pending
        $completed_tasks = $this->actingAs($user)->get(route('tasks.index',['status' => 'completed']));
        $pending_tasks = $this->actingAs($user)->get(route('tasks.index',['status' => 'pending']));

        //Both routes must return 200
        $completed_tasks->assertStatus(200);
        $pending_tasks->assertStatus(200);

        // Assert that only completed tasks are included in the response  (completed field != NULL)
        $completed_tasks->assertViewHas('tasks', function ($tasks) use ($user) {
            return $tasks->every(fn($task) => $task->completed !== null && $task->user_id === $user->id);
        });

        // Assert that only pending tasks are included in the response (completed field = NULL)
        $pending_tasks->assertViewHas('tasks', function ($tasks) use ($user) {
            return $tasks->every(fn($task) => $task->completed == null && $task->user_id === $user->id);
        });

        // Assert the task count is correct
        $completed_tasks->assertViewHas('tasks', function ($tasks) {
            global $completed_no;
            return $tasks->count() === $completed_no;
        });

        // Assert the task count is correct
        $pending_tasks->assertViewHas('tasks', function ($tasks) {
            global $pending_no;
            return $tasks->count() === $pending_no;
        });
    }


    /**
     * @return void
     * Function to test authenticated user creating a task
     */
    public function can_authenticated_user_create_a_task(): void
    {
        $user = User::factory()->create();        // Simulate the user being authenticated
        $this->actingAs($user);

        $test_task_data = [                       // Test data
            'title'         => 'Test Task',
            'description'   => 'This is a test task description',
            'due_date'      => '2025-03-19',
        ];

        $response = $this->post(route('tasks.store'), $test_task_data);         //post the data to this route
        $response->assertRedirect(route('tasks.index'));                        //after this action, app should redirect to main listing page
        $response->assertSessionHas('success', 'Task created successfully!');
        $this->assertDatabaseHas('tasks', [                                      //Check if the DB saved the test task data correctly
            'title'         => $test_task_data['title'],
            'description'   => $test_task_data['description'],
            'due_date'      => $test_task_data['due_date'],
            'user_id'       => $user->id,
        ]);
    }


    /**
     * @return void
     * Function to test unauthenticated user (guest) creating a task
     */
    public function can_guest_user_create_a_task(): void
    {
        $test_task_data = [                                                           // Test data
            'title'         => 'Test Task',
            'description'   => 'This is a test task description',
            'due_date'      => '2025-03-19',
        ];

        $response = $this->post(route('tasks.store'), $test_task_data);         //post the data to this route
        $response->assertRedirect(route('tasks.index'));                        //after this action, app should redirect to main listing page
        $response->assertStatus(403);
        $this->assertDatabaseMissing('tasks', [                                 //checking that the data did not save in the DB
            'title' => $test_task_data['title'],
        ]);
    }


    /**
     * @return void
     * Function to check if unauthenticated users can access create tasks page
     */
    public function can_guest_access_create_tasks_page(): void
    {
        $response = $this->get(route('tasks.create'));                      // Trying to access without authentication
        $response->assertRedirect(route('login'));                          // Force redirect to login
    }

    //=============================END OF CREATE TESTS========================================//

    //=============================START OF EDIT TESTS========================================//

    /**
     * @return void
     * Function to check the following:
     * 1. if authenticated user can access edit page of their own task (Yes, 200 success)
     * 2. if authenticated user can access someone else's task (No, 403 error)
     * 3. if unauthenticated user can access any task (No, 403 error)
     */
    public function can_user_access_edit_page(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $task = Task::factory()->create([ 'user_id' => $user1->id ]);               // Create a user and a task owned by the user

        //checking if user can access their own task edit page
        $response = $this->actingAs($user1)->get(route('tasks.edit', $task->id));
        $response->assertStatus(200);
        $response->assertViewIs('tasks.edit');
        $response->assertViewHas('task', $task);

        //checking if user can access someone else's task
        $response = $this->actingAs($user2)->get(route('tasks.edit', $task->id));
        $response->assertStatus(403);

        //checking if guest user can access any tasks
        $response = $this->get(route('tasks.edit', $task->id));
        $response->assertStatus(403);
        $response->assertRedirect(route('login'));
    }

    //=============================START OF EDIT TESTS========================================//

    //=============================START OF UPDATE TESTS========================================//

    /**
     * @return void
     * Function to check if an authenticated user can update their own task via form submission on web app
     */
    public function can_authenticated_user_update_their_own_task(): void
    {
        $user = User::factory()->create();                              //Create test user
        $task = Task::factory()->create(['user_id' => $user->id]);      //Create test task associated with that user

        $test_update_data = [                                           //test update data
            'title'       => 'Updated Task Title',
            'description' => 'Updated Task Description',
            'due_date'    => '2025-03-20',
        ];

        //testing if editing works
        $response = $this->actingAs($user)->patch(route('tasks.update', $task->id), $test_update_data);
        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('success', 'Task updated successfully!');
        $this->assertDatabaseHas('tasks', array_merge(['id' => $task->id], $test_update_data));
    }


    /**
     * @return void
     * Function to check if authenticated user can edit someone else's task
     */
    public function can_authenticated_user_update_someone_elses_task(): void
    {
        $user1 = User::factory()->create();                             //associated task owner
        $user2 = User::factory()->create();                             //unassociated task owner
        $task = Task::factory()->create(['user_id' => $user1->id]);

        $test_update_data = [                                           //test update data
            'title'       => 'Updated Task Title',
            'description' => 'Updated Task Description',
            'due_date'    => '2025-03-20',
        ];

        //checking if its possible to update someone else's task - it should return 403 error
        $response = $this->actingAs($user2)->patch(route('tasks.update', $task->id), $test_update_data);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('tasks', array_merge(['id' => $task->id], $test_update_data));
    }


    /**
     * @return void
     * Function to check if unauthenticated (guest) user can edit a task
     */
    public function can_guest_user_update_task(): void
    {
        $user = User::factory()->create();                             //associated task owner
        $task = Task::factory()->create(['user_id' => $user->id]);

        $test_update_data = [                                           //test update data
            'title'       => 'Updated Task Title',
            'description' => 'Updated Task Description',
            'due_date'    => '2025-03-20',
        ];

        //checking if its possible to update someone else's task - it should return 403 error
        $response = $this->patch(route('tasks.update', $task->id), $test_update_data);
        $response->assertStatus(403);
        $response->assertRedirect(route('login'));
    }


    /**
     * @return void
     * Function to validate the updation of task data (form validation)
     */
    public function validate_update_data(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $invalidData = [
            'title'       => '',                                // Empty title
            'description' => 'This description is valid.',
            'due_date'    => 'invalid-date',                    // Invalid date
        ];

        $response = $this->actingAs($user)->patch(route('tasks.update', $task->id), $invalidData);
        $response->assertSessionHasErrors(['title', 'due_date']);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id, 'title' => '', 'due_date' => 'invalid-date']);
    }

    //=============================END OF UPDATE TESTS========================================//

    //=============================START OF DELETE TESTS========================================//

    /**
     * @return void
     * Function to test if authenticated user can delete their own task
     */
    public function test_user_can_delete_their_own_task(): void
    {
        $user       = User::factory()->create();                                                //create a user
        $task       = Task::factory()->create(['user_id' => $user->id]);                        //create a task associated with that user
        $response   = $this->actingAs($user)->delete(route('tasks.destroy', $task->id));
        $response->assertSessionHas('success', 'Task deleted successfully!');                   //successful deletion
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);                       //verification of deletion
        $response->assertRedirect(route('tasks.index'));                                  //successful redirection
    }


    /**
     * @return void
     * Function to test if authenticated user can delete someone else's task
     */
    public function can_authenticated_user_delete_someone_elses_task(): void
    {
        $user1      = User::factory()->create();
        $user2      = User::factory()->create();
        $task       = Task::factory()->create(['user_id' => $user1->id]);
        $response   = $this->actingAs($user2)->delete(route('tasks.destroy', $task->id));
        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);                         //Deletion should have been unsuccessful and task should still be there
    }


    /**
     * @return void
     * Unauthenticated user cannot delete a task or have access at all
     */
    public function test_guest_user_cannot_delete_a_task(): void
    {
        $user       = User::factory()->create();
        $task       = Task::factory()->create(['user_id' => $user->id]);      //task used for testing
        $response   = $this->delete(route('tasks.destroy', $task->id));   //guest trying to delete the legit task
        $response->assertStatus(403);                                   //error status
        $response->assertRedirect(route('login'));                      // Assuming guest users are redirected to login
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);         // Task should still exist
    }

    //=============================END OF DELETE TESTS========================================//

}
