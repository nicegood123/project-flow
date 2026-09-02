# Project Flow

Project Flow is a Laravel and Vue application for managing client projects.

Repository: https://github.com/nicegood123/project-flow

## Development Workflow

The project scope and implementation plan were drafted in ChatGPT before development began. The work was then organized into GitHub issues and implemented with GitHub Copilot in VS Code. GitHub Copilot was used to help create the Laravel backend, Vue frontend, tests, documentation, and validation commands; implementation decisions and final verification remained part of the development workflow.

Git-recorded development time: 36 minutes 45 seconds, from the first project commit at 15:26:40 to the latest documentation update at 16:03:25 on 2026-09-02 (UTC+08:00).

## Technology Stack

- Laravel 13 and PHP 8.3
- Vue 3 Composition API with Inertia.js
- TypeScript and Vite
- Tailwind CSS
- MySQL with Laravel Eloquent
- Laravel Form Requests, API Resources, and session authentication
- Pest, Laravel Pint, PHPStan, and Vite Plus quality checks

## Setup Instructions

### Requirements

- PHP 8.3 or later
- Composer
- Node.js 20 or later and npm
- MySQL, or another Laravel-supported database

### Install and Run

1. Clone the repository and enter the project directory.
2. Copy `.env.example` to `.env` and configure the `DB_*` values for a local database.
3. Install dependencies and build the frontend:

   ```bash
   composer setup
   ```

4. Seed development data:

   ```bash
   php artisan db:seed
   ```

5. Start the application:

   ```bash
   composer dev
   ```

Open the URL reported by Laravel, normally `http://localhost:8000`. Register an account at `/register`, then use the Projects workspace at `/projects`.

### Quality Checks

```bash
php artisan test
./vendor/bin/phpstan analyse --memory-limit=512M
npm run types:check
npm run check
```

## Features Implemented

- Session-based registration, login, and logout using Laravel authentication.
- Protected Project REST API with JSON responses and standard Laravel validation errors.
- Project CRUD with Form Requests and API Resources.
- Project list, create, edit, and delete interfaces built with Vue 3 Composition API.
- Project search by client or project name.
- Status and priority filtering.
- Whitelisted sorting with ascending and descending directions.
- Reusable project form, status indicator, and priority indicator components.
- Project factory, seeder, and feature-test coverage for authentication and the API.

## GitHub Issue Plan

The following issues were prepared as the implementation plan for GitHub Copilot:

- [x] #1 Initialize Laravel Project
- [x] #2 Configure Vue Frontend
- [x] #3 Create Projects Database Migration
- [x] #4 Create Project Eloquent Model
- [x] #5 Add Project Factory and Seeder
- [x] #6 Create Project REST API Routes
- [x] #7 Implement Project CRUD API
- [x] #8 Add Project Request Validation
- [x] #9 Standardize Project API Responses
- [x] #10 Create Project List Page
- [x] #11 Add Status and Priority Indicators
- [x] #12 Create Project Form
- [x] #13 Connect Create Form to REST API
- [x] #14 Create Edit Project Form
- [x] #15 Connect Edit Form to REST API
- [x] #16 Implement Project Deletion
- [x] #17 Add Project API Feature Tests
- [x] #18 Improve Project API Architecture
- [x] #19 Add Project Search
- [x] #20 Add Project Filtering
- [x] #21 Add Project Sorting
- [x] #22 Add Authentication with Registration

## Assumptions Made

- Each registered user can manage the shared project list; projects are not assigned to individual users for this MVP.
- Project status and priority use the values defined by the product requirements.
- Start and due dates are optional, but when both are present the due date cannot precede the start date.
- The frontend and Laravel application are served from the same origin, allowing session-cookie authentication and CSRF protection for API mutations.
- Pagination is not required for the current MVP; search, filtering, and sorting are implemented as database queries so they can be combined with pagination later.

## Technical Reflection

The API separates responsibilities cleanly: controllers coordinate requests and responses, Form Requests own validation, API Resources define the response contract, and the Eloquent model remains limited to persistence concerns. The Vue frontend keeps HTTP communication in a small API client and reuses presentation components for project forms and indicators.

The main tradeoff is a deliberately simple shared-project authorization model. It keeps the MVP focused while leaving a clear future path to project ownership, policies, and paginated query results.