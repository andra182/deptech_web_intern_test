<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    error_log("Received POST data: " . print_r($_POST, true));
    error_log("Session data before update: " . print_r($_SESSION, true));

    $admin_id = $_SESSION['admin_id'];
    $nama_depan = mysqli_real_escape_string($conn, $_POST['nama_depan']);
    $nama_belakang = mysqli_real_escape_string($conn, $_POST['nama_belakang']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $tanggal_lahir = mysqli_real_escape_string($conn, $_POST['tanggal_lahir']);
    $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);

    if (empty($nama_depan) || empty($nama_belakang) || empty($email)) {
        echo "<script>alert('Nama depan, nama belakang, dan email harus diisi!'); window.location.href='dashboard.php';</script>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Format email tidak valid!'); window.location.href='dashboard.php';</script>";
        exit;
    }

    $check_email = mysqli_query($conn, "SELECT id FROM admins WHERE email = '$email' AND id != $admin_id");
    if (mysqli_num_rows($check_email) > 0) {
        echo "<script>alert('Email sudah digunakan!'); window.location.href='dashboard.php';</script>";
        exit;
    }

    $update_fields = array();
    $update_fields[] = "nama_depan = '$nama_depan'";
    $update_fields[] = "nama_belakang = '$nama_belakang'";
    $update_fields[] = "email = '$email'";
    
    if (!empty($tanggal_lahir)) {
        $update_fields[] = "tanggal_lahir = '$tanggal_lahir'";
    }
    
    if (!empty($jenis_kelamin) && in_array($jenis_kelamin, ['Laki-laki', 'Perempuan'])) {
        $update_fields[] = "jenis_kelamin = '$jenis_kelamin'";
    }

    $query = "UPDATE admins SET " . implode(", ", $update_fields) . " WHERE id = $admin_id";

    error_log("Update query: " . $query);

    if (mysqli_query($conn, $query)) {
        $_SESSION['admin_nama_depan'] = $nama_depan;
        $_SESSION['admin_nama_belakang'] = $nama_belakang;
        $_SESSION['admin_nama'] = $nama_depan . ' ' . $nama_belakang;
        $_SESSION['admin_email'] = $email;
        if (!empty($tanggal_lahir)) $_SESSION['admin_tanggal_lahir'] = $tanggal_lahir;
        if (!empty($jenis_kelamin)) $_SESSION['admin_jenis_kelamin'] = $jenis_kelamin;

        echo "<script>alert('Profile berhasil diupdate!'); window.location.href='dashboard.php';</script>";
    } else {
        error_log("MySQL Error: " . mysqli_error($conn));
        echo "<script>alert('Gagal mengupdate profile: " . addslashes(mysqli_error($conn)) . "'); window.location.href='dashboard.php';</script>";
    }
} else {
    header("Location: dashboard.php");
    exit;
}
?>
