# Webform-data-entry-validation-and-database-storage

<img width="1280" height="674" alt="image" src="https://github.com/user-attachments/assets/86da6f68-814c-4237-97e5-fb7546fd7d0d" />
<img width="1277" height="672" alt="image" src="https://github.com/user-attachments/assets/1839df22-51d1-441d-b46c-3e0d573f754f" />


# Webform Data Entry, Validation and Database Storage

A PHP and MySQL registration system that validates user-submitted form data server-side, stores valid entries in a MySQL database, and displays all currently stored users on the page.

Built as coursework for the "Web Programming Using PHP" module at Birkbeck, University of London.

## Overview

Users fill in a registration form (first name, middle name(s), surname, email, username, and password). On submission, every field is validated and sanitised server-side; if validation passes and the email/username aren't already taken, the record is inserted into a MySQL `usersTable` using a prepared PDO statement. The form is "sticky" — if validation fails, the page re-renders with the user's previous input still filled in and an inline error message next to each invalid field, so nothing needs to be retyped.

## Key Features

- **Server-side validation** for every field:
  - First name and surname must be alphanumeric
  - Middle name(s) are optional, but allow letters/numbers and spaces (for multiple middle names)
  - Email must match a valid email format
  - Username must be at least 10 characters long and alphanumeric
  - Password must be 10–30 characters and include at least one uppercase letter, one lowercase letter, one digit, and one special character, enforced with a regular expression
- **Duplicate checking** — email and username are checked against existing records before insertion, with a specific error shown for each if already taken
- **Sanitised input** — all submitted data is trimmed and passed through `htmlentities()` before validation or display
- **Sticky form** — on a failed submission, the form re-displays the user's previously entered values (except the password) alongside field-specific error messages, rather than clearing the form
- **Automatic table creation** — the app creates the `usersTable` table itself on load if it doesn't already exist, so no manual database setup is required beyond providing connection details
- **Prepared statements throughout** — all queries (insert, duplicate checks) use PDO prepared statements to protect against SQL injection
- **Live results display** — after a successful submission, the page lists every user currently stored in the database (ID, email, username)
- **Template-based rendering** — the form and page layout are built from separate HTML templates with placeholder tokens (e.g. `[+username+]`, `[+usernameError+]`) substituted server-side, keeping HTML and PHP logic separate

## Tech Stack

- **PHP** — form handling, validation logic, and rendering
- **MySQL** (via PDO) — data storage, accessed exclusively through prepared statements
- **HTML/CSS** — form and page templates

## Project Structure

```
├── index.php                  # Entry point: wires up the DB, processes the form, renders the page
├── includes/
│   ├── functions.php          # Validation, duplicate checks, table creation, data saving, rendering helpers
│   └── config.php             # Database connection (PDO)
├── html/
│   ├── userDataForm.html      # Registration form template
│   └── template.html          # Overall page layout template
```

## Database

Submitted registrations are stored in a MySQL `usersTable`:

| Column        | Type          | Notes                  |
|---------------|---------------|------------------------|
| `userID`      | INT           | Primary key, auto-increment |
| `firstName`   | VARCHAR(45)   | Required               |
| `middleNames` | VARCHAR(45)   | Optional               |
| `surname`     | VARCHAR(45)   | Required               |
| `email`       | VARCHAR(45)   | Required, unique       |
| `username`    | VARCHAR(30)   | Required, unique       |
| `password`    | VARCHAR(30)   | Required               |

The table is created automatically by `createTable()` on first run if it doesn't already exist, so this schema doesn't need to be set up manually — see `database.sql` for the equivalent `CREATE TABLE` statement if you'd prefer to create it directly.

## How It Works

1. `index.php` connects to the database and ensures `usersTable` exists.
2. If the form was submitted, `saveData()` validates and sanitises every field.
3. If validation passes and the email/username are not already taken, the record is inserted using a prepared statement, and the form is cleared.
4. If validation fails, the form re-renders with the previous values and specific error messages for each invalid field.
5. The page lists every user currently stored in the database below the form.

## Note on Related Coursework

This project's `usersTable` is reused (with the same schema) by a related coursework project, [Single-point-of-entry-authenticated-website-with-browsing-history-state-preservation](https://github.com/AH1906/Single-point-of-entry-authenticated-website-with-browsing-history-state-preservation), which implements login using accounts registered here.

## About

This project was built as coursework for the Web Programming Using PHP module of the BSc Computer Science (Part-Time) degree at Birkbeck, University of London.

**Live demo:** https://titan.dcs.bbk.ac.uk/~abutt20/p1/cwk2/task3/index.php
