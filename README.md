# TodoApp API

## Overview
TodoApp API is a simple task management system with modern authentication and role-based access control. It allows users to create boards, add tasks, manage their status, and perform actions like duplicating, archiving, and more. Admins have additional control over users and can manage the system from a dashboard.

## Features

### User Functionalities:
- **Authentication System:** Secure login, registration, and session management.
- **Board Management:**
  - Create a board.
  - Modify the name or status of a board.
  - Move a board to trash or restore it from trash.
  - Duplicate a board (with or without tasks).
- **Task Management:**
  - Create, delete, and update tasks in a board.
  - Change the status of tasks (drag-and-drop functionality on the frontend like Notion).
  - Edit task content.
- **Task Status Management:** Users can drag and drop tasks to change their status.

### Admin Functionalities:
- **Dashboard Overview:** Admins can view all registered users.
- **User Management:**
  - Add new users with a default password.
  - Delete users from the system.
  - When a new user logs in for the first time, they must update their password.
- **Default Admin:** The first admin is created during the initial setup (seeding).

## Entities

### Users
- **Fields:**
  - `user_lastname`: Last name of the user.
  - `user_firstname`: First name of the user.
  - `user_email`: Email address of the user (used for login).
  - `password`: Hashed password.
  - `user_profile`: URL or path to the user's profile picture.
  - `user_role`: Role of the user (e.g., `user`, `admin`).
  - `created_at`: Timestamp of user creation.
  - `updated_at`: Timestamp of the last user update.
  - `deleted_at`: Timestamp of user deletion (if soft-deleted).

### Boards
- **Fields:**
  - `board_name`: Name of the board.
  - `board_status`: Status of the board (e.g., `active`, `archived`).
  - `created_at`: Timestamp of board creation.
  - `deleted_at`: Timestamp of board deletion (if moved to trash).
  - `updated_at`: Timestamp of the last board update.
  
- **Relationships:**
  - A user can have zero or many boards.
  - Each board belongs to one user.

### Tasks
- **Fields:**
  - `task_content`: Content or description of the task.
  - `status`: Current status of the task (e.g., `pending`, `in-progress`, `completed`).
  - `created_at`: Timestamp of task creation.
  - `updated_at`: Timestamp of the last task update.
  - `deleted_at`: Timestamp of task deletion (if moved to trash).

- **Relationships:**
  - A board can have zero or more tasks.
  - Each task belongs to one board.

## API Endpoints

### Authentication
- `POST /api/auth/login` - Login user
- `POST /api/auth/register` - Register new user
- `POST /api/auth/logout` - Logout user

### User Management
- `GET /api/users` - Get all users (Admin only)
- `POST /api/users` - Add a new user (Admin only)
- `DELETE /api/users/:id` - Delete a user (Admin only)
- `PATCH /api/users/:id` - Update user info (Admin only)

### Board Management
- `GET /api/boards` - Get all boards for the authenticated user
- `POST /api/boards` - Create a new board
- `PATCH /api/boards/:id` - Update board details (name, status)
- `DELETE /api/boards/:id` - Move board to trash
- `POST /api/boards/:id/duplicate` - Duplicate a board (with or without tasks)
  
### Task Management
- `GET /api/boards/:id/tasks` - Get all tasks for a board
- `POST /api/boards/:id/tasks` - Create a new task in a board
- `PATCH /api/boards/:id/tasks/:taskId` - Update task details (content, status)
- `DELETE /api/boards/:id/tasks/:taskId` - Delete a task

## Setup and Installation

### Prerequisites
- Python 3.x
- Flask
- Flask extensions (e.g., `Flask-JWT-Extended` for authentication, `SQLAlchemy` for ORM)
- Postman for API testing

### Installation Steps
1. Clone the repository:
    ```bash
    git clone https://github.com/your-username/todoapp-api.git
    ```
2. Navigate to the project directory:
    ```bash
    cd todoapp-api
    ```
3. Install dependencies:
    ```bash
    pip install -r requirements.txt
    ```
4. Set up environment variables for database configuration and JWT secret key.

5. Run the Flask app:
    ```bash
    flask run
    ```

### Seeding the Database
The system creates a default admin during the initial setup. To seed the database:
```bash
flask db seed
