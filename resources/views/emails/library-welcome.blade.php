<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to SeatBuddy</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6; margin: 0; padding: 0; color: #374151; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .header { background-color: #ef4444; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 1px; }
        .content { padding: 40px; line-height: 1.6; }
        .content h2 { color: #111827; margin-top: 0; }
        .welcome-badge { background-color: #fee2e2; color: #b91c1c; padding: 4px 12px; border-radius: 9999px; font-size: 14px; font-weight: 600; display: inline-block; margin-bottom: 16px; }
        .credentials-card { background-color: #f9fafb; border: 1px solid #e5e7eb; padding: 24px; margin: 25px 0; border-radius: 8px; }
        .credential-item { margin-bottom: 12px; }
        .label { font-size: 13px; color: #6b7280; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 4px; }
        .value { font-size: 18px; color: #111827; font-weight: 700; }
        .footer { background-color: #f9fafb; padding: 20px; text-align: center; font-size: 14px; color: #6b7280; border-top: 1px solid #e5e7eb; }
        .notice { background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 12px; margin-top: 20px; font-size: 14px; color: #92400e; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #ef4444; color: #ffffff !important; text-decoration: none; border-radius: 8px; font-weight: 600; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SeatBuddy</h1>
        </div>
        <div class="content">
            <span class="welcome-badge">Library Registration</span>
            <h2>Welcome to the SeatBuddy community!</h2>
            <p>Congratulations! Your library, <strong>{{ $library->name }}</strong>, is now registered with SeatBuddy. You can manage your students, seats, fees, and attendance through our Admin Panel.</p>
            
            <p>Your admin credentials are below:</p>
            
            <div class="credentials-card">
                <div class="credential-item">
                    <span class="label">User ID (Login ID)</span>
                    <span class="value">{{ $user->login_name }}</span>
                </div>
                <div class="credential-item" style="margin-top: 16px;">
                    <span class="label">Password</span>
                    <span class="value">{{ $password }}</span>
                </div>
            </div>

            <p>You can access the admin panel at:</p>
            <a href="https://seatbuddy.in/admin" class="btn">Login to Admin Panel</a>

            <div class="notice">
                For security, please change your password from the profile section once you log in.
            </div>

            <p style="margin-top: 30px;">We are excited to help you manage your library more efficiently!<br>Regards,<br><strong>SeatBuddy Team</strong></p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} SeatBuddy. All rights reserved.
        </div>
    </div>
</body>
</html>
