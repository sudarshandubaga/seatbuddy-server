<!DOCTYPE html>
<html>

<head>
    <title>Welcome to Our Library</title>
</head>

<body>
    <h2>Welcome to {{ config('app.name') }}, {{ $user->name }}!</h2>
    <p>Your library student account has been successfully created. Here are your login credentials:</p>

    <div style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 20px; border-radius: 5px; margin: 20px 0;">
        <p><strong>Username:</strong> {{ $user->login_name }}</p>
        <p><strong>Password:</strong> {{ $password }}</p>
    </div>

    <p>You can use these credentials to log in to the SeatBuddy mobile app.</p>

    <p>If you have any questions, feel free to contact us.</p>

    <br>
    <p>Best Regards,</p>
    <p>Library Team</p>
</body>

</html>