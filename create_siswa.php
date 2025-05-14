<?php
include 'koneksi.php';

$nis = $_POST['nis'];
$firstName = $_POST['nama_depan'];
$lastName = $_POST['nama_belakang'];
$gender = $_POST['jenis_kelamin'];
$address = $_POST['alamat'];
$phone = $_POST['nomor_hp'];

$targetDir = "uploads/";
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$targetFile = "";
if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {
    $photoName = basename($_FILES["foto"]["name"]);
    $targetFile = $targetDir . uniqid() . "_" . $photoName;
    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile)) {
    } else {
        echo "Sorry, there was an error uploading your file.";
        exit;
    }
}

$sql = "INSERT INTO siswa (nis, nama_depan, nama_belakang, jenis_kelamin, alamat, nomor_hp, foto)
        VALUES ('$nis', '$firstName', '$lastName', '$gender', '$address', '$phone', '$targetFile')";

if ($conn->query($sql) === TRUE) {
    header("Location: dashboard.php");
    exit;
} else {
    echo "Gagal menyimpan: " . $conn->error;
}

$conn->close();
?>
