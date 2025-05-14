<?php
session_start();
include 'koneksi.php';

$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = $_POST['password'];

$query = mysqli_query($conn, "SELECT * FROM admins WHERE email='$email'");
$data = mysqli_fetch_assoc($query);

if ($data && password_verify($password, $data['password'])) {
    $_SESSION['admin_id'] = $data['id'];
    $_SESSION['admin_nama_depan'] = $data['nama_depan'];
    $_SESSION['admin_nama_belakang'] = $data['nama_belakang'];
    $_SESSION['admin_nama'] = $data['nama_depan'] . ' ' . $data['nama_belakang'];
    $_SESSION['admin_email'] = $data['email'];
    $_SESSION['admin_tanggal_lahir'] = $data['tanggal_lahir'];
    $_SESSION['admin_jenis_kelamin'] = $data['jenis_kelamin'];
    
    header("Location: dashboard.php");
    exit();
} else {
    header("Location: login.php?error=" . urlencode("Login gagal. Cek email atau password."));
    exit();
}
?>
