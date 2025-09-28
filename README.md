# 🏢 Company Management System App

[🎥 Watch the Demo Video](https://github.com/user-attachments/assets/785440b8-69a3-4e4d-85de-5fb0ebd78125)

## About The Project

The Company Management System is a comprehensive solution designed to streamline and manage various aspects of a company's operations. This application provides tools for managing clients, projects, and tasks efficiently, ensuring a smooth workflow and enhanced productivity.

---

## ✨ Features

- **Client, Project & Task Management**: Full CRUD operations for clients, projects, tasks, phases, and milestones.
- **Full-Text Search**: Fast, typo-tolerant search powered by **Meilisearch**.
- **Real-time Notifications**: Instant updates using **Laravel Reverb**.
- **User Authentication**: Secure login, registration, email verification, and Two-Factor Authentication via Laravel Jetstream.
- **Team Management**: Create teams, invite users, and assign roles.
- **Role-Based Access Control**: Granular permissions to restrict access based on user roles.
- **Customizable Dashboard**: An overview of key metrics with customizable widgets.
- **Localization**: Multi-language support with session-based locale management.
- **Responsive Design**: A mobile-friendly interface that adapts to different screen sizes.
- **API Integration**: RESTful API endpoints with authentication via Laravel Sanctum.
- **Profile & Settings Management**: Users can update their profile, preferences, and application settings.

---

## 🛠️ Tech Stack

- **Backend**: Laravel, Meilisearch
- **Frontend**: Inertia.js, Vue.js
- **Real-time**: Laravel Reverb
- **Authentication**: Laravel Sanctum, Jetstream
- **Localization**: Mcamara Laravel Localization

---

## 🚀 Getting Started

Follow these instructions to get a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

- Composer v2+
- Node.js v19.1.0+
- Docker & Docker Compose

### Installation

1.  **Clone the repository:**
    ```shell
    git clone git@github.com:Pejoal/manager.git
    cd manager
    ```
2.  **Set up the environment:**
    ```shell
    cp .env.example .env
    ```
    > Make sure to configure your database and application variables in the `.env` file.
3.  **Install Laravel Sail:**
    ```shell
    composer require laravel/sail --dev --ignore-platform-reqs
    ```
4.  **Create a shell alias for convenience:**
    ```shell
    alias sail="./vendor/bin/sail"
    ```
5.  **Start the Docker containers:**
    This command starts the web server, database, and search engine in the background.
    ```shell
    sail up -d
    ```
6.  **Install backend dependencies:**
    ```shell
    sail composer install --ignore-platform-reqs
    ```
7.  **Run initial setup commands:**
    ```shell
    sail artisan key:generate
    sail artisan migrate:fresh --seed
    sail artisan storage:link
    ```
8.  **Sync search index settings:**
    This command is crucial for configuring your Meilisearch indexes.
    ```shell
    sail artisan scout:sync-index-settings
    ```
9.  **Install frontend dependencies:**
    ```shell
    sail npm install
    ```
10. **Compile assets and start services:**
    Open two separate terminal tabs for these commands.
    - In the first tab, compile frontend assets and watch for changes:
      ```shell
      sail npm run dev
      ```
    - In the second tab, start the Reverb WebSocket server:
      ```shell
      sail artisan reverb:start
      ```

---

## Notes

after adding new / removing locales to `config/laravellocalization.php`, run:

```shell
sail artisan cache:clear
ail artisan config:clear
```

## 👤 Author

- **Pejoal** - [GitHub](https://www.github.com/Pejoal)

---

## 🤝 Support

For support, please email me at [pejoal.official@gmail.com](mailto:pejoal.official@gmail.com).
