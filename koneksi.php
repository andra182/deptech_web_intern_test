<?php
$conn = mysqli_connect("localhost", "root", "", "pkl_project");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
