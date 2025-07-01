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
        <input type="text" name="task" required placeholder="Enter task @Time">
        <button type="submit" name="add">Add Task</button>
    </form>

    <ul>
        <?php
        $result = $conn->query("SELECT * FROM db_tasks ORDER BY id DESC");
        while ($row = $result->fetch_assoc()) {
            $isChecked = $row['completed'] ? "checked" : "";
            $taskClass = $row['completed'] ? "completed" : "";
            $timestamp = strtotime($row['created_at']);
            $formatted = date("d M Y, h:i A", $timestamp);

            echo "<li>
                <form action='tasks.php' method='POST' style='display:inline'>
                    <input type='hidden' name='toggle_id' value='" . $row['id'] . "'>
                    <input type='hidden' name='completed' value='" . ($row['completed'] ? 0 : 1) . "'>
                    <input type='checkbox' name='toggle_complete' onChange='this.form.submit()' $isChecked>
                </form>

                <span class='$taskClass'>" . htmlspecialchars($row['task']) . "</span><br>
                <small>$formatted</small>

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
