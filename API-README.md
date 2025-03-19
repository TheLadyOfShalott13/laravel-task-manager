# API Documentation - `ApiTaskController`

This controller handles API endpoints for user authentication and task management, including creating, updating, retrieving, and deleting tasks.

---

## Endpoints

### 1. **Generate API Token**
**Endpoint**: `/generate-token`  
**Method**: `POST`  
**Description**: Authenticates a user and generates an API token for authorized actions.

#### Request Parameters:
| Parameter   | Type     | Required | Description                  |
|-------------|----------|----------|------------------------------|
| `email`     | `string` | Yes      | User's email address.        |
| `password`  | `string` | Yes      | User's account password.     |

#### Responses:
- **200**: Token generated successfully.
  ```json
  {
    "message": "Login successful",
    "token": "your_api_token_here"
  }
  ```
- **401**: Invalid credentials.
  ```json
  {
    "message": "Invalid credentials"
  }
  ```

---

### 2. **Fetch All Tasks**
**Endpoint**: `/tasks`  
**Method**: `GET`  
**Description**: Retrieves all tasks associated with the authenticated user.

#### Responses:
- **200**: Returns an array of tasks.
  ```json
  {
    "tasks": [
      { "id": 1, "title": "Task 1", "description": "...", "due_date": "...", "completed": false },
      { "id": 2, "title": "Task 2", "description": "...", "due_date": "...", "completed": true }
    ]
  }
  ```
- **404**: User not found or no tasks available.
  ```json
  {
    "error": "User not found"
  }
  ```

---

### 3. **Create a Task**
**Endpoint**: `/tasks`  
**Method**: `POST`  
**Description**: Creates a new task for the authenticated user.

#### Request Parameters:
| Parameter     | Type     | Required | Description                  |
|---------------|----------|----------|------------------------------|
| `title`       | `string` | Yes      | Task title (max: 150 chars). |
| `description` | `string` | No       | Task description (max: 255).|
| `due_date`    | `date`   | Yes      | Due date for the task.       |

#### Responses:
- **201**: Task created successfully.
  ```json
  {
    "message": "Task created successfully!",
    "task": { "id": 1, "title": "...", "description": "...", "due_date": "...", "completed": false }
  }
  ```
- **400**: Validation error or creation failure.
  ```json
  {
    "message": "Error while creating task!"
  }
  ```

---

### 4. **Retrieve a Single Task**
**Endpoint**: `/tasks/{id}`  
**Method**: `GET`  
**Description**: Fetches details of a specific task.

#### Responses:
- **200**: Returns task details.
  ```json
  {
    "single_task": { "id": 1, "title": "...", "description": "...", "due_date": "...", "completed": false }
  }
  ```
- **403**: Unauthorized access.
  ```json
  {
    "message": "You are not authorized to view this task"
  }
  ```

---

### 5. **Update a Task**
**Endpoint**: `/tasks/{id}`  
**Method**: `PUT`  
**Description**: Updates an existing task.

#### Request Parameters:
| Parameter     | Type     | Required | Description                  |
|---------------|----------|----------|------------------------------|
| `title`       | `string` | Yes      | Updated task title.          |
| `description` | `string` | No       | Updated description.         |
| `due_date`    | `date`   | Yes      | Updated due date.            |

#### Responses:
- **200**: Task updated successfully.
  ```json
  {
    "message": "Task updated successfully"
  }
  ```
- **403**: Unauthorized access.
  ```json
  {
    "message": "You are not authorized to update this task"
  }
  ```

---

### 6. **Update Task Status**
**Endpoint**: `/tasks/update-status/{id}`  
**Method**: `PUT`  
**Description**: Updates the completion status of a task.

#### Request Parameters:
| Parameter   | Type      | Required | Description                 |
|-------------|-----------|----------|-----------------------------|
| `completed` | `boolean` | Yes      | New completion status.      |

#### Responses:
- **200**: Task status updated successfully.
  ```json
  {
    "message": "Task updated successfully"
  }
  ```
- **403**: Unauthorized access.
  ```json
  {
    "message": "You are not authorized to update this task"
  }
  ```

---

### 7. **Delete a Task**
**Endpoint**: `/tasks/{id}`  
**Method**: `DELETE`  
**Description**: Deletes a specific task.

#### Responses:
- **200**: Task deleted successfully.
  ```json
  {
    "message": "Task deleted successfully!"
  }
  ```
- **403**: Unauthorized access.
  ```json
  {
    "message": "You are not authorized to delete this task."
  }
  ```

---