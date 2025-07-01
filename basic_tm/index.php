<?php include 'dbconnect.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Task Manager with Time & Delete</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Task Manager</h2>

    <form action="tasks.php" method="POST">
        <input type="text" name="task" required placeholder="Enter task">
        <button type="submit" name="add">Add Task</button>
    </form>

    <ul>
        <?php
        $result = $conn->query("SELECT * FROM db_tasks ORDER BY id DESC");
        while ($row = $result->fetch_assoc()) {
            echo "<li>
                <strong>" . htmlspecialchars($row['task']) . "</strong><br>
                <small>" . $row['created_at'] . "</small>
                <form action='tasks.php' method='POST' style='display:inline'>
                    <input type='hidden' name='delete_id' value='" . $row['id'] . "'>
                    <button type='submit' name='delete'>❌</button>
                </form>
            </li>";
        }
        ?>
    </ul>

    <script src="script.js"></script>
</body>
</html>
