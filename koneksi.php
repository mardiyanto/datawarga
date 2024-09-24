<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "dbwarga"; // Ganti dengan nama database Anda

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
