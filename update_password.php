<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = $_SESSION['admin_id'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        echo "Password baru dan konfirmasi password tidak cocok";
        exit;
    }

    $query = "SELECT password FROM admins WHERE id = $admin_id";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if (!password_verify($old_password, $user['password'])) {
        echo "Password lama tidak cocok";
        exit;
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $update_query = "UPDATE admins SET password = '$hashed_password' WHERE id = $admin_id";
    
    if (mysqli_query($conn, $update_query)) {
        header("Location: dashboard.php");
    } else {
        echo "Error updating password: " . mysqli_error($conn);
    }
}
?>
