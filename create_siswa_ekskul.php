<?php
include 'koneksi.php';

$siswa_id = $_POST['siswa_id'];
$ekstrakurikuler_id = $_POST['ekstrakurikuler_id'];
$tahun_mulai = $_POST['tahun_mulai'];

$check_sql = "SELECT id FROM siswa_ekstrakurikuler WHERE siswa_id = ? AND ekstrakurikuler_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $siswa_id, $ekstrakurikuler_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo "Error: Siswa sudah terdaftar di ekstrakurikuler ini";
    exit;
}

$sql = "INSERT INTO siswa_ekstrakurikuler (siswa_id, ekstrakurikuler_id, tahun_mulai) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $siswa_id, $ekstrakurikuler_id, $tahun_mulai);

if ($stmt->execute()) {
    header("Location: dashboard.php");
    exit;
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$check_stmt->close();
$conn->close();
?>