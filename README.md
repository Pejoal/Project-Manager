# Company Management System App

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
