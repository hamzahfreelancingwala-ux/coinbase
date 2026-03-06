<?php
include 'db.php';

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Use JS to redirect to the home page (index.php)
echo "<script>
    alert('Logged out successfully');
    window.location.href = 'index.php';
</script>";
exit;
?>
