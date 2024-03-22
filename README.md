# PHP Database Operations

This PHP script provides classes for database connectivity and common database operations such as insert, select, update, and delete. It utilizes PDO (PHP Data Objects) for secure database connections and prepared statements to prevent SQL injection attacks.

## Installation

1. Clone this repository to your local machine:
   ```
   git clone <repository-url>
   ```

2. Make sure you have PHP installed on your system.

3. Update the `config.php` file with your database configuration details.

## Usage

1. Include the `config.php` file and the necessary PHP files for database operations:

   ```php
   require_once 'config.php';
   ```

2. Create an instance of the `DatabaseOperations` class:

   ```php
   $dbOperations = new DatabaseOperations();
   ```

3. Perform database operations such as select, insert, update, and delete:

   ```php
   // Select example
   $result = $dbOperations->select('table_name', ['column' => 'value']);
   var_dump($result);

   // Insert example
   $data = ['column1' => 'value1', 'column2' => 'value2'];
   $dbOperations->insert('table_name', $data);

   // Update example
   $data = ['column1' => 'new_value1', 'column2' => 'new_value2'];
   $conditions = 'column1 = :column1';
   $dbOperations->update('table_name', $data, $conditions);

   // Delete example
   $conditions = 'column1 = :column1';
   $dbOperations->delete('table_name', $conditions);
   ```

## Configuration

Database connection details are stored in the `config.php` file. Update this file with your database host, name, username, and password:

```php
define('DB_CONFIG', [
    'host' => 'Host_Name',
    'database' => 'Database_Name',
    'username' => 'Your_Username',
    'password' => 'Your_Password'
]);
```

## Contributing

Contributions are welcome! If you find any issues or have suggestions for improvement, please open an issue or create a pull request.
