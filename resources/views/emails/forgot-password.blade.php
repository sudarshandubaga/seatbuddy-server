<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - SeatBuddy</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            color: #374151;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .header {
            background-color: #4f46e5;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            letter-spacing: 1px;
        }

        .content {
            padding: 40px;
            line-height: 1.6;
        }

        .content h2 {
            color: #111827;
            margin-top: 0;
        }

        .password-box {
            background-color: #f9fafb;
            border: 2px dashed #e5e7eb;
            padding: 20px;
            text-align: center;
            margin: 25px 0;
            border-radius: 8px;
        }

        .password-box code {
            font-size: 28px;
            font-weight: bold;
            color: #4f46e5;
            letter-spacing: 2px;
        }

        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4f46e5;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>SeatBuddy</h1>
        </div>
        <div class="content">
            <h2>Hello!</h2>
            <p>We received a request to reset the password for your SeatBuddy account. Use the temporary password below
                to log in:</p>

            <div class="password-box">
                <code>{{ $password }}</code>
            </div>

            <p>For security reasons, we recommend changing this password immediately after logging in from the "Change
                Password" section in your profile.</p>

            <p>If you did not request a password reset, no further action is required.</p>

            <p>Regards,<br><strong>SeatBuddy Team</strong></p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} SeatBuddy. All rights reserved.
        </div>
    </div>
</body>

</html>