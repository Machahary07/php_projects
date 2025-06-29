document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const addTaskForm = document.getElementById('addTaskForm');
    const tasksContainer = document.getElementById('tasksContainer');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const currentDateElement = document.getElementById('current-date');
    const currentTimeElement = document.getElementById('current-time');
    
    // Current filter
    let currentFilter = 'all';
    
    // Initialize the app
    updateDateTime();
    setInterval(updateDateTime, 1000);
    loadTasks();
    checkDueTasks();
    setInterval(checkDueTasks, 60000); // Check every minute
    
    // Request notification permission
    if ('Notification' in window) {
        Notification.requestPermission().then(permission => {
            if (permission !== 'granted') {
                console.log('Notification permission denied');
            }
        });
    }
    
    // Form submission
    addTaskForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(addTaskForm);
        
        fetch('tasks.php?action=add', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addTaskForm.reset();
                loadTasks();
                showNotification('Task added successfully!');
            } else {
                alert('Error adding task: ' + data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    });
    
    // Filter buttons
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            loadTasks();
        });
    });
    
    // Load tasks from server
    function loadTasks() {
        fetch(`tasks.php?action=get&filter=${currentFilter}`)
            .then(response => response.json())
            .then(tasks => {
                if (tasks.length === 0) {
                    tasksContainer.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-clipboard-list"></i>
                            <h3>No tasks found</h3>
                            <p>Add a new task to get started</p>
                        </div>
                    `;
                    return;
                }
                
                tasksContainer.innerHTML = '';
                tasks.forEach(task => {
                    const taskElement = createTaskElement(task);
                    tasksContainer.appendChild(taskElement);
                });
            })
            .catch(error => console.error('Error:', error));
    }
    
    // Create task HTML element
    function createTaskElement(task) {
        const dueDate = task.due_date ? new Date(task.due_date) : null;
        const dueTime = task.due_time || '';
        const now = new Date();
        
        // Format due date
        let dueText = '';
        if (dueDate) {
            const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
            dueText = dueDate.toLocaleDateString('en-US', options);
            
            if (dueTime) {
                const [hours, minutes] = dueTime.split(':');
                dueText += ` at ${formatTime(hours, minutes)}`;
            }
        }
        
        // Check if task is overdue
        const isOverdue = dueDate && dueDate < now && !task.is_completed;
        const dueClass = isOverdue ? 'urgent' : '';
        
        const taskElement = document.createElement('div');
        taskElement.className = `task ${task.is_completed ? 'completed' : ''}`;
        taskElement.dataset.id = task.id;
        
        taskElement.innerHTML = `
            <div class="task-info">
                <div class="task-title">
                    ${task.title}
                </div>
                ${task.description ? `<div class="task-description">${task.description}</div>` : ''}
                ${dueText ? `<div class="task-due ${dueClass}"><i class="far fa-clock"></i> ${dueText}</div>` : ''}
            </div>
            <div class="task-actions">
                <button class="task-btn complete" title="${task.is_completed ? 'Mark as pending' : 'Mark as complete'}">
                    <i class="fas fa-${task.is_completed ? 'undo' : 'check'}"></i>
                </button>
                <button class="task-btn delete" title="Delete task">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        
        // Add event listeners to buttons
        const completeBtn = taskElement.querySelector('.complete');
        const deleteBtn = taskElement.querySelector('.delete');
        
        completeBtn.addEventListener('click', function() {
            toggleTaskCompletion(task.id);
        });
        
        deleteBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this task?')) {
                deleteTask(task.id);
            }
        });
        
        return taskElement;
    }
    
    // Toggle task completion status
    function toggleTaskCompletion(taskId) {
        fetch('tasks.php?action=toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${taskId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadTasks();
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    // Delete task
    function deleteTask(taskId) {
        fetch('tasks.php?action=delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${taskId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadTasks();
                showNotification('Task deleted successfully!');
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    // Update date and time
    function updateDateTime() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        currentDateElement.textContent = now.toLocaleDateString('en-US', options);
        currentTimeElement.textContent = now.toLocaleTimeString('en-US');
    }
    
    // Format time (12-hour format)
    function formatTime(hours, minutes) {
        const h = parseInt(hours);
        const m = minutes || '00';
        const ampm = h >= 12 ? 'PM' : 'AM';
        const hour12 = h % 12 || 12;
        return `${hour12}:${m} ${ampm}`;
    }
    
    // Check for due tasks and show notifications
    function checkDueTasks() {
        if (Notification.permission !== 'granted') return;
        
        fetch('tasks.php?action=get&filter=pending')
            .then(response => response.json())
            .then(tasks => {
                const now = new Date();
                
                tasks.forEach(task => {
                    if (!task.due_date) return;
                    
                    const dueDate = new Date(task.due_date);
                    const dueTime = task.due_time ? task.due_time.split(':') : [23, 59];
                    dueDate.setHoursparseInt(dueTime[0]), dueDate.setMinutes(parseInt(dueTime[1]));
                    
                    // Check if task is due within the next 15 minutes
                    const timeDiff = (dueDate - now) / (1000 * 60); // difference in minutes
                    
                    if (timeDiff > 0 && timeDiff <= 15) {
                        showNotification(`Task "${task.title}" is due soon!`);
                    }
                });
            })
            .catch(error => console.error('Error:', error));
    }
    
    // Show browser notification
    function showNotification(message) {
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('Task Manager', {
                body: message,
                icon: 'https://cdn-icons-png.flaticon.com/512/3063/3063188.png'
            });
        }
    }
});