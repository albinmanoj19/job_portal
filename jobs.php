<?php

require_once "includes/db.php";

$sql = "SELECT * FROM jobs ORDER BY created_at DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Find Jobs | JobPortal</title>

    <link rel="stylesheet" href="/job_portal/css/jobs.css?v=1">

</head>

<body>

    <!-- Navigation -->

    <header class="navbar">

        <div class="logo">
            JobPortal
        </div>

        <nav>

            <a href="index.php">Home</a>

            <a href="jobs.php" class="active">Find Jobs</a>

            <a href="dashboard.php">Dashboard</a>

            <a href="logout.php">Logout</a>

        </nav>

    </header>


    <!-- Page Header -->

    <section class="page-header">

        <h1>Find Your Dream Job</h1>

        <p>
            Explore the latest job opportunities.
        </p>

    </section>


    <!-- Jobs -->

    <main class="jobs-container">

        <?php if ($result->num_rows > 0): ?>

            <?php while ($job = $result->fetch_assoc()): ?>

                <div class="job-card">

                    <div class="job-info">

                        <span class="category">
                            <?php echo htmlspecialchars($job["category"]); ?>
                        </span>

                        <h2>
                            <?php echo htmlspecialchars($job["title"]); ?>
                        </h2>

                        <h3>
                            <?php echo htmlspecialchars($job["company"]); ?>
                        </h3>

                        <p class="location">
                            📍 <?php echo htmlspecialchars($job["location"]); ?>
                        </p>

                        <p class="salary">
                            💰 <?php echo htmlspecialchars($job["salary"]); ?>
                        </p>

                        <p class="description">
                            <?php echo htmlspecialchars($job["description"]); ?>
                        </p>

                    </div>

                    <div class="job-action">

                        <a href="job_details.php?id=<?php echo $job["id"]; ?>">
                            View Job
                        </a>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="no-jobs">

                <h2>No Jobs Available</h2>

                <p>
                    There are currently no job listings.
                </p>

            </div>

        <?php endif; ?>

    </main>


    <!-- Footer -->

    <footer>

        <p>
            &copy; 2026 JobPortal. All Rights Reserved.
        </p>

    </footer>

</body>

</html>