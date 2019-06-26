<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Reset password</title>
    </head>
    <body>
        <p>Hi, <strong>{{ ucwords($user->name) }}</strong>! Welcome to KOL Platform. Thank you for sign-up with us.</p>
        <p>
            Your reset password link:
            <a href="{{ getFrontendBaseUri() . '/reset-password/' . $token . "?email=$email" }}">
                {{ getFrontendBaseUri() . '/reset-password/' . $token . "?email=$email" }}
            </a>
        </p>
    </body>
</html>
