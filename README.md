# 🏢 Company Management System with Payroll

[🎥 Watch the Demo Video](https://github.com/user-attachments/assets/785440b8-69a3-4e4d-85de-5fb0ebd78125)

## About The Project

The Company Management System is a comprehensive solution designed to streamline and manage various aspects of a company's operations. This application provides tools for managing clients, projects, tasks, and now includes a complete **payroll management system** with time tracking, salary calculations, and tax management.

---

## ✨ Features

### Core Management Features

- **Client, Project & Task Management**: Full CRUD operations for clients, projects, tasks, phases, and milestones.
- **Full-Text Search**: Fast, typo-tolerant search powered by **Meilisearch**.
- **Real-time Notifications**: Instant updates using **Laravel Reverb**.
- **User Authentication**: Secure login, registration, email verification, and Two-Factor Authentication via Laravel Jetstream.
- **Team Management**: Create teams, invite users, and assign roles.
- **Role-Based Access Control**: Granular permissions using **Spatie Laravel Permission**.
- **Customizable Dashboard**: An overview of key metrics with customizable widgets.
- **Localization**: Multi-language support with session-based locale management.
- **Responsive Design**: A mobile-friendly interface that adapts to different screen sizes.
- **API Integration**: RESTful API endpoints with authentication via Laravel Sanctum.

### 💰 Payroll Management Features

- **Time Tracking**: Automatic time entry generation from task start/end datetime
- **Employee Profiles**: Comprehensive employee information with hourly rates and tax settings
- **Salary Calculations**: Automatic calculation with overtime, regular hours, and tax deductions
- **Tax Management**: Flexible tax configuration system supporting flat, percentage, and progressive taxes
- **Payslip Generation**: Automated payslip creation with detailed breakdowns
- **Admin-Only Access**: Secure payroll management restricted to administrators
- **Approval Workflows**: Time entry and payslip approval processes
- **Comprehensive Reports**: Daily, weekly, monthly payroll analytics and reports
- **Multiple Payment Methods**: Support for bank transfer, cash, and check payments
- **Overtime Calculations**: Automatic overtime detection and rate multiplication

#### Payroll System Components

1. **Time Entries**: Track worked hours based on task assignments
2. **Employee Profiles**: Manage hourly rates, tax exemptions, and payment methods
3. **Payroll Settings**: Configure company-wide payroll parameters
4. **Tax Configurations**: Set up federal, state, Social Security, Medicare, and other taxes
5. **Payslips**: Generate detailed pay statements with gross pay, deductions, and net pay
6. **Reports & Analytics**: Comprehensive payroll reporting and statistics

---

## 🛠️ Tech Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Frontend**: Vue.js 3, Inertia.js, Tailwind CSS
- **Database**: MySQL/PostgreSQL with comprehensive indexing
- **Search**: Meilisearch with Laravel Scout
- **Real-time**: Laravel Reverb for WebSocket connections
- **Authentication**: Laravel Sanctum, Jetstream with 2FA
- **Permissions**: Spatie Laravel Permission package
- **Localization**: Mcamara Laravel Localization
- **Development**: Laravel Sail (Docker-based development environment)

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
    git clone git@github.com/Pejoal/manager.git
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

8.  **Set up payroll system:**

    ```shell
    sail artisan db:seed --class=PayrollSeeder
    ```

9.  **Sync search index settings:**
    This command is crucial for configuring your Meilisearch indexes.

    ```shell
    sail artisan scout:sync-index-settings
    ```

10. **Install frontend dependencies:**

    ```shell
    sail npm install
    ```

11. **Compile assets and start services:**
    Open two separate terminal tabs for these commands.
    - In the first tab, compile frontend assets and watch for changes:
      ```shell
      sail npm run dev
      ```
    - In the second tab, start the Reverb WebSocket server:
      ```shell
      sail artisan reverb:start
      ```

### Sharing Your Local Development Environment

```bash
sail share --subdomain="pejoal"
```

## 👥 User Roles & Permissions

The system includes three main roles with specific permissions:

### 🔑 Admin Role

- Full access to all system features
- Payroll management (view, create, edit, approve)
- Employee profile management
- Payslip generation and approval
- Tax configuration management
- System settings and reports

### 👔 Manager Role

- View payroll reports
- View employee profiles (read-only)
- Manage assigned projects and tasks
- View time entries for their projects

### 👤 Employee Role

- Create and manage own time entries
- View own payslips and time tracking data
- Update personal profile information

---

## 💼 Payroll System Usage

### For Employees:

1. **Time Tracking**: Log worked hours by creating time entries linked to assigned tasks
2. **View Payslips**: Access generated payslips and payment history
3. **Profile Management**: Update personal information and tax exemptions

### For Administrators:

1. **Employee Setup**: Create employee profiles with hourly rates and tax settings
2. **Time Approval**: Review and approve employee time entries
3. **Payroll Processing**: Generate payslips for pay periods with automatic calculations
4. **Tax Management**: Configure tax rates and brackets for accurate deductions
5. **Reports**: Generate comprehensive payroll reports and analytics

### Key Features:

- **Automatic Calculations**: Hours worked calculated from task start/end times
- **Overtime Detection**: Automatic identification of overtime hours based on daily/weekly limits
- **Tax Deductions**: Progressive, flat, and percentage-based tax calculations
- **Approval Workflows**: Multi-level approval process for time entries and payslips
- **Audit Trail**: Complete activity logging for all payroll-related actions

---

## 🗂️ Database Schema

The payroll system adds the following key tables:

- `employee_profiles` - Employee payroll information
- `time_entries` - Tracked work hours with task linkage
- `payslips` - Generated pay statements
- `payroll_settings` - Company-wide payroll configuration
- `tax_configurations` - Flexible tax calculation rules

---

## 🌐 Localization

The application supports multiple languages with comprehensive translation coverage:

- All user interface text is translatable
- Payroll-specific translations included
- Session-based locale management
- Easy addition of new languages

### Adding New Locales:

1. Add locale to `config/laravellocalization.php`
2. Create language files in `lang/{locale}/`
3. Clear cache:
   ```shell
   sail artisan cache:clear
   sail artisan config:clear
   ```

---

## 🔧 Configuration

### Payroll Settings:

Configure company payroll parameters in the admin panel:

- Company information and tax ID
- Pay periods (weekly, bi-weekly, monthly)
- Standard working hours and overtime rules
- Default hourly rates and currencies
- Automatic time entry generation settings

### Tax Configuration:

Set up multiple tax types:

- **Progressive**: Income tax with multiple brackets
- **Percentage**: Fixed percentage taxes (Social Security, Medicare)
- **Flat**: Fixed amount deductions
- Priority-based calculation order

---

## 📊 Reports & Analytics

The payroll system provides comprehensive reporting:

- Employee time tracking summaries
- Payroll cost analysis by period
- Tax deduction breakdowns
- Overtime analysis and trends
- Export capabilities for accounting software integration

---

## 🔒 Security Features

- **Role-based access control** with granular permissions
- **Admin-only payroll access** to sensitive financial data
- **Audit logging** for all payroll activities
- **Data validation** and sanitization
- **CSRF protection** and secure headers
- **Rate limiting** on sensitive operations

---

## 🤝 Support

For support, please email me at [pejoal.official@gmail.com](mailto:pejoal.official@gmail.com).

---

## 👤 Author

- **Pejoal** - [GitHub](https://www.github.com/Pejoal)

---

## 📄 License

This project is proprietary software. All rights reserved.
