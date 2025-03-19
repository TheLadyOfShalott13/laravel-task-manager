# ApiTaskControllerTest Documentation

This document describes the test suite for the `ApiTaskController` in a Laravel application. These tests ensure the API endpoints for task management function correctly.

## Overview

The `ApiTaskControllerTest` class uses PHPUnit and Laravel's testing utilities to verify the behavior of the API endpoints. It covers token generation, task creation, retrieval, updating, and deletion.

## Setup

* **Namespace:** `Tests\Feature`
* **Dependencies:**
    * `App\Models\Task`
    * `App\Models\User`
    * `Illuminate\Foundation\Testing\RefreshDatabase`
    * `Tests\TestCase`
* **Traits:** `RefreshDatabase` (ensures a clean database for each test)
* **Properties:**
    * `$user`: Stores a User model instance.

## Test Groups

The tests are organized into logical groups:

1.  **Generate Token Tests:** Tests for the token generation endpoint.
2.  **Index Tests:** Tests for fetching lists of tasks.
3.  **Task Creation Tests:** Tests for creating new tasks.
4.  **Task Fetching Single Row Tests:** Tests for retrieving a single task.
5.  **Task Update Single Row Tests:** Tests for updating a single task.
6.  **Delete Task Tests:** Tests for deleting tasks.

## Test Details

### 1. Generate Token Tests

* **`test_successful_token_generation()`:**
    * Tests successful token generation for valid user credentials.
    * Creates a test user.
    * Sends a POST request to `route('generate_token')` with valid credentials.
    * Asserts a 200 status code, JSON structure, and success message.
* **`test_failed_login_due_to_invalid_credentials()`:**
    * Tests failed token generation for invalid password.
    * Creates a test user.
    * Sends a POST request to `route('generate_token')` with an incorrect password.
    * Asserts a 401 status code and an error message.
* **`test_failed_token_gen_missing_fields()`:**
    * Tests failed token generation when the request is missing fields.
    * Sends a POST request to `route('generate_token')` with missing password field.
    * Asserts a 422 status code, and validation errors for the missing fields.

### 2. Index Tests

* **`test_successful_user_fetch_tasks()`:**
    * Tests successful retrieval of tasks.
    * Sends a GET request to `route('tasks.index')` with a valid token.
    * Asserts a 200 status code and a JSON structure containing tasks.
* **`test_successful_user_fetch_empty_tasks()`:**
    * Tests retrieval of an empty task list.
    * Sends a GET request to `route('tasks.index')` with a valid token.
    * Asserts a 200 status code, a JSON structure, and an empty task list.
* **`test_unauthenticated_user_access()`:**
    * Tests that an unauthenticated user gets an empty list of tasks.
    * Sends a GET request to `route('tasks.index')` with a valid token.
    * Asserts a 200 status code, and an empty tasks array.
* **`test_nonexistent_user_returns_error()`:**
    * Tests that an invalid token returns a 404 error.
    * Sends a GET request to `route('tasks.index')` with an invalid token.
    * Asserts a 404 status code, and a message.

### 3. Task Creation Tests

* **`test_successful_user_task_creation()`:**
    * Tests successful task creation.
    * Sends a POST request to `route('tasks.store')` with valid task data and a valid token.
    * Asserts a 201 status code and verifies the task is created in the database.
* **`test_task_creation_validation_errors()`:**
    * Tests task creation with invalid data.
    * Sends a POST request to `route('tasks.store')` with invalid data and a valid token.
    * Asserts a 422 status code and validation errors.
* **`test_task_creation_exception_errors()`:**
    * Tests how the api handles exceptions thrown during task creation.
    * Mocks the Task model to throw an exception during task creation.
    * Asserts a 400 status code, and an error message.
* **`test_unauthenticated_user_task_creation()`:**
    * Tests task creation without a valid token.
    * Sends a POST request to `route('tasks.store')` without a token.
    * Asserts a 401 status code.

### 4. Task Fetching Single Row Tests

* **`test_user_getting_one_task()`:**
    * Tests retrieval of a single task.
    * Sends a GET request to `route('tasks.show', ['task' => $test_task_id])` with a valid token.
    * Asserts a 200 status code and the correct task data.
* **`test_user_getting_one_task_of_another()`:**
    * Tests that a user can not retrieve a task that belongs to another user.
    * Sends a GET request to `route('tasks.show', ['task' => $test_task_id])` with a valid token.
    * Asserts a 403 status code.
* **`test_unauthenticated_user_fetch_single_task()`:**
    * Tests that an unauthenticated user can not retrieve a single task.
    * Sends a GET request to `route('tasks.show', ['task' => $test_task_id])` without a token.
    * Asserts a 401 status code.

### 5. Task Update Single Row Tests

* **`test_success_update_of_task_for_valid_user()`:**
    * Tests successful task update.
    * Sends a PUT request to `route('tasks.update', ['task' => $test_task_id])` with updated data and a valid token.
    * Asserts a 200 status code and verifies the task is updated in the database.
* **`test_validation_fails_with_invalid_data()`:**
    * Tests task update with invalid data.
    * Sends a PUT request to `route('tasks.update', ['task' => $test_task_id])` with invalid data and a valid token.
    * Asserts a 422 status code and validation errors.
* **`test_update_fails_when_user_is_not_authorized()`:**
    * Tests task update without a valid token.
    * Sends a PUT request to `route('tasks.update', ['tasks' => $test_task_id])` without a token.
    * Asserts a 404 status code.
* **`test_update_fails_when_task_does_not_exist()`:**
    * Tests task update with an invalid task ID.
    * Sends a PUT request to `route('tasks.update', ['task' => $test_task_id])` with an invalid task ID and a valid token.
    * Asserts a 404 status code.

### 6. Delete Task Tests

* **`test_successful_task_deletion_by_authorized_user()`:**
    * Tests successful task deletion.
    * Sends a DELETE request to `route('tasks.destroy', ['task' => $test_task_id])` with a valid token.
    * Asserts a 200 status code and verifies the task is deleted from the database.
* **`test_unauthorized_user_task_delete()`:**
    * Tests task deletion without a valid token.
    * Sends a DELETE request to `route('tasks.destroy', ['task' => $test_task_id])` without a token.
    * Asserts a 403 status code.
* **`test_task_delete_not_existing()`:**
    * Tests task deletion with an invalid task ID.
    * Sends a DELETE request to `route('tasks.destroy', ['task' => $test_task_id])` with an invalid task ID and a valid token.
    * Asserts a 404 status code.