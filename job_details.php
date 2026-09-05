<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "includes/db.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: jobs.php");
    exit;
}

$job_id = (int) $_GET["id"];

$stmt = $conn->prepare(
    "SELECT * FROM jobs WHERE id = ?"
);

$stmt->bind_param("i", $job_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: jobs.php");
    exit;
}

$job = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($job["title"]); ?> | JobPortal
    </title>

    <link rel="stylesheet" href="css/job_details.css">

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

            <a href="dashboard.php">Dashboard</a>

            <a href="profile.php">Profile</a>

            <a href="logout.php">Logout</a>

        </nav>

    </header>


    <!-- Job Details -->

    <main class="details-container">

        <a href="jobs.php" class="back-link">
            ← Back to Jobs
        </a>

        <div class="job-details">

            <div class="job-header">

                <span class="category">
                    <?php echo htmlspecialchars($job["category"]); ?>
                </span>

                <h1>
                    <?php echo htmlspecialchars($job["title"]); ?>
                </h1>

                <h2>
                    <?php echo htmlspecialchars($job["company"]); ?>
                </h2>

            </div>


            <div class="job-meta">

                <div>
                    <strong>📍 Location</strong>
                    <p>
                        <?php echo htmlspecialchars($job["location"]); ?>
                    </p>
                </div>

                <div>
                    <strong>💰 Salary</strong>
                    <p>
                        <?php echo htmlspecialchars($job["salary"]); ?>
                    </p>
                </div>

                <div>
                    <strong>📅 Posted</strong>
                    <p>
                        <?php echo date(
                            "d M Y",
                            strtotime($job["created_at"])
                        ); ?>
                    </p>
                </div>

            </div>


            <section class="description-section">

                <h2>Job Description</h2>

                <p>
                    <?php echo nl2br(
                        htmlspecialchars($job["description"])
                    ); ?>
                </p>

            </section>


            <div class="apply-section">

                <a
                    href="apply.php?id=<?php echo $job["id"]; ?>"
                    class="apply-btn"
                >
                    Apply for this Job
                </a>

            </div>

        </div>

    </main>


    <footer>

        <p>
            &copy; 2026 JobPortal. All Rights Reserved.
        </p>

    </footer>

</body>

</html>