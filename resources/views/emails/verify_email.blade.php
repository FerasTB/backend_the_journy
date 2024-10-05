<!DOCTYPE html>
<html>

<head>
    <title>Email Verification</title>
</head>

<body>
    <p>Dear {{ $user->name }},</p>
    <p>Your email verification code is: <strong>{{ $verificationCode }}</strong></p>
    <p>Please enter this code in the app to verify your email address.</p>
    <p>Thank you!</p>
</body>

</html>
