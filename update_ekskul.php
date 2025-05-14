<?php
include 'koneksi.php';

$id = $_POST['id'];
$nama = $_POST['nama'];
$penanggung_jawab = $_POST['penanggung_jawab'];
$status = $_POST['status'];

$sql = "UPDATE ekstrakurikuler SET nama=?, penanggung_jawab=?, status=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $nama, $penanggung_jawab, $status, $id);

if ($stmt->execute()) {
    header("Location: dashboard.php");
    exit;
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>