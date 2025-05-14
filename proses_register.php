<?php
include 'koneksi.php';

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$birthDate = $_POST['birthDate'];
$gender = $_POST['gender'];
$password = $_POST['password'];

if (empty($firstName) || empty($lastName) || empty($email) || empty($birthDate) || empty($gender) || empty($password)) {
    header("Location: register.php?error=Semua field wajib diisi!");
    exit;
}

$cek = mysqli_query($conn, "SELECT id FROM admins WHERE email='$email'");
if (mysqli_num_rows($cek) > 0) {
    header("Location: register.php?error=Email sudah terdaftar!");
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$query = "INSERT INTO admins (nama_depan, nama_belakang, email, tanggal_lahir, jenis_kelamin, password)
          VALUES ('$firstName', '$lastName', '$email', '$birthDate', '$gender', '$hashedPassword')";

if (mysqli_query($conn, $query)) {
    header("Location: login.php");
    exit;
} else {
    $errorMsg = urlencode("Gagal menyimpan data: " . mysqli_error($conn));
    header("Location: register.php?error=$errorMsg");
    exit;
}
?>
