# 🏢 Company Management System with Payroll

[🎥 Watch the Demo Video](https://github.com/user-attachments/assets/785440b8-69a3-4e4d-85de-5fb0ebd78125)

## About The Project

The Company Management System is a comprehensive Laravel-based solution designed to streamline and manage various aspects of a company's operations. This application provides advanced tools for managing clients, projects, tasks, phases, milestones, and includes a complete **payroll management system** with automated time tracking, intelligent salary calculations, progressive tax management, and comprehensive reporting.

Built with modern web technologies, the system offers real-time updates, full-text search capabilities, role-based access control, and multi-language support, making it suitable for companies of various sizes and industries.

---

## ✨ Features

### Core Management Features

- **Client Management**: Complete CRUD operations with client-project associations
- **Project Management**: Full project lifecycle management with phases and milestones
- **Task Management**: Advanced task tracking with assignments, priorities, and statuses
- **Phase & Milestone Tracking**: Organize projects with structured phases and milestones
- **Full-Text Search**: Lightning-fast, typo-tolerant search powered by **Meilisearch**
- **Real-time Notifications**: Instant updates and notifications using **Laravel Reverb** WebSockets
- **User Authentication**: Comprehensive security with Laravel Jetstream including:
  - Email verification
  - Two-Factor Authentication (2FA)
  - Password reset
  - Session management
- **Team Management**: Create teams, invite members, and manage team roles
- **Role-Based Access Control**: Granular permissions system using **Spatie Laravel Permission**
- **Activity Logging**: Complete audit trail of all system activities
- **Customizable Dashboard**: Real-time metrics and analytics with customizable widgets
- **Multi-language Support**: Full internationalization with session-based locale management
- **Responsive Design**: Mobile-first, fully responsive interface built with **Tailwind CSS**
- **RESTful API**: Complete API with authentication via **Laravel Sanctum**
- **Map Integration**: Location-based features with MapLibre GL integration

### 💰 Payroll Management Features

#### Core Payroll Capabilities

- **Automated Time Tracking**: Automatic time entry generation from task start/end datetime
- **Smart Time Entry Management**:
  - Overlap detection and prevention
  - Approval workflows
  - Manual entry and editing
  - Bulk approval capabilities
- **Employee Profile Management**:
  - Comprehensive employee information
  - Customizable hourly rates per employee
  - Overtime rate multipliers
  - Standard working hours configuration
  - Tax exemptions and deductions
  - Payment method preferences (bank transfer, cash, check)
  - Hire and termination date tracking
  - Active/inactive status management
- **Intelligent Salary Calculations**:
  - Automatic hourly rate application
  - Regular vs. overtime hour differentiation
  - Daily overtime detection
  - Configurable standard working hours
  - Automatic gross pay calculation
- **Advanced Tax Management**:
  - Multiple tax configuration support
  - Three tax types: **Progressive** (bracket-based), **Percentage** (fixed rate), **Flat** (fixed amount)
  - Pre-configured US tax system:
    - Federal Income Tax (progressive brackets)
    - Social Security Tax (6.2% up to wage base)
    - Medicare Tax (1.45%)
    - Additional Medicare Tax (0.9% for high earners)
    - State Income Tax (configurable)
    - Unemployment Insurance
  - Priority-based tax calculation order
  - Tax bracket configuration
  - Minimum/maximum income thresholds
  - Employee type targeting (all, employees, contractors)
- **Automated Payslip Generation**:
  - Single or bulk payslip creation
  - Detailed earnings breakdown
  - Comprehensive deductions listing
  - Unique payslip numbering system
  - Draft, approved, and paid statuses
  - Approval workflows
  - Pay period management (weekly, bi-weekly, monthly)
- **Admin-Only Access**: Secure payroll features restricted to administrators
- **Approval Workflows**:
  - Time entry approval process
  - Payslip approval system
  - Bulk approval capabilities
  - Audit trail for all approvals
- **Comprehensive Reports & Analytics**:
  - Daily, weekly, monthly, quarterly, and yearly reports
  - Employee-wise analysis
  - Project-wise payroll breakdown
  - Summary and detailed views
  - Payroll cost trends
  - Hours tracking statistics
  - Tax deduction summaries
- **Multiple Payment Methods**: Support for bank transfer, cash, and check payments
- **Overtime Management**:
  - Automatic overtime detection based on daily hours
  - Configurable overtime rate multipliers
  - Standard hours per day/week settings
  - Overtime vs. regular hours tracking

#### Payroll System Components

1. **Time Entries Module**:
   - Tracks worked hours linked to tasks and projects
   - Automatic calculation from task start/end times
   - Manual entry creation and editing
   - Overlap prevention
   - Approval workflows
   - User and admin access levels

2. **Employee Profiles Module**:
   - Hourly rate management
   - Tax exemption configuration
   - Payment method setup
   - Standard hours configuration
   - Employment period tracking
   - Active/inactive status

3. **Payroll Settings Module**:
   - Company information (name, address, tax ID)
   - Pay period configuration (weekly, bi-weekly, monthly)
   - Pay day settings
   - Default hourly rates
   - Working days selection
   - Standard work hours (start time, end time)
   - Break duration settings
   - Automatic overtime calculation toggle
   - Overtime approval requirements
   - Auto time entry generation
   - Currency and timezone settings

4. **Tax Configurations Module**:
   - Federal, state, and local tax setup
   - Social Security and Medicare taxes
   - Progressive tax brackets
   - Percentage-based taxes
   - Flat rate taxes
   - Tax priority ordering
   - Income thresholds
   - Tax descriptions and documentation

5. **Payslips Module**:
   - Detailed pay statements
   - Regular and overtime hours breakdown
   - Gross pay calculation
   - Tax deduction itemization
   - Other deductions tracking
   - Net pay computation
   - Bonus inclusion
   - Status management (draft, approved, paid)
   - PDF export (coming soon)
   - Bulk operations

6. **Reports & Analytics Module**:
   - Payroll dashboard with real-time statistics
   - Period-based reporting (weekly, monthly, quarterly, yearly)
   - Employee-wise reports
   - Project-wise payroll analysis
   - Summary and detailed views
   - Export capabilities
   - Visual charts and graphs
   - Trend analysis

---

## 🛠️ Tech Stack

### Backend

- **PHP**: 8.4
- **Laravel Framework**: 11.37.0
- **Database**: MariaDB 10 (MySQL-compatible)
- **ORM**: Eloquent with comprehensive relationships
- **Authentication**: Laravel Sanctum + Jetstream
- **Authorization**: Spatie Laravel Permission package v6.21
- **Search Engine**: Meilisearch with Laravel Scout v10.12
- **Real-time**: Laravel Reverb v1.0 (WebSocket server)
- **Localization**: Mcamara Laravel Localization v2.2

### Frontend

- **JavaScript Framework**: Vue.js 3.3.13
- **SPA Framework**: Inertia.js v2.0.3
- **CSS Framework**: Tailwind CSS v3.4
- **Build Tool**: Vite v6.0
- **Server-Side Rendering**: Vue SSR with @vue/server-renderer
- **UI Components**:
  - Headless UI for accessible components
  - vue-select for advanced dropdowns
  - vue-chart-3 + Chart.js for data visualization
  - vuedraggable for drag-and-drop
- **Maps**: MapLibre GL for location features
- **Internationalization**: laravel-vue-i18n v2.8

### Development Environment

- **Laravel Sail**: Docker-based development environment
- **Docker Services**:
  - PHP 8.4 application container
  - MariaDB 10 database
  - Meilisearch search engine
  - phpMyAdmin for database management
- **Testing**: Pest PHP v3.7 with Laravel plugin
- **Code Quality**: Laravel Pint for code styling
- **Package Manager**: Composer v2+, npm/yarn

### Additional Packages & Tools

- **Real-time Broadcasting**: Laravel Echo + Pusher.js
- **API Documentation**: Ziggy v2.0 for route generation
- **Form Validation**: Laravel Form Requests
- **Queue Processing**: Laravel Queue with database driver
- **Logging**: Laravel Pail for real-time log viewing
- **Development**: Concurrently for parallel command execution

---

## 🚀 Getting Started

Follow these instructions to get a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

Before you begin, ensure you have the following installed:

- **Docker & Docker Compose**: For running the containerized environment
- **PHP**: 8.2 or higher (for Composer)
- **Composer**: v2 or higher
- **Node.js**: v19.1.0 or higher
- **npm**: Latest version
- **Git**: For version control

### Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/Pejoal/manager.git
   cd manager
   ```

2. **Set up environment variables:**

   ```bash
   cp .env.example .env
   ```

   **Important**: Edit the `.env` file and configure:
   - Database credentials (DB_DATABASE, DB_USERNAME, DB_PASSWORD)
   - Application URL (APP_URL)
   - Meilisearch master key (MEILISEARCH_KEY)
   - Mail configuration (for notifications)
   - Reverb configuration (for real-time features)

3. **Install Laravel Sail (if not already installed):**

   ```bash
   composer require laravel/sail --dev --ignore-platform-reqs
   ```

4. **Create a convenient shell alias:**

   ```bash
   alias sail="./vendor/bin/sail"
   ```

   Add this to your `~/.bashrc` or `~/.zshrc` for persistence.

5. **Start the Docker containers:**

   ```bash
   sail up -d
   ```

   This starts:
   - Laravel application (http://localhost)
   - MariaDB database (port 3306)
   - Meilisearch (http://localhost:7700)
   - phpMyAdmin (http://localhost:81)

6. **Install PHP dependencies:**

   ```bash
   sail composer install --ignore-platform-reqs
   ```

7. **Generate application key:**

   ```bash
   sail artisan key:generate
   ```

8. **Run database migrations and seeders:**

   ```bash
   sail artisan migrate:fresh --seed
   ```

9. **Set up the payroll system:**

   ```bash
   sail artisan db:seed --class=PayrollSeeder
   ```

   This creates:
   - Admin, Manager, and Employee roles
   - Payroll permissions
   - Default payroll settings
   - US tax configurations (Federal, Social Security, Medicare, etc.)

10. **Create storage symlink:**

    ```bash
    sail artisan storage:link
    ```

11. **Configure Meilisearch indexes:**

    ```bash
    sail artisan scout:sync-index-settings
    ```

    This is crucial for search functionality to work properly.

12. **Install frontend dependencies:**

    ```bash
    sail npm install
    ```

13. **Start development servers:**

    Open **two separate terminal windows/tabs**:

    **Terminal 1 - Vite development server:**

    ```bash
    sail npm run dev
    ```

    **Terminal 2 - Laravel Reverb WebSocket server:**

    ```bash
    sail artisan reverb:start
    ```

14. **Access the application:**
    - Main application: http://localhost
    - phpMyAdmin: http://localhost:81
    - Meilisearch: http://localhost:7700

### Default Login Credentials

After seeding, you can log in with:

- **Email**: Your configured admin email (check `PayrollSeeder.php`)
- **Password**: The password you set during user creation

### Optional: Share Your Local Development

To share your local development environment:

```bash
sail share --subdomain="your-subdomain"
```

---

## 👥 User Roles & Permissions

The system includes three main roles with specific permissions managed by Spatie Laravel Permission:

### 🔑 Admin Role

**Full System Access:**

- Complete CRUD operations on all entities
- User management and role assignment
- System settings configuration

**Payroll Management:**

- View, create, edit, and delete employee profiles
- Activate/deactivate employee accounts
- View and approve all time entries
- Generate payslips (single and bulk)
- Approve and mark payslips as paid
- Configure payroll settings
- Manage tax configurations
- Access all payroll reports
- Generate time entries automatically from tasks
- Bulk operations on time entries and payslips

**Reporting:**

- Full access to all reports and analytics
- Export capabilities
- Custom report generation

### 👔 Manager Role

**Project Management:**

- Manage assigned projects and tasks
- View project-related time entries
- Monitor team member activities

**Limited Payroll Access:**

- View payroll reports (read-only)
- View employee profiles (read-only)
- View time entries for managed projects
- Create own time entries

**Reporting:**

- View summary reports
- Access to project-specific analytics

### 👤 Employee Role

**Personal Management:**

- View and update personal profile
- Manage own time entries (create, edit, delete)
- View own time tracking history

**Payroll Access:**

- View own payslips
- View own payment history
- View personal earnings and deductions
- Update tax exemptions

**Restrictions:**

- Cannot view other employees' data
- Cannot approve time entries
- Cannot generate payslips
- Cannot access system settings

---

## 💼 Payroll System Usage

### For Employees:

1. **Time Tracking**:
   - Log worked hours by creating time entries linked to assigned tasks
   - View the "Time Entries" page from the navigation
   - Click "Create Time Entry"
   - Select task, enter start/end datetime
   - Add optional description
   - Submit for approval

2. **View Payslips**:
   - Access generated payslips from the Payroll menu
   - View detailed breakdown of earnings and deductions
   - Download payslips (PDF feature coming soon)
   - Check payment history

3. **Profile Management**:
   - Update personal information
   - View hourly rate and overtime rate
   - Check standard working hours

### For Administrators:

1. **Employee Setup**:
   - Navigate to Payroll → Employee Profiles
   - Click "Create Employee Profile"
   - Enter employee details, hourly rates, tax settings
   - Set standard working hours and overtime multiplier
   - Configure payment method
   - Activate the profile

2. **Time Entry Management**:
   - Review pending time entries
   - Approve or reject submitted entries
   - Use bulk approve for multiple entries
   - Monitor for overlapping time entries
   - Generate automatic time entries from completed tasks

3. **Payroll Processing**:
   - Navigate to Payroll → Payslips
   - Choose "Generate Payslips" or "Generate Bulk"
   - Select pay period (start and end dates)
   - Choose employees
   - System automatically:
     - Calculates regular vs. overtime hours
     - Applies appropriate hourly rates
     - Computes tax deductions
     - Calculates net pay
   - Review draft payslips
   - Approve payslips
   - Mark as paid when payment is completed

4. **Tax Configuration**:
   - Access Payroll → Settings → Tax Configurations
   - Create tax configurations for different tax types
   - Set up progressive tax brackets
   - Configure minimum/maximum income thresholds
   - Set priority for calculation order
   - Activate/deactivate taxes as needed

5. **Payroll Settings**:
   - Configure company information
   - Set pay period (weekly, bi-weekly, monthly)
   - Define standard working hours
   - Set default hourly rates
   - Choose working days
   - Configure overtime settings
   - Enable/disable automatic features

6. **Reports & Analytics**:
   - Access Payroll → Reports
   - Select report type (summary, detailed, by employee, by project)
   - Choose time period
   - Filter by employees or projects
   - View charts and statistics
   - Export data for accounting software

### Key Features:

- **Automatic Calculations**: Hours worked automatically calculated from task start/end times
- **Overtime Detection**: System identifies overtime hours based on daily/weekly configured limits
- **Progressive Tax Calculations**: Support for bracket-based federal income tax
- **Multi-Tax Support**: Apply multiple taxes (federal, state, Social Security, Medicare) with priority ordering
- **Approval Workflows**: Multi-level approval process for time entries and payslips
- **Audit Trail**: Complete activity logging for all payroll-related actions with timestamps
- **Overlap Prevention**: System prevents overlapping time entries for the same employee
- **Bulk Operations**: Approve multiple time entries or payslips simultaneously
- **Real-time Updates**: Dashboard statistics update in real-time

---

## 📐 Project Structure & Organization

The system implements a sophisticated hierarchical structure for organizing work, providing flexibility and clarity in project management.

### Hierarchical Structure

```
Client(s)
    └── Project
            ├── Phases (Optional organizational layer)
            │   └── Milestones (Optional within phases)
            │       └── Tasks (Can be assigned to phases, milestones, or directly to projects)
            └── Milestones (Optional, can exist without phases)
                └── Tasks
```

### Structure Components

#### 🏢 Clients

- Top-level entity representing customers or stakeholders
- Multiple clients can be associated with a single project
- Stores contact information (name, email, phone)
- Can be linked to multiple projects

#### 📁 Projects

- Core organizational unit for all work
- **Unique Features:**
  - Slug-based URLs for SEO-friendly navigation
  - Start and end date tracking
  - Customizable status (from master settings)
  - Customizable priority (from master settings)
  - Multiple client associations
- **Contains:**
  - Phases (optional)
  - Milestones (optional)
  - Tasks (required)
- **Relationships:**
  - Belongs to multiple clients
  - Has many phases
  - Has many milestones
  - Has many tasks

#### 📊 Phases

- Optional organizational layer within projects
- Provides sequential structure to project workflow
- **Features:**
  - Customizable ordering (drag-and-drop support)
  - Description for detailed phase information
  - Can contain milestones and tasks
- **Use Cases:**
  - Breaking large projects into manageable segments
  - Sequential workflow management (e.g., Design → Development → Testing → Deployment)
  - Budget and timeline tracking per phase
- **Relationships:**
  - Belongs to one project
  - Has many milestones (optional)
  - Has many tasks

#### 🎯 Milestones

- Optional achievement markers within projects or phases
- Represent significant checkpoints or deliverables
- **Features:**
  - Can belong to a phase or directly to a project
  - Customizable ordering within parent
  - Description for milestone details
  - Can contain multiple tasks
- **Use Cases:**
  - Major deliverable completion
  - Project phase completion markers
  - Client approval points
  - Release versions
- **Relationships:**
  - Belongs to one project
  - Optionally belongs to one phase
  - Has many tasks

#### ✅ Tasks

- Fundamental work unit in the system
- Most granular level of work organization
- **Features:**
  - Can be assigned to:
    - Project directly (no phase/milestone)
    - Phase (within project)
    - Milestone (within project or phase)
  - Customizable ordering (drag-and-drop support)
  - Multiple user assignments
  - Customizable status (from master settings)
  - Customizable priority (from master settings)
  - Start and end datetime for time tracking
  - Description for task details
  - **Searchable** via Meilisearch (name and description)
- **Payroll Integration:**
  - Start/end datetime generates time entries automatically
  - Links to time tracking system
  - Used for employee hour calculation
- **Relationships:**
  - Belongs to one project (required)
  - Optionally belongs to one phase
  - Optionally belongs to one milestone
  - Belongs to many users (assignees)
  - Has one status
  - Has one priority
  - Has many time entries (payroll)

### Organization Flexibility

The system supports multiple organizational approaches:

1. **Simple Structure:**

   ```
   Project → Tasks
   ```

   Direct task assignment to projects without phases or milestones.

2. **Phase-Based Structure:**

   ```
   Project → Phases → Tasks
   ```

   Organize work into sequential phases with tasks under each phase.

3. **Milestone-Based Structure:**

   ```
   Project → Milestones → Tasks
   ```

   Track major achievements with tasks grouped under milestones.

4. **Complete Hierarchical Structure:**

   ```
   Project → Phases → Milestones → Tasks
   ```

   Full hierarchical organization with phases containing milestones, which contain tasks.

5. **Hybrid Structure:**
   ```
   Project
       ├── Phase 1 → Milestone A → Tasks
       ├── Phase 2 → Tasks (no milestone)
       └── Tasks (directly under project)
   ```
   Mix and match organizational structures within a single project.

### Ordering & Organization

- **Drag-and-Drop Ordering:**
  - Phases can be reordered within projects
  - Milestones can be reordered within projects or phases
  - Tasks can be reordered within their container (project, phase, or milestone)

- **Visual Organization:**
  - Color-coded statuses for quick visual identification
  - Priority indicators for urgency visibility
  - Progress tracking at each level

---

## ⚙️ Master Settings & Customization

The system provides comprehensive master settings for customizing the application to match your organization's workflow and terminology.

### Customizable Master Settings

#### 🎨 Visual Settings

Located in the Settings table, these control the visual appearance:

- **Client Color**: Default color for client-related items (`clients_color`)
- **Project Color**: Default color for project-related items (`projects_color`)
- **Task Color**: Default color for task-related items (`tasks_color`)

Access via: `Settings` model (singleton pattern)

#### 📊 Project Statuses

Fully customizable project lifecycle statuses stored in `project_statuses` table.

**Default Statuses:**

- **Planned** (🔴 #FF5733) - Project in planning phase
- **In Progress** (🔵 #36A2EB) - Active development
- **Completed** (🟢 #4BC0C0) - Successfully finished
- **On Hold** (🟠 #FFA500) - Temporarily paused
- **Cancelled** (⚫ #808080) - Discontinued
- **Deferred** (🔴 #C70039) - Postponed to future

**Features:**

- Create unlimited custom statuses
- Assign custom colors (hex codes)
- Edit existing status names and colors
- Delete unused statuses (with safety checks)
- Used across all projects system-wide

**Management:**

```
Routes: /project-statuses
Controller: ProjectStatusController
Model: ProjectStatus
```

#### 🎯 Project Priorities

Customizable priority levels for projects stored in `project_priorities` table.

**Default Priorities:**

- **Low** (🟡 #FFCE56) - Can be delayed if needed
- **Medium** (🟢 #00FF00) - Normal priority
- **High** (💗 #FF1493) - Important and urgent
- **Urgent** (🔴 #FF1500) - Critical, needs immediate attention

**Features:**

- Create unlimited custom priorities
- Assign custom colors for visual distinction
- Edit existing priority names and colors
- Delete unused priorities (with safety checks)
- Applied to projects for resource allocation

**Management:**

```
Routes: /project-priorities
Controller: ProjectPriorityController
Model: ProjectPriority
```

#### ✅ Task Statuses

Customizable task lifecycle statuses stored in `task_statuses` table.

**Default Statuses:**

- **Pending** (🔴 #E70A1D) - Awaiting start, `completed_field: false`
- **In Progress** (🔵 #36A2EB) - Currently being worked on, `completed_field: false`
- **Completed** (🟢 #2BFF10) - Finished successfully, `completed_field: true` ✓
- **On Hold** (🟠 #FFA500) - Temporarily paused, `completed_field: false`
- **Cancelled** (⚫ #808080) - Discontinued, `completed_field: false`
- **Review** (🟡 #FFD700) - Under review/approval, `completed_field: false`
- **Testing** (🟣 #8A2BE2) - In testing phase, `completed_field: false`

**Special Features:**

- **Completed Field**: Boolean flag indicating task completion status
- Statuses with `completed_field: true` mark tasks as finished
- Used for progress calculations and reporting
- Affects time entry generation (completed tasks can generate entries)

**Features:**

- Create unlimited custom statuses
- Assign custom colors
- Set completion flag for each status
- Edit existing statuses
- Delete unused statuses (with safety checks)
- Used across all tasks system-wide

**Management:**

```
Routes: /task-statuses
Controller: TaskStatusController
Model: TaskStatus
Fields: name, color, completed_field (boolean)
```

#### 🎯 Task Priorities

Customizable priority levels for tasks stored in `task_priorities` table.

**Default Priorities:**

- **Low** (🟡 #FFCE56) - Can be done later
- **Medium** (🟢 #00FF00) - Normal priority
- **High** (💗 #FF1493) - Important task
- **Urgent** (🔴 #FF1500) - Critical, needs immediate attention

**Features:**

- Create unlimited custom priorities
- Assign custom colors for visual distinction
- Edit existing priority names and colors
- Delete unused priorities (with safety checks)
- Used for task sorting and filtering
- Affects work queue organization

**Management:**

```
Routes: /task-priorities
Controller: TaskPriorityController
Model: TaskPriority
```

### Master Settings Management

#### Access Control

- All master settings require **authentication**
- Admin users have full CRUD access
- Settings are shared across the entire application
- Changes apply immediately system-wide

#### API Endpoints

**Project Statuses:**

```
GET    /project-statuses          - List all statuses
POST   /project-statuses          - Create new status
PUT    /project-statuses/{id}     - Update status
DELETE /project-statuses/{id}     - Delete status
```

**Project Priorities:**

```
GET    /project-priorities        - List all priorities
POST   /project-priorities        - Create new priority
PUT    /project-priorities/{id}   - Update priority
DELETE /project-priorities/{id}   - Delete priority
```

**Task Statuses:**

```
GET    /task-statuses             - List all statuses
POST   /task-statuses             - Create new status
PUT    /task-statuses/{id}        - Update status
DELETE /task-statuses/{id}        - Delete status
```

**Task Priorities:**

```
GET    /task-priorities           - List all priorities
POST   /task-priorities           - Create new priority
PUT    /task-priorities/{id}      - Update priority
DELETE /task-priorities/{id}      - Delete priority
```

#### Usage in Application

**Assigning to Projects:**

```php
$project->status_id = $projectStatus->id;
$project->priority_id = $projectPriority->id;
$project->save();
```

**Assigning to Tasks:**

```php
$task->status_id = $taskStatus->id;
$task->priority_id = $taskPriority->id;
$task->save();
```

**Checking Task Completion:**

```php
if ($task->status->completed_field) {
  // Task is completed, can generate time entry
  // Can trigger payroll calculations
}
```

### Customization Best Practices

1. **Plan Your Workflow:**
   - Map your organization's workflow before customizing
   - Consider existing processes and terminology
   - Ensure status names are clear and unambiguous

2. **Color Coding:**
   - Use consistent color schemes across related items
   - Red typically indicates urgency or issues
   - Green typically indicates completion or success
   - Blue typically indicates active work
   - Yellow/Orange typically indicates caution or pending states

3. **Status Progression:**
   - Design statuses to follow logical progression
   - Consider which statuses can transition to others
   - Mark appropriate statuses as "completed" for accurate tracking

4. **Priority Levels:**
   - Don't create too many priority levels (3-5 is optimal)
   - Ensure clear distinction between priority levels
   - Define criteria for each priority level

5. **Regular Review:**
   - Periodically review unused statuses/priorities
   - Archive or delete obsolete configurations
   - Update colors and names as workflow evolves

### System Settings

Additional system-wide settings include:

- **Payroll Settings**: Company information, pay periods, working hours (see Payroll section)
- **Tax Configurations**: Tax rates, brackets, and deductions (see Payroll section)
- **Localization**: Language preferences, date formats, timezones
- **Search Configuration**: Meilisearch index settings for full-text search
- **Notification Settings**: Real-time notification preferences

---

## 🗂️ Database Schema

### Core Tables

- `users` - User accounts with authentication
- `teams` - Team management
- `team_user` - Team membership
- `team_invitations` - Team invitations
- `clients` - Client information
- `projects` - Project details with status and priority
- `client_project` - Client-project associations
- `phases` - Project phases with ordering
- `milestones` - Project milestones with ordering
- `tasks` - Tasks with assignments, status, and priority
- `task_user` - Task assignments to users
- `task_statuses` - Customizable task statuses
- `task_priorities` - Customizable task priorities
- `project_statuses` - Customizable project statuses
- `project_priorities` - Customizable project priorities
- `activities` - Activity logging for audit trail

### Payroll Tables

- `employee_profiles` - Employee payroll information with rates and settings
- `time_entries` - Tracked work hours linked to tasks and projects
- `payslips` - Generated pay statements with detailed breakdowns
- `payroll_settings` - Company-wide payroll configuration (singleton)
- `tax_configurations` - Flexible tax calculation rules with brackets

### Permission Tables (Spatie)

- `roles` - User roles (admin, manager, employee)
- `permissions` - Granular permissions
- `role_has_permissions` - Role-permission associations
- `model_has_roles` - User-role associations
- `model_has_permissions` - Direct user permissions

---

## 🌐 Localization

The application supports multiple languages with comprehensive translation coverage:

### Supported Languages

- English (en) - Default
- German (de)
- Easily extensible for additional languages

### Features

- All user interface text is translatable
- Payroll-specific translations included (`payroll.php`)
- Session-based locale management
- Automatic locale detection from URL
- Language switcher in navigation
- RTL support ready

### Translation Files Location

- PHP translations: `lang/{locale}/` (e.g., `lang/en/`, `lang/de/`)
- JSON translations: `lang/{locale}.json` (e.g., `lang/php_en.json`)

### Adding New Locales

1. **Add locale to configuration:**
   Edit `config/laravellocalization.php` and add your locale to the `supportedLocales` array.

2. **Create language files:**

   ```bash
   mkdir -p lang/fr  # Example for French
   cp -r lang/en/* lang/fr/
   # Translate the files
   ```

3. **Add JSON translations:**

   ```bash
   cp lang/php_en.json lang/php_fr.json
   # Translate the JSON file
   ```

4. **Clear cache:**
   ```bash
   sail artisan cache:clear
   sail artisan config:clear
   sail artisan view:clear
   ```

---

## 🔧 Configuration

### Payroll Settings

Configure company payroll parameters through the admin panel (Payroll → Settings):

**Company Information:**

- Company name, address, and tax ID
- Currency (USD, EUR, etc.)
- Timezone for accurate time tracking

**Pay Period Configuration:**

- Weekly: Pay day (0-6, 0=Sunday)
- Bi-weekly: Every two weeks
- Monthly: Pay day (1-31)

**Working Hours:**

- Standard start time (e.g., 09:00)
- Standard end time (e.g., 17:00)
- Break duration in minutes
- Working days selection (Monday-Sunday)

**Overtime Rules:**

- Auto-calculate overtime toggle
- Require approval for overtime toggle
- Standard hours per day/week for overtime calculation

**Automation:**

- Auto-generate time entries from completed tasks
- Default hourly rate for new employees

### Tax Configuration

Set up multiple tax types through Settings → Tax Configurations:

**Progressive Taxes** (Income Tax with brackets):

```php
'brackets' => [
    ['min' => 0, 'max' => 10275, 'rate' => 10],
    ['min' => 10276, 'max' => 41775, 'rate' => 12],
    ['min' => 41776, 'max' => 89450, 'rate' => 22],
    // ... more brackets
]
```

**Percentage Taxes** (Fixed rate):

- Social Security: 6.2% (up to wage base)
- Medicare: 1.45% (all wages)
- State Tax: Configurable percentage

**Flat Taxes** (Fixed amount):

- Local taxes with fixed amount deductions

**Configuration Options:**

- Priority (calculation order)
- Minimum income threshold
- Maximum income cap
- Applies to (all employees, employees only, contractors only)
- Active/inactive status

### Environment Variables

Key environment variables in `.env`:

```env
# Application
APP_NAME="Project Manager"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=project_manager
DB_USERNAME=sail
DB_PASSWORD=password

# Meilisearch
MEILISEARCH_HOST=http://meilisearch:7700
MEILISEARCH_KEY=your-master-key
SCOUT_DRIVER=meilisearch

# Reverb (WebSockets)
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080

# Mail (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

---

## 📊 Reports & Analytics

The payroll system provides comprehensive reporting through the Reports module:

### Report Types

**Summary Report:**

- Total hours worked
- Total payroll cost
- Average hourly rate
- Number of employees
- Time period overview

**Detailed Report:**

- Individual payslip details
- Employee-wise breakdown
- Project-wise breakdown
- Line-by-line entries

**By Employee Report:**

- Employee payroll history
- Hours worked per employee
- Earnings per employee
- Tax deductions per employee

**By Project Report:**

- Project payroll costs
- Hours allocated to each project
- Project profitability insights

### Time Periods

- Weekly
- Monthly (default)
- Quarterly
- Yearly
- Custom date range

### Filters

- Select specific employees
- Choose specific projects
- Filter by pay period
- Filter by approval status

### Export Capabilities

- PDF export (coming soon)
- Excel export (coming soon)
- CSV data export
- Print-friendly format

### Dashboard Statistics

- Current month hours
- Current month payroll
- Pending approvals count
- Active employees count
- Month-over-month comparisons
- Upcoming payslips
- Recent time entries
- Recent payslips

---

## 🔒 Security Features

The application implements multiple layers of security:

### Authentication & Authorization

- **Laravel Sanctum**: API token authentication
- **Laravel Jetstream**: Feature-rich authentication scaffolding
- **Two-Factor Authentication**: Optional 2FA for enhanced security
- **Email Verification**: Required before account activation
- **Password Reset**: Secure password recovery
- **Session Management**: Active session viewing and termination
- **Role-based Access Control**: Granular permissions with Spatie Permission package

### Data Protection

- **Input Validation**: Laravel Form Requests for all inputs
- **XSS Prevention**: Automatic output sanitization
- **SQL Injection Protection**: Eloquent ORM with parameter binding
- **CSRF Protection**: Token-based CSRF protection on all forms
- **Mass Assignment Protection**: Fillable/guarded model properties

### Payroll Security

- **Admin-only Access**: Sensitive financial data restricted to administrators
- **Audit Logging**: Complete activity trail for all payroll operations
- **Data Validation**: Strict validation on all payroll calculations
- **Permission Checks**: Granular permission verification on every action
- **Time Entry Validation**: Overlap prevention and logical checks

### Additional Security

- **Rate Limiting**: Throttling on sensitive operations
- **Secure Headers**: Security headers configured
- **HTTPS Ready**: SSL/TLS support
- **Environment Variables**: Sensitive data in .env file
- **Database Encryption**: Password hashing with bcrypt
- **API Authentication**: Token-based API security

---

## 🧪 Testing

The project uses Pest PHP for testing:

```bash
# Run all tests
sail artisan test

# Run specific test suite
sail artisan test --testsuite=Feature

# Run with coverage
sail artisan test --coverage
```

### Test Structure

- **Feature Tests**: End-to-end testing of features
- **Unit Tests**: Individual component testing
- **Database**: Factory-based test data generation

---

## 🛠️ Development Commands

### Useful Artisan Commands

```bash
# Clear all caches
sail artisan optimize:clear

# Run queue worker
sail artisan queue:work

# View logs in real-time
sail artisan pail

# Generate IDE helper files (Soon)
sail artisan ide-helper:generate

# Run code style fixer
sail artisan pint

# Import time entries from tasks
sail artisan payroll:generate-time-entries
```

### Running All Services Together

The `composer dev` command runs all services concurrently:

```bash
sail composer dev
```

This starts:

- Laravel development server
- Queue worker
- Log viewer (Pail)
- Vite development server

---

## 🤝 Support

For support, please email me at [pejoal.official@gmail.com](mailto:pejoal.official@gmail.com).

---

## 👤 Author

**Pejoal**

- GitHub: [@Pejoal](https://www.github.com/Pejoal)
- Email: pejoal.official@gmail.com

---

## 📄 License

This project is proprietary software. All rights reserved.

---

## 🙏 Acknowledgments

- Laravel Team for the amazing framework
- Spatie for the permission package
- Meilisearch for the search engine
- All open-source contributors

---

## 📝 Changelog

### Version 1.0.0 (Current)

- Initial release with full payroll system
- Complete project management features
- Real-time notifications
- Multi-language support
- Comprehensive reporting

---

## 🗺️ Roadmap

### Upcoming Features

- PDF payslip generation
- Excel report exports
- Email notifications for payroll events
- Mobile application
- Advanced analytics and charts
- Expense tracking integration
- Invoice generation
- Client portal
- Time tracking mobile app
- Biometric time tracking integration

---

## 💡 Tips & Best Practices

### For Administrators

1. Configure payroll settings before creating employee profiles
2. Set up tax configurations based on your jurisdiction
3. Run `payroll:generate-time-entries` periodically to auto-create entries from tasks
4. Regularly backup the database
5. Review and approve time entries before generating payslips
6. Keep tax rates updated annually

### For Developers

1. Always use Form Requests for validation
2. Follow the repository pattern for data access
3. Use events for decoupled business logic
4. Write tests for critical payroll calculations
5. Document complex tax calculation logic
6. Use database transactions for payroll operations

---

## 🐛 Troubleshooting

### Common Issues

**Search not working:**

```bash
sail artisan scout:sync-index-settings
sail artisan scout:import "App\Models\Task"
```

**Reverb not connecting:**

- Check REVERB_SERVER_PORT in .env
- Ensure Reverb is running: `sail artisan reverb:start`
- Check firewall settings

**Payroll calculations incorrect:**

- Verify employee profile hourly rates
- Check tax configuration priorities
- Ensure time entries are approved
- Review standard hours settings

**Permission errors:**

```bash
sail artisan permission:cache-reset
sail artisan optimize:clear
```

---

_Last Updated: October 2025_
