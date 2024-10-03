<?php
// Database Configuration
$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'login_system';

// Create a database connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle registration
    if (isset($_POST['signup'])) {
        $full_name = $_POST["full_name"];
        $email = $_POST["email"];
        $password = $_POST["password"];

        // Hash the password before saving to the database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert data into the database
        $sql = "INSERT INTO users (full_name, email, password) VALUES ('$full_name', '$email', '$hashed_password')";
        if ($conn->query($sql) === TRUE) {
            echo "Signup successful!";
            header("Location: signin.php");
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Handle login
    if (isset($_POST['login'])) {
        $email = $_POST["email"];
        $password = $_POST["password"];

        // Retrieve user data from the database
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user_data = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user_data["password"])) {
                // Start session and store user info
                session_start();
                $_SESSION["logged_in"] = true;
                $_SESSION["user_id"] = $user_data["id"];
                $_SESSION["full_name"] = $user_data["full_name"];
                header("Location: dashboard.php");
                exit;
            } else {
                echo "Invalid password!";
            }
        } else {
            echo "User not found!";
        }
    }
}

$conn->close();
?>

