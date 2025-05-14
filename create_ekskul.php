<?php
include 'koneksi.php';

$nama = $_POST['nama'];
$penanggung_jawab = $_POST['penanggung_jawab'];
$status = $_POST['status'];

$sql = "INSERT INTO ekstrakurikuler (nama, penanggung_jawab, status) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $nama, $penanggung_jawab, $status);

if ($stmt->execute()) {
    header("Location: dashboard.php");
    exit;
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>