<?php

class DatabaseManager {
    private $connection;
    
    public function __construct($host, $username, $password, $database) {
        $this->connection = new mysqli($host, $username, $password, $database);
        
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }
    
    public function create($table, $data) {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_map(function($value) {
            return "'" . $this->connection->real_escape_string($value) . "'";
        }, array_values($data)));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        
        return $this->query($sql);
    }
    
    public function read($table, $columns = '*', $where = '') {
        $sql = "SELECT $columns FROM $table";
        
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        
        return $this->query($sql);
    }
    
    public function update($table, $data, $where = '') {
        $set = implode(', ', array_map(function($key, $value) {
            return "$key = '" . $this->connection->real_escape_string($value) . "'";
        }, array_keys($data), array_values($data)));
        
        $sql = "UPDATE $table SET $set";
        
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        
        return $this->query($sql);
    }
    
    public function delete($table, $where = '') {
        $sql = "DELETE FROM $table";
        
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        
        return $this->query($sql);
    }
    
    private function query($sql) {
        $result = $this->connection->query($sql);
        
        if (!$result) {
            die("Query error: " . $this->connection->error);
        }
        
        return $result;
    }
    
    public function close() {
        $this->connection->close();
    }
}

// Kullanım örneği
$database = new DatabaseManager('localhost', 'username', 'password', 'database_name');

// CREATE işlemi örneği
$data = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => 25
];
$database->create('users', $data);

// READ işlemi örneği
$result = $database->read('users', '*', "age > 18");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo $row['name'] . ' - ' . $row['email'] . '<br>';
    }
}

// UPDATE işlemi örneği
$data = [
    'name' => 'Jane Doe',
    'age' => 30
];
$database->update('users', $data, "id = 1");

// DELETE işlemi örneği
$database->delete('users', "id = 2");

$database->close();
