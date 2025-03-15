# Laravel Task Manager

This is a simple task management application built with Laravel, designed to help users organize and track their tasks efficiently.

## Features

* **Task Creation:** Users can create new tasks with titles, descriptions, and due dates.
* **Task Listing:** Displays a list of tasks with filtering options (completed, pending) and search functionality.
* **Task Editing:** Users can edit existing tasks, including marking them as completed.
* **Task Deletion:** Users can delete tasks.
* **User Authentication:** Users can register and log in to manage their own tasks.
* **User-Specific Tasks:** Tasks are associated with individual users, ensuring data privacy.
* **API Functionality:** Provides an API for task management, secured with Laravel Sanctum for token-based authentication.
* **Search and Filtering:** Tasks can be searched and filtered.
* **Pagination:** Tasks are paginated to improve performance.

## Installation

1.  **Clone the repository:**

    ```bash
    git clone [https://github.com/TheLadyOfShalott13/laravel-task-manager.git](https://www.google.com/search?q=https://github.com/TheLadyOfShalott13/laravel-task-manager.git)
    cd laravel-task-manager
    ```

2.  **Install Composer dependencies:**

    ```bash
    composer install
    ```

3.  **Copy the `.env.example` file to `.env` and configure your database settings:**

    ```bash
    cp .env.example .env
    ```

    * Update the `.env` file with your database credentials.

4.  **Generate an application key:**

    ```bash
    php artisan key:generate
    ```

5.  **Run database migrations:**

    ```bash
    php artisan migrate
    ```

6.  **Install Laravel Sanctum:**

    ```bash
    composer require laravel/sanctum
    php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
    php artisan migrate
    ```

7.  **Start the development server:**

    ```bash
    php artisan serve
    ```

8.  **Access the application in your browser:**

    * Open `http://localhost:8000` in your browser.

## API Usage

1.  **Generate an API token:**
    * Register or log in as a user.
    * Use the `/login` API endpoint to generate a Sanctum token.

2.  **Include the token in your API requests:**
    * Set the `Authorization` header to `Bearer your_generated_token`.

3.  **API Routes:**
    * `GET /api/tasks`: List tasks (with search and filter options).
    * `POST /api/tasks`: Create a new task.
    * `GET /api/tasks/{task}`: Show a specific task.
    * `PUT/PATCH /api/tasks/{task}`: Update a task.
    * `DELETE /api/tasks/{task}`: Delete a task.

## Technologies Used

* Laravel 12
* PHP 8.2+
* MySQL
* Bootstrap 5
* Laravel Sanctum
* Laravel Auth UI (for login + register)
* Docker compose (for emulating server)
* Nginx

## Contributing

Contributions are welcome! Please feel free to submit a pull request or open an issue.

## License

This project is open-source and available under the [MIT license](LICENSE).