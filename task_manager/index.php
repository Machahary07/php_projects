<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-tasks"></i> Task Manager</h1>
            <div class="date-time">
                <div id="current-date"></div>
                <div id="current-time"></div>
            </div>
        </header>

        <div class="task-form">
            <h2>Add New Task</h2>
            <form id="addTaskForm">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description"></textarea>
                </div>
                <div class="form-group-row">
                    <div class="form-group">
                        <label for="due_date">Due Date:</label>
                        <input type="date" id="due_date" name="due_date">
                    </div>
                    <div class="form-group">
                        <label for="due_time">Due Time:</label>
                        <input type="time" id="due_time" name="due_time">
                    </div>
                </div>
                <button type="submit" class="btn-add">Add Task <i class="fas fa-plus"></i></button>
            </form>
        </div>

        <div class="task-list">
            <h2>Your Tasks</h2>
            <div class="filter-options">
                <button class="filter-btn active" data-filter="all">All</button>
                <button class="filter-btn" data-filter="pending">Pending</button>
                <button class="filter-btn" data-filter="completed">Completed</button>
                <button class="filter-btn" data-filter="today">Due Today</button>
            </div>
            <div id="tasksContainer">
                <!-- Tasks will be loaded here via AJAX -->
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>