<?php
// Include the database configuration file
require_once 'config.php';

// Database class for establishing a connection
class Database
{
    private $host;
    private $database;
    private $username;
    private $password;
    public $conn;

    // Constructor to establish the database connection
    public function __construct($host, $database, $username, $password)
    {
        $this->host = $host;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        
        try {
            // Creating a PDO connection
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->database", $this->username, $this->password);
            // Setting PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Throw an exception instead of echoing the error
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }

    // Method to get the database connection
    public function getConnection()
    {
        return $this->conn;
    }
}

// Base class for common database operations
class DBConnector
{
    protected $connection;

    // Constructor to establish the database connection
    public function __construct()
    {
        // Creating an instance of the Database class
        $database = new Database(DB_CONFIG['host'], DB_CONFIG['database'], DB_CONFIG['username'], DB_CONFIG['password']);
        // Getting the database connection
        $this->connection = $database->getConnection();
    }

    // Method to prepare and bind parameters for executing a statement
    protected function prepareAndBind($query, $params)
    {
        try {
            // Prepare the SQL statement
            $statement = $this->connection->prepare($query);
            // If preparing the statement fails, throw an exception
            if ($statement === false) {
                throw new Exception("Failed to prepare statement.");
            }
            // Bind parameters to the prepared statement
            if (!empty($params)) {
                foreach ($params as $key => &$value) {
                    $statement->bindParam(":$key", $value);
                }
            }
            return $statement;
        } catch (PDOException $e) {
            throw new Exception("Error preparing statement: " . $e->getMessage());
        }
    }

    // Method to execute a prepared statement
    protected function executePreparedStatement($statement)
    {
        try {
            // Execute the prepared statement
            if ($statement->execute() === false) {
                throw new Exception("Failed to execute statement.");
            }
            return $statement;
        } catch (PDOException $e) {
            throw new Exception("Error executing statement: " . $e->getMessage());
        }
    }
}

// Class for database operations such as insert, select, update, delete
class DatabaseOperations extends DBConnector
{
    // Method to insert data into the specified table
    public function insert($tableName, $data)
    {
        // Construct the SQL query for insertion
        $columns = implode(", ", array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $query = "INSERT INTO $tableName ($columns) VALUES ($placeholders)";
        // Prepare and execute the SQL statement
        $statement = $this->prepareAndBind($query, $data);
        return $this->executePreparedStatement($statement);
    }

    // Method to select data from the specified table
    public function select($tableName, $conditions = [])
    {
        // Construct the WHERE clause based on provided conditions
        $whereClause = '';
        if (!empty($conditions)) {
            $whereClause = "WHERE " . implode(" AND ", array_map(function ($key) {
                return "$key = :$key";
            }, array_keys($conditions)));
        }

        // Construct the SQL query for selection
        $query = "SELECT * FROM $tableName $whereClause";
        // Prepare and execute the SQL statement
        $statement = $this->prepareAndBind($query, $conditions);
        if ($statement === false) {
            return false;
        }
        if ($statement->execute() === false) {
            return false;
        }
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to update data in the specified table
    public function update($tableName, $data, $conditions)
    {
        // Construct the SET clause for update
        $set = implode(", ", array_map(function ($key) {
            return "$key = :$key";
        }, array_keys($data)));

        // Construct the SQL query for update
        $query = "UPDATE $tableName SET $set WHERE $conditions";
        // Prepare and execute the SQL statement
        $statement = $this->prepareAndBind($query, $data);
        return $this->executePreparedStatement($statement);
    }

    // Method to delete data from the specified table
    public function delete($tableName, $conditions)
    {
        // Construct the SQL query for deletion
        $query = "DELETE FROM $tableName WHERE $conditions";
        // Prepare and execute the SQL statement
        $statement = $this->connection->prepare($query);
        return $this->executePreparedStatement($statement);
    }
}

// Configuration for database connection
define('DB_CONFIG', [
    'host' => 'Host_Name',
    'database' => 'Database_Name',
    'username' => 'Your_Username',
    'password' => 'Your_Password'
]);

// Usage Example:
// $dbOperations = new DatabaseOperations();
// $result = $dbOperations->select('table_name', ['column' => 'value']);
// var_dump($result);
?>
