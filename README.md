# Smart Complaint System

A PHP and MySQL web application for reporting, tracking, and managing civic complaints. Citizens can create accounts, submit complaints with optional images, comment and vote on complaints, view rankings, and update their profiles. Administrators can review complaints, update their status, and view dashboard charts.

## Features

- Citizen registration and login
- Administrator login
- Complaint submission with category, location, severity, and image upload
- Complaint search, filtering, details, comments, upvotes, and downvotes
- Citizen profiles and rankings
- Administrator complaint management and status updates
- Dashboard charts and status summaries

## Requirements

- PHP 8.1 or later with the `mysqli` extension enabled
- MySQL 5.7+ or MariaDB
- A web server, or PHP's built-in development server
- Git, if cloning the repository

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/nagasumukh01/Smart_Complaint_System.git
   cd Smart_Complaint_System
   ```

2. Create a MySQL database named `problem_reporter`.

3. Import the application schema and seed data into that database. The current repository does not include a `.sql` schema dump, so the required tables must be supplied separately or created before using the application.

4. Configure the database connection. The current PHP files use these default values:

   | Setting | Value |
   | --- | --- |
   | Host | `127.0.0.1` |
   | Port | `3307` |
   | Username | `root` |
   | Password | empty |
   | Database | `problem_reporter` |

   Update the connection values consistently in the PHP files if your MySQL installation uses different credentials or a different port. The shared connection settings are in `db_connect.php`; some endpoint files currently define their own connection inline.

5. Ensure the `uploads/` directory exists and is writable by PHP. It stores profile images and complaint attachments.

## Run Locally

From the project directory, start PHP's development server:

```bash
php -S 127.0.0.1:8000 -t .
```

Open the login page:

```text
http://127.0.0.1:8000/login.html
```

On Windows, if PHP was installed without a default `php.ini`, enable `mysqli` when starting the server. For example:

```powershell
$php = (Get-Command php).Source
$extensionDir = Join-Path (Split-Path $php) 'ext'
php -d extension_dir="$extensionDir" -d extension=mysqli -S 127.0.0.1:8000 -t .
```

## Project Structure

| Path | Purpose |
| --- | --- |
| `login.html`, `register.html` | Citizen and administrator entry forms |
| `login.php`, `register.php`, `logout.php` | Authentication and session handling |
| `citizen_*.php` | Citizen dashboard, profile, and rankings |
| `admin_dashboard.php` | Administrator complaint management |
| `submit_problem.php`, `problem_detail.php` | Complaint creation and details |
| `problem_comments.php`, `upvote.php`, `downvote.php` | Complaint interaction endpoints |
| `filter_problems.php`, `search_problems.php` | Complaint search and filtering |
| `charts.php`, `status_chart.php` | Dashboard chart data and views |
| `db_connect.php` | Shared database connection helper |
| `style.css`, `script.js` | Frontend styles and JavaScript |
| `uploads/` | Uploaded profile images and complaint attachments |

## Notes

- Authentication uses PHP sessions.
- Passwords created during registration are stored with PHP's `password_hash` function.
- Uploaded files are saved under `uploads/` with generated filenames.
- This project does not currently include automated tests or dependency lockfiles.

## License

No license file is currently included in this repository.