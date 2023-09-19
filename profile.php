<?php
$servername = "localhost";
$username = "demouser";
$password = "demopassword";
$dbname = "demo_db";

$conn = new mysqli($servername, $username, $password, $dbname, 3306, '/tmp/mysql.sock');

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

$user_id = $_GET['id'];
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    ?>
    <h2>User Profile</h2>
    <p>ID: <?php echo $row['id']; ?></p>
    <p>Name: <?php echo $row['name']; ?></p>
    <p>Email: <?php echo $row['email']; ?></p>
    <p>Password: <?php echo $row['password']; ?></p>
    <p>SIN: <?php echo $row['sin']; ?></p>
    <?php
} else {
    echo "User not found!";
}

$conn->close();
?>
