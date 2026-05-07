<!DOCTYPE html>
<html>
<head>
    <title>Promotion Notification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height:1.6;">

    <h2>Congratulations, {{ $user->name }} 🎉</h2>

    <p>
        We are pleased to inform you that you have been promoted.
    </p>

    <p>
        <strong>New Role:</strong> {{ ucfirst($newRole) }}
    </p>

    <p>
        Your access permissions and dashboard have been updated accordingly.
    </p>

    <p>
        Please log in to your account to view your updated role and responsibilities.
    </p>

    <br>

    <p>Best regards,</p>
    <p><strong>HR Department</strong></p>

</body>
</html>