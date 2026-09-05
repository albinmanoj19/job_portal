<?php
require_once "includes/db.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $role = $_POST["role"] ?? "jobseeker";

    if ($name === "" || $email === "" || $password === "") {

        $message = "Please fill in all fields.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";
        $message_type = "error";

    } else {

        // Check if email already exists
        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "This email is already registered.";
            $message_type = "error";

        } else {

            // Hash password
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Insert user
            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password, role)
                 VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssss",
                $name,
                $email,
                $hashed_password,
                $role
            );

            if ($stmt->execute()) {

                $message = "Account created successfully!";
                $message_type = "success";

            } else {

                $message = "Something went wrong. Please try again.";
                $message_type = "error";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register | JobPortal</title>

    <!-- Separate CSS file -->
    <link rel="stylesheet" href="/job_portal/css/register.css?v=1">

</head>

<body>

    <div class="register-container">

        <div class="logo">
            JobPortal
        </div>

        <h1>Create Your Account</h1>

        <p class="subtitle">
            Join JobPortal and find your next opportunity.
        </p>

        <?php if ($message !== ""): ?>

            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="register.php">

            <div class="form-group">

                <label for="name">
                    Full Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter your full name"
                    value="<?php echo htmlspecialchars($_POST["name"] ?? ""); ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    value="<?php echo htmlspecialchars($_POST["email"] ?? ""); ?>"
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
                    placeholder="Enter at least 6 characters"
                    required
                >

            </div>

            <div class="form-group">

                <label for="role">
                    Register As
                </label>

                <select id="role" name="role">

                    <option value="jobseeker">
                        Job Seeker
                    </option>

                    <option value="employer">
                        Employer
                    </option>

                </select>

            </div>

            <button type="submit">
                Create Account
            </button>

        </form>

        <p class="login-link">
            Already have an account?
            <a href="login.php">Login</a>
        </p>

        <a href="index.php" class="home-link">
            ← Back to Home
        </a>

    </div>

</body>

</html>