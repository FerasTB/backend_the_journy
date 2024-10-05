<!DOCTYPE html>
<html>

<head>
    <title>Email Verification</title>
</head>

<body>
    <p>Hello {{ $user->name }},</p>

    <p>Please verify your email by clicking the link below:</p>

    <p><a href="{{ $verification_link }}">Verify Email</a></p>

    <p>Thanks,<br>Your App Team</p>
</body>

</html>
