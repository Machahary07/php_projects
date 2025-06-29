<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'task_manager';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database and table if they don't exist
$conn->query("CREATE DATABASE IF NOT EXISTS $database");
$conn->select_db($database);
$conn->query("
    CREATE TABLE IF NOT EXISTS tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        due_date DATE,
        due_time TIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_completed BOOLEAN DEFAULT FALSE
    )
");
?>