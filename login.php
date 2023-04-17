<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form action="login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>

    <?php
    // Display error messages if any
    if (isset($error)) {
        echo "<p>Error: $error</p>";
    }
    ?>

    <?php
    session_start();

    // Replace these variables with your own database credentials
    $host = 'localhost:3307';
    $username = 'root';
    $password = '';
    $dbname = 'shopping_cart';

    // Create a database connection
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Validate form data
        // ... (perform validation, such as checking for empty fields, etc.)

        // Compare credentials with database
        $query = "SELECT * FROM users WHERE username = ? AND password = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // If credentials are valid, set session variable and redirect to index.php
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            
            header("Location: index.php");
            exit();
        } else {
            // If credentials are invalid, display an error message
            $error = "Invalid username or password";
        }

        $stmt->close();
    }

    // Insert login value to users table if it doesn't exist already
    if (isset($_POST['username'])) {
        $username = $_POST['username'];
        $query = "INSERT INTO users (username) SELECT ? FROM dual WHERE NOT EXISTS (SELECT * FROM users WHERE username = ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $stmt->close();
    }

    $conn->close();
    ?>
</body>
</html>
