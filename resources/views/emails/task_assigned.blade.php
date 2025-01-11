<!DOCTYPE html>
<html>
<head>
    <title>New Task Assigned</title>
</head>
<body>
    <h1>New Task Assigned</h1>
    <p>Hello {{ $user->name }},</p>
    <p>You have been assigned a new task: <strong>{{ $task->name }}</strong></p>
    <p>Description: {{ $task->description }}</p>
    <p>Thank you!</p>
</body>
</html>