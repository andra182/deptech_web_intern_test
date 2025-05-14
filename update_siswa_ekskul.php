<?php
include 'koneksi.php';

$id = $_POST['id'];
$siswa_id = $_POST['siswa_id'];
$ekstrakurikuler_id = $_POST['ekstrakurikuler_id'];
$tahun_mulai = $_POST['tahun_mulai'];

$check_sql = "SELECT id FROM siswa_ekstrakurikuler WHERE siswa_id = ? AND ekstrakurikuler_id = ? AND id != ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("iii", $siswa_id, $ekstrakurikuler_id, $id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo "Error: Siswa sudah terdaftar di ekstrakurikuler ini";
    exit;
}

$sql = "UPDATE siswa_ekstrakurikuler SET siswa_id=?, ekstrakurikuler_id=?, tahun_mulai=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisi", $siswa_id, $ekstrakurikuler_id, $tahun_mulai, $id);

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