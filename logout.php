<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy session
session_destroy();

// Clear session cookies
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging out...</title>
</head>
<body>
    <script>
        // Clear sessionStorage
        sessionStorage.clear();
        // Redirect to login page
        window.location.href = 'login.php';
    </script>
</body>
</html>

