<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nis'])) {
    $nis = $_POST['nis'];
    
    $query = "SELECT foto FROM siswa WHERE nis = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $nis);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    $delete_query = "DELETE FROM siswa WHERE nis = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "s", $nis);
    
    if (mysqli_stmt_execute($stmt)) {
        if ($row && $row['foto'] && file_exists($row['foto'])) {
            unlink($row['foto']);
        }
        echo "success";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Error: NIS not provided";
}

mysqli_close($conn);
?>