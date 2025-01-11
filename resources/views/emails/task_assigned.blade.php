<!DOCTYPE html>
<html>
<head>
    <title>{{ $isNew ? 'New Task Assigned' : 'Task Updated' }}</title>
</head>
<body>
    <h1>{{ $isNew ? 'New Task Assigned' : 'Task Updated' }}</h1>
    <p>Hello {{ $user->name }},</p>
    <p>
    @if($isNew)
      You have been assigned a new task: <strong>{{ $task->name }}</strong>
      @else
      Task: <strong>{{ $task->name }}</strong> has been updated
    @endif
    </p>
    <p>Description: {{ $task->description }}</p>
    <p>Thank you!</p>
</body>
</html>