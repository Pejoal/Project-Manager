# GitHub Copilot Instructions

## Project Overview

This is a Laravel-based Company Management System with Vue.js frontend using Inertia.js. The system manages clients, projects, tasks, phases, milestones, and payroll functionality.

## Tech Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Frontend**: Vue.js 3, Inertia.js, Tailwind CSS
- **Database**: MySQL/PostgreSQL
- **Search**: Meilisearch with Laravel Scout
- **Real-time**: Laravel Reverb
- **Authentication**: Laravel Sanctum, Jetstream
- **Permissions**: Spatie Laravel Permission
- **Localization**: Mcamara Laravel Localization
- **Development**: Laravel Sail (Docker)

## Architecture Patterns

- Repository Pattern for data access
- Service Layer for business logic
- Event-driven architecture for notifications
- Policy-based authorization
- Resource API responses
- Form Request validation

## Code Style & Conventions

- Follow PSR-12 coding standards
- Use Laravel naming conventions
- Prefer explicit return types
- Use typed properties where possible
- Follow Vue 3 Composition API
- Use TypeScript for complex components

## Database Design

- Use foreign key constraints
- Implement soft deletes where appropriate
- Use meaningful table and column names
- Index frequently queried columns
- Use migrations for all schema changes

## Key Features to Maintain

1. **Multi-language Support**: All text must be translatable using Laravel localization
2. **Role-based Access Control**: Use Spatie Laravel Permission package
3. **Real-time Updates**: Implement with Laravel Reverb
4. **Full-text Search**: Use Meilisearch for fast, typo-tolerant search
5. **Activity Logging**: Track all user actions
6. **Responsive Design**: Mobile-first approach with Tailwind CSS

## Payroll System Requirements

- Time tracking based on task start_datetime and end_datetime
- Hourly rate management per user
- Automatic salary calculation with tax considerations
- Admin-only access to payroll features
- Daily, weekly, monthly payroll reports
- Payslip generation
- Tax calculation and deduction tracking

## Component Structure

- Controllers: Handle HTTP requests, validate input, return responses
- Models: Eloquent models with relationships and business logic
- Services: Business logic and complex operations
- Repositories: Data access layer
- Resources: API response formatting
- Requests: Form validation
- Policies: Authorization logic
- Events/Listeners: Decoupled business logic

## Vue.js Patterns

- Use Composition API
- Implement proper prop validation
- Use computed properties for derived state
- Emit events for parent communication
- Implement proper form handling with Inertia forms
- Use slots for flexible component composition

## Security Best Practices

- Validate all input using Form Requests
- Use authorization policies
- Sanitize output to prevent XSS
- Use CSRF protection
- Implement rate limiting
- Use secure headers

## Performance Considerations

- Eager load relationships to avoid N+1 queries
- Use database indexing appropriately
- Implement caching where beneficial
- Optimize frontend bundle size
- Use lazy loading for routes and components

## Testing Guidelines

- Write Feature tests for endpoints
- Write Unit tests for services and models
- Use factories for test data
- Mock external services
- Test authorization scenarios

## Error Handling

- Use appropriate HTTP status codes
- Provide meaningful error messages
- Log errors for debugging
- Handle validation errors gracefully
- Implement global error boundaries in Vue

## API Design

- Follow RESTful conventions
- Use resource routes
- Implement proper status codes
- Use consistent response formats
- Include pagination for lists
- Implement filtering and sorting

When generating code:

1. Always consider security implications
2. Follow the established patterns in the codebase
3. Include proper error handling
4. Add meaningful comments for complex logic
5. Ensure all text is translatable
6. Implement proper validation
7. Consider performance impact
8. Write clean, maintainable code
9. do not give me commands to run in terminal unless necessary (try first to create the files directly)
