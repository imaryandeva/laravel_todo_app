<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        input[type=checkbox]:checked+label span:first-of-type {
            background-color: #10B981;
            border-color: #10B981;
            color: #fff;
        }

        input[type=checkbox]:checked+label span:nth-of-type(2) {
            text-decoration: line-through;
            color: #9CA3AF;
        }
    </style>
</head>
<body>
<div class="w-screen h-screen font-medium">
  

    <div class="flex flex-grow items-center justify-center bg-gray-900 h-full">
        
        <div class="max-w-full p-8 bg-gray-800 rounded-lg shadow-lg w-[700px] text-gray-200">
              <div class="text-center py-4">
    <h1 class="text-3xl font-bold text-white underline">Simple Todo App</h1>
</div>
            <div class="flex items-center mb-6">
                <div class="flex items-center w-full h-8 px-2 mt-2 text-sm font-medium rounded" ">
                    <input id="new-task" class="flex-grow h-8 ml-4 bg-transparent focus:outline-none font-medium" type="text" placeholder="add a new task">
                    <button onclick="addTask()">
                        <svg class="w-5 h-5 text-gray-400 fill-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    </button>
                </div>
            </div>
            <hr class="pb-2">
            <div id="tasks">
               
                @foreach($tasks as $task)
                    <div class="flex justify-between" id="task-{{ $task->id }}">
                        <input class="hidden" type="checkbox" id="task_{{ $task->id }}" {{ $task->completed ? 'checked' : '' }} onclick="completeTask({{ $task->id }})">
                        <label class="flex items-center w-[85%] h-10 px-2 rounded cursor-pointer hover:bg-gray-900" for="task_{{ $task->id }}">
                            <span class="flex items-center justify-center w-5 h-5 text-transparent border-2 border-gray-500 rounded-full">
                                @if($task->completed)
                                    <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </span>
                            <span class="ml-4 text-sm">{{ $task->task }}</span>
                        </label>
                        <div class="ml-2 flex items-center gap-2">
                            <button onclick="editTask({{ $task->id }}, '{{ addslashes($task->task) }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                </svg>
                            </button>
                            <button onclick="confirmDeleteTask({{ $task->id }})" class="inline-flex w-8 h-8 rounded-lg text-sm border border-black/10 justify-center items-center bg-gray-50 hover:bg-gray-100 shrink-0">
                                ❌
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    function addTask() {
        let task = document.getElementById('new-task').value;
        if (task.trim() === '') {
            alert('Task cannot be empty');
            return;
        }

        fetch('/tasks', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({ task: task })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        
            let tasksDiv = document.getElementById('tasks');
            let newTaskDiv = document.createElement('div');
            newTaskDiv.className = 'flex justify-between';
            newTaskDiv.id = `task-${data.task.id}`;
            newTaskDiv.innerHTML = `
                <input class="hidden" type="checkbox" id="task_${data.task.id}" onclick="completeTask(${data.task.id})">
                <label class="flex items-center w-[85%] h-10 px-2 rounded cursor-pointer hover:bg-gray-900" for="task_${data.task.id}">
                    <span class="flex items-center justify-center w-5 h-5 text-transparent border-2 border-gray-500 rounded-full"></span>
                    <span class="ml-4 text-sm">${data.task.task}</span>
                </label>
                <div class="ml-2 flex items-center gap-2">
                    <button onclick="editTask(${data.task.id}, '${addslashes(data.task.task)}')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                        </svg>
                    </button>
                    <button onclick="confirmDeleteTask(${data.task.id})" class="inline-flex w-8 h-8 rounded-lg text-sm border border-black/10 justify-center items-center bg-gray-50 hover:bg-gray-100 shrink-0">
                        ❌
                    </button>
                </div>
            `;
            tasksDiv.appendChild(newTaskDiv);
            document.getElementById('new-task').value = '';
        })
        .catch(error => alert('Error: ' + error));
    }

    function editTask(id, currentTask) {
        let task = prompt('Enter new task name', currentTask);
        if (task && task.trim() !== '') {
            fetch(`/tasks/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ task: task })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
               
                let taskElement = document.getElementById(`task-${id}`);
                taskElement.querySelector('label span.ml-4').textContent = data.task.task;
            })
            .catch(error => alert('Task already exists.'));
        } else {
            alert('Task cannot be empty');
        }
    }

    function confirmDeleteTask(id) {
        if (confirm('Are you sure you want to delete this task?')) {
            deleteTask(id);
        }
    }

    function deleteTask(id) {
        fetch(`/tasks/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken()
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
           
            let taskElement = document.getElementById(`task-${id}`);
            taskElement.parentNode.removeChild(taskElement);
        })
        .catch(error => alert('Error: ' + error));
    }

   function completeTask(id) {
    fetch(`/tasks/${id}/complete`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken() 
        },
        body: JSON.stringify({ _token: getCsrfToken() })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        alert(data.message);
        
        let checkbox = document.getElementById(`task_${id}`);
        checkbox.checked = !data.active; 
        
        let iconHTML = `
            <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
        `;
        let label = document.querySelector(`#task-${id} label span:first-of-type`);
        label.innerHTML = data.active ? '' : iconHTML;

        let taskText = document.querySelector(`#task-${id} label span:nth-of-type(2)`);
        if (data.active) {
            taskText.style.textDecoration = 'none';
            taskText.style.color = '#fff';
        } else {
            taskText.style.textDecoration = 'line-through';
            taskText.style.color = '#9CA3AF';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating task status');
    });
}


    function addslashes(str) {
        return str.replace(/\\/g, '\\\\').replace(/'/g, '\\\'').replace(/"/g, '\\"').replace(/\0/g, '\\0');
    }
</script>

</body>
</html>
