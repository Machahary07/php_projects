<?php
header('Content-Type: application/json');
include 'db_connect.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        $title = $_POST['title'];
        $description = $_POST['description'] ?? '';
        $due_date = $_POST['due_date'] ?? null;
        $due_time = $_POST['due_time'] ?? null;
        
        $stmt = $conn->prepare("INSERT INTO tasks (title, description, due_date, due_time) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $description, $due_date, $due_time);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
        break;

    case 'delete':
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $id);
        echo json_encode(['success' => $stmt->execute()]);
        break;

    case 'toggle':
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE tasks SET is_completed = NOT is_completed WHERE id = ?");
        $stmt->bind_param("i", $id);
        echo json_encode(['success' => $stmt->execute()]);
        break;

    case 'get':
        $filter = $_GET['filter'] ?? 'all';
        $query = "SELECT * FROM tasks";
        
        switch ($filter) {
            case 'pending':
                $query .= " WHERE is_completed = FALSE";
                break;
            case 'completed':
                $query .= " WHERE is_completed = TRUE";
                break;
            case 'today':
                $query .= " WHERE due_date = CURDATE()";
                break;
        }
        
        $query .= " ORDER BY due_date, due_time";
        $result = $conn->query($query);
        
        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }
        
        echo json_encode($tasks);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>