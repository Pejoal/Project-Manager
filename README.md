# Company Management System App

## Description

The Company Management System App is a comprehensive solution designed to streamline and manage various aspects of a company's operations. This application provides tools for managing clients, projects, and tasks efficiently, ensuring smooth workflow and enhanced productivity.

---

## Features

- **Client Management**

  - View all clients
  - Add new clients
  - View client details
  - Edit client information
  - Delete clients

- **Project Management**

  - View all projects
  - Create new projects
  - View project details
  - Edit project information
  - Delete projects

- **Task Management**

  - View all tasks within a project
  - Add new tasks to a project
  - View task details
  - Edit task information
  - Delete tasks

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

## Technologies Used

- **Backend**: Laravel
- **Frontend**: Inertia.js, Vue.js
- **Localization**: Mcamara Laravel Localization
- **Authentication**: Laravel Sanctum, Jetstream

---

## Authors

- [@Pejoal](https://www.github.com/Pejoal)

---

## Installation

```shell
git clone git@github.com:Pejoal/manager.git
```

```shell
cd manager
```

```shell
cp .env.example .env
```

---

### Back-End

```shell
composer require laravel/sail --dev --ignore-platform-reqs
```

```shell
alias sail="./vendor/bin/sail"
```

```shell
sail up -d
```

```shell
sail composer install --ignore-platform-reqs
```

```shell
sail artisan storage:link
```

```shell
sail artisan key:generate
```

```shell
sail artisan migrate:fresh --seed
```

### Front-End

```shell
sail npm install
```

```shell
sail npm run dev
```

### Database

- Username: pejoal
- Password: pejoal

### Prerequisites

- Composer ( V 2 )
- Node js ( V 19.1.0 )
- Docker ( Compose )

### Stack

- PHP ( 8.2 )
- Laravel 11 ( Sail )
- MySQL ( MariaDB )
- Inertia js
- Vue 3 ( Composition API )
- Tailwind CSS

---

## For Support..

- [My Email](pejoal.official@gmail.com)
