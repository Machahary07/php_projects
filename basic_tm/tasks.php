<?php
include 'dbconnect.php';

// Add task
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $task = $conn->real_escape_string($_POST['task']);
    if (!empty($task)) {
        $conn->query("INSERT INTO db_tasks (task, created_at) VALUES ('$task', NOW())");
    }
    header("Location: index.php");
    exit();
}

// Delete task
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $id = (int) $_POST['delete_id'];
    $conn->query("DELETE FROM db_tasks WHERE id = $id");
    header("Location: index.php");
    exit();
}
