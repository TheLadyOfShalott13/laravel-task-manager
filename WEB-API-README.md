# WebTaskControllerTest Documentation

This document describes the test suite for the `WebTaskController` in a Laravel application. These tests ensure the web-based task management functionalities work correctly.

## Overview

The `WebTaskControllerTest` class employs PHPUnit and Laravel's testing utilities to verify the behavior of the web-based task management features. It covers task listing, creation, editing, updating, and deletion, including authentication and authorization checks.

## Setup

* **Namespace:** `Tests\Feature`
* **Dependencies:**
    * `App\Models\Task`
    * `App\Models\User`
    * `Illuminate\Foundation\Testing\RefreshDatabase`
    * `Illuminate\Foundation\Testing\WithFaker`
    * `Tests\TestCase`
* **Traits:** `RefreshDatabase` (ensures a clean database for each test)
* **Purpose:** Tests the web task controller, checking authentication, authorization, and data integrity.

## Test Groups

The tests are organized into logical groups:

1.  **Index Tests:** Tests for the task listing page.
2.  **Create Tests:** Tests for the task creation functionality.
3.  **Edit Tests:** Tests for the task editing page.
4.  **Update Tests:** Tests for the task update functionality.
5.  **Delete Tests:** Tests for the task deletion functionality.

## Test Details

### 1. Index Tests

* **`can_authenticated_user_access_their_full_tasks_list()`:**
    * Tests if authenticated users can access their own tasks and if the correct tasks are returned.
    * Creates two users and assigns different tasks to each.
    * Simulates authentication and checks if each user receives only their tasks.
    * Asserts status 200 and verifies the task count and ownership.
* **`can_guest_access_tasks_page()`:**
    * Tests if guest users are redirected to the login page when trying to access the task list.
    * Sends a GET request without authentication.
    * Asserts a redirect to the login route.

### 2. Create Tests

* **`can_authenticated_user_access_create_task_page()`:**
    * Tests if authenticated users can access the task creation page.
    * Creates a user and simulates authentication.
    * Sends a GET request to the create route.
    * Asserts status 200 and verifies the correct view is returned.
* **`can_authenticated_user_get_completed_and_pending_tasks()`:**
    * Tests that a user can get their completed and pending tasks.
    * Creates a user, and then creates completed and pending tasks for that user.
    * Asserts that the completed tasks route only returns completed tasks, and that the pending tasks route only returns pending tasks.
* **`can_authenticated_user_create_a_task()`:**
    * Tests if authenticated users can create a new task.
    * Creates a user and simulates authentication.
    * Sends a POST request with task data to the store route.
    * Asserts a redirect to the task list, a success session message, and verifies the task is created in the database.
* **`can_guest_user_create_a_task()`:**
    * Tests if guest users are blocked from creating a new task.
    * Sends a POST request with task data to the store route without authentication.
    * Asserts a redirect to the task list, and a 403 error.
    * Asserts that the database does not contain the new task.
* **`can_guest_access_create_tasks_page()`:**
    * Tests if guest users are redirected to the login page when trying to access the task creation page.
    * Sends a GET request to the create route without authentication.
    * Asserts a redirect to the login route.

### 3. Edit Tests

* **`can_user_access_edit_page()`:**
    * Tests if authenticated users can access the edit page for their own tasks and if unauthorized users are blocked.
    * Creates two users and a task owned by the first user.
    * Simulates authentication and checks access for the owner, another user, and a guest.
    * Asserts status 200 for the owner and 403 for others.

### 4. Update Tests

* **`can_authenticated_user_update_their_own_task()`:**
    * Tests if authenticated users can update their own tasks.
    * Creates a user and a task owned by that user.
    * Sends a PATCH request with updated task data to the update route.
    * Asserts a redirect to the task list, a success session message, and verifies the task is updated in the database.
* **`can_authenticated_user_update_someone_elses_task()`:**
    * Tests if authenticated users are blocked from updating tasks owned by others.
    * Creates two users and a task owned by the first user.
    * Simulates authentication for the second user and sends a PATCH request.
    * Asserts a 403 error and verifies the task is not updated.
* **`can_guest_user_update_task()`:**
    * Tests if guest users are blocked from updating tasks.
    * Creates a user and a task owned by that user.
    * Sends a PATCH request without authentication.
    * Asserts a 403 error and a redirect to the login page.
* **`validate_update_data()`:**
    * Tests if task update data is validated correctly.
    * Creates a user and a task owned by that user.
    * Sends a PATCH request with invalid data.
    * Asserts session errors for invalid fields and verifies the task is not updated in the database.

### 5. Delete Tests

* **`test_user_can_delete_their_own_task()`:**
    * Tests if authenticated users can delete their own tasks.
    * Creates a user and a task owned by that user.
    * Sends a DELETE request to the destroy route.
    * Asserts a success session message, verifies the task is deleted from the database, and asserts a redirect to the task list.
* **`can_authenticated_user_delete_someone_elses_task()`:**
    * Tests if authenticated users are blocked from deleting tasks owned by others.
    * Creates two users and a task owned by the first user.
    * Simulates authentication for the second user and sends a DELETE request.
    * Asserts a 403 error and verifies the task still exists.
* **`test_guest_user_cannot_delete_a_task()`:**
    * Tests if guest users are blocked from deleting tasks.
    * Creates a user and a task owned by that user.
    * Sends a DELETE request without authentication.
    * Asserts a 403 error, a redirect to the login page, and verifies the task still exists.