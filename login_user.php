<?php
$servername = "localhost";
$username = "demouser";
$password = "demopassword";
$dbname = "demo_db";

$conn = new mysqli($servername, $username, $password, $dbname, 3306, '/tmp/mysql.sock');

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

$user = $_POST['email'];
$pass = $_POST['password'];

// Vulnerable to SQL Injection
// Comment these two lines and uncomment the next two lines to prevent the SQL Injection
$sql = "SELECT * FROM users WHERE email='$user' AND password='$pass'";
$result = $conn->query($sql);

// Prevention of SQL Injection
// $stmt = $pdo->prepare("SELECT * FROM users WHERE username=? AND password=?");
// $result->execute([$username, $password]);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    header("Location: profile.php?id=" . $row['id']);
    exit;
} else {
    echo "Login failed!";
}

$conn->close();
?>