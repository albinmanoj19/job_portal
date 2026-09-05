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

    <title>JobPortal - Home</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <!-- Navigation -->

    <header class="navbar">

        <div class="logo">
            JobPortal
        </div>

        <nav class="navigation">

    <a href="index.php">Home</a>

    <a href="jobs.php">Find Jobs</a>

    <a href="dashboard.php">Dashboard</a>

    <a href="profile.php">Profile</a>

    <a href="contact.php">Contact</a>

    <a href="about.php">About</a>

    <a href="logout.php">Logout</a>

</nav>
    </header>


    <!-- Main Home Section -->

    <main>

        <section class="hero">

            <div class="hero-content">

                <h1>
                    Welcome,
                    <?php echo htmlspecialchars($user_name); ?>!
                </h1>

                <p>
                    Find your next opportunity and build your career
                    with JobPortal.
                </p>

                <div class="hero-buttons">

                    <a href="jobs.php" class="primary-btn">
                        Find Jobs
                    </a>

                    <a href="dashboard.php" class="secondary-btn">
                        My Dashboard
                    </a>

                </div>

            </div>

        </section>


        <!-- Features -->

        <section class="features">

            <h2>Explore JobPortal</h2>

            <div class="feature-container">

                <div class="feature-box">

                    <h3>🔎 Find Jobs</h3>

                    <p>
                        Browse available job opportunities
                        and find a job that matches your skills.
                    </p>

                    <a href="jobs.php">
                        Explore Jobs
                    </a>

                </div>


                <div class="feature-box">

                    <h3>📄 Applications</h3>

                    <p>
                        Keep track of the jobs you have
                        applied for.
                    </p>

                    <a href="applications.php">
                        My Applications
                    </a>

                </div>


                <div class="feature-box">

                    <h3>👤 My Profile</h3>

                    <p>
                        Manage your personal information
                        and professional profile.
                    </p>

                    <a href="profile.php">
                        View Profile
                    </a>

                </div>


                <?php if ($user_role === "employer") { ?>

                    <div class="feature-box">

                        <h3>💼 Post a Job</h3>

                        <p>
                            Create job listings and find
                            suitable candidates.
                        </p>

                        <a href="post_job.php">
                            Post a Job
                        </a>

                    </div>

                <?php } ?>

            </div>

        </section>

    </main>


    <!-- Footer -->

    <footer>

        <p>
            &copy; 2026 JobPortal. All Rights Reserved.
        </p>

    </footer>

</body>

</html>