# Personal Details PHP App

## Setup

1. Create a MySQL database and update `config.php` with your database credentials.
2. Run the SQL in `create_table.sql` to create the table.

```sql
-- from create_table.sql
CREATE TABLE IF NOT EXISTS personal_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT,
    country VARCHAR(100) NOT NULL,
    gender VARCHAR(20) NOT NULL,
    interests TEXT,
    birth_date DATE NOT NULL,
    rating INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

3. Place the project directory under a PHP-capable web server root.
   - Example: `/var/www/html/personal-details-form`
   - Or use a local server from the project folder:

```bash
php -S localhost:8000
```

4. Open the front-end form:
   - `http://localhost:8000/form.php`

5. View saved records:
   - `http://localhost:8000/list.php`

## Files

- `config.php` - database connection and helper
- `form.php` - personal details submission form
- `save.php` - form POST handler to insert records
- `list.php` - list all saved records with Edit/Delete links
- `edit.php` - update an existing record
- `delete.php` - delete a record
- `create_table.sql` - SQL for creating the required table
