# Company Management System App

[Watch the demo video](https://github.com/Pejoal/Project-Manager/raw/refs/heads/main/public/docs/videos/demo.mp4)

## Description

The Company Management System App is a comprehensive solution designed to streamline and manage various aspects of a company's operations. This application provides tools for managing clients, projects, and tasks efficiently, ensuring smooth workflow and enhanced productivity.

---

## Features

- **Client Management**

  - View, add, edit, and delete clients
  - View client details

- **Project Management**

  - View, create, edit, and delete projects
  - View project details

- **Task Management**

  - View, add, edit, and delete tasks within a project
  - View task details
  - Update task status
  - Update task priority

- **Localization**

  - Multi-language support
  - Locale-based URL generation
  - Session-based locale management

- **User Authentication**

  - Secure login and registration
  - Email verification
  - Session management
  - Two-Factor Authentication

- **Dashboard**

  - Overview of key metrics and information
  - Access to translations and localized content
  - Analytics and Reports: Display key metrics and reports such as employee performance, project progress, and financial summaries
  - Customizable Widgets: Allow users to customize their dashboard with widgets that display relevant information

- **Settings Management**

  - Update application settings
  - Manage user preferences

- **Notifications**

  - Real-time notifications
  - Email notifications

- **Role-Based Access Control**

  - Define roles and permissions
  - Restrict access to certain features based on user roles

- **API Integration**

  - RESTful API endpoints
  - API authentication and authorization

- **Responsive Design**

  - Mobile-friendly interface
  - Adaptive layout for different screen sizes

- **Team Management**

  - Create and manage teams
  - Invite users to teams
  - Assign roles within teams

- **Profile Management**

  - Update profile information
  - Upload profile photos

- **Security Features**

  - Password reset functionality
  - Password confirmation for sensitive actions
  - CSRF protection

- **Phase Management**

  - View, add, edit, and delete phases within a project
  - View phase details

- **Milestone Management**

  - View, add, edit, and delete milestones within a project
  - View milestone details

- **Project Status Management**

  - View, add, edit, and delete project statuses

- **Project Priority Management**

  - View, add, edit, and delete project priorities

- **Task Status Management**

  - View, add, edit, and delete task statuses

- **Task Priority Management**

  - View, add, edit, and delete task priorities

---

## Authors

- [@Pejoal](https://www.github.com/Pejoal)

---

## Installation

Clone the repository:

```shell
git clone git@github.com:Pejoal/manager.git
```

Navigate to the project directory:

```shell
cd manager
```

Copy the example environment file and configure the environment variables:

```shell
cp .env.example .env
```

---

### Back-End

Install Laravel Sail as a development dependency:

```shell
composer require laravel/sail --dev --ignore-platform-reqs
```

Create a Sail alias:

```shell
alias sail="./vendor/bin/sail"
```

Start the Sail environment:

```shell
sail up -d
```

Install PHP dependencies:

```shell
sail composer install --ignore-platform-reqs
```

Create a symbolic link for storage:

```shell
sail artisan storage:link
```

Generate the application key:

```shell
sail artisan key:generate
```

Run database migrations and seed the database:

```shell
sail artisan migrate:fresh --seed
```

Start Reverb:

```shell
sail artisan reverb:start
```

### Front-End

Install Node.js dependencies:

```shell
sail npm install
```

Compile the front-end assets:

```shell
sail npm run dev
```

```shell
sail npm run build
```

### Database

- Username: pejoal
- Password: pejoal

### Prerequisites

- Composer ( V 2 )
- Node js ( V 19.1.0 )
- Docker ( Compose )

---

### Technologies Used

- **Backend**: Laravel
- **Frontend**: Inertia.js, Vue.js
- **Localization**: Mcamara Laravel Localization
- **Authentication**: Laravel Sanctum, Jetstream

---

## For Support..

- [My Email](pejoal.official@gmail.com)
