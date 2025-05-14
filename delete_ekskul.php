<?php
include 'koneksi.php';

$id = $_POST['id'];

$sql = "DELETE FROM ekstrakurikuler WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>