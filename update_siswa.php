<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama_depan = $_POST['nama_depan'];
    $nama_belakang = $_POST['nama_belakang'];
    $nis = $_POST['nis'];
    $nomor_hp = $_POST['nomor_hp'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $alamat = $_POST['alamat'];
    
    $update_query = "UPDATE siswa SET 
                    nama_depan = ?,
                    nama_belakang = ?,
                    nis = ?,
                    nomor_hp = ?,
                    jenis_kelamin = ?,
                    alamat = ?";
    
    $params = [$nama_depan, $nama_belakang, $nis, $nomor_hp, $jenis_kelamin, $alamat];
    $types = "ssssss";
    
    if (!empty($_FILES['foto']['name'])) {
        $foto = $_FILES['foto'];
        $foto_name = uniqid() . '_' . $foto['name'];
        $foto_path = 'uploads/' . $foto_name;
        
        if (move_uploaded_file($foto['tmp_name'], $foto_path)) {
            $update_query .= ", foto = ?";
            $params[] = $foto_path;
            $types .= "s";
        }
    }
    
    $update_query .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";
    
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>