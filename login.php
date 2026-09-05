<?php

session_start();

require_once "includes/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {

        $message = "Please enter your email and password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email, password, role
             FROM users
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_email"] = $user["email"];
                $_SESSION["user_role"] = $user["role"];

                header("Location: index.php");
                exit;

            } else {

                $message = "Incorrect email or password.";
            }

        } else {

            $message = "Incorrect email or password.";
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | JobPortal</title>

    <link rel="stylesheet" href="/job_portal/css/login.css?v=1">

</head>

<body>

    <div class="login-container">

        <div class="logo">
            JobPortal
        </div>

        <h1>Welcome Back</h1>

        <p class="subtitle">
            Login to your JobPortal account.
        </p>

        <?php if ($message !== ""): ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="login.php">

            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >

            </div>

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>

            <button type="submit">
                Login
            </button>

        </form>

        <p class="register-link">
            Don't have an account?
            <a href="register.php">Create Account</a>
        </p>

        <a href="index.php" class="home-link">
            ← Back to Home
        </a>

    </div>

</body>

</html>