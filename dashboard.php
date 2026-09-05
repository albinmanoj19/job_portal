<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_name = $_SESSION["user_name"];
$user_role = $_SESSION["user_role"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | JobPortal</title>

    <link rel="stylesheet" href="/job_portal/css/dashboard.css?v=1">

</head>

<body>

    <!-- Navigation -->

    <header class="navbar">

        <div class="logo">
            JobPortal
        </div>

        <nav>

            <a href="index.php">Home</a>

            <a href="jobs.php">Find Jobs</a>

            <a href="logout.php">Logout</a>

        </nav>

    </header>


    <!-- Dashboard -->

    <main class="dashboard">

        <div class="welcome">

            <h1>
                Welcome, <?php echo htmlspecialchars($user_name); ?>!
            </h1>

            <p>
                Welcome to your JobPortal dashboard.
            </p>

            <span class="role">
                <?php echo htmlspecialchars(ucfirst($user_role)); ?>
            </span>

        </div>


        <!-- Dashboard Cards -->

        <div class="cards">

            <div class="card">

                <div class="card-icon">
                    🔎
                </div>

                <h2>Find Jobs</h2>

                <p>
                    Search for jobs that match your
                    skills and interests.
                </p>

                <a href="jobs.php">
                    Find Jobs
                </a>

            </div>


            <div class="card">

                <div class="card-icon">
                    📄
                </div>

                <h2>My Applications</h2>

                <p>
                    View and manage your submitted
                    job applications.
                </p>

                <a href="applications.php">
                    View Applications
                </a>

            </div>


            <?php if ($user_role === "employer") { ?>

                <div class="card">

                    <div class="card-icon">
                        💼
                    </div>

                    <h2>Post a Job</h2>

                    <p>
                        Create a new job listing and
                        find suitable candidates.
                    </p>

                    <a href="post_job.php">
                        Post Job
                    </a>

                </div>

            <?php } ?>


            <div class="card">

                <div class="card-icon">
                    👤
                </div>

                <h2>My Profile</h2>

                <p>
                    View and update your personal
                    profile information.
                </p>

                <a href="profile.php">
                    View Profile
                </a>

            </div>

        </div>

    </main>


    <!-- Footer -->

    <footer>

        <p>
            &copy; 2026 JobPortal. All Rights Reserved.
        </p>

    </footer>

</body>

</html>