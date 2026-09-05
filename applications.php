<?php
session_start();

require_once "includes/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];


/* =========================
   GET APPLICATIONS
========================= */

$stmt = $conn->prepare("
    SELECT
        applications.id,
        applications.status,
        applications.resume,
        jobs.title,
        jobs.company,
        jobs.location
    FROM applications
    INNER JOIN jobs
        ON applications.job_id = jobs.id
    WHERE applications.user_id = ?
    ORDER BY applications.id DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Applications | JobPortal</title>

    <link
        rel="stylesheet"
        href="css/applications.css?v=2"
    >

</head>

<body>


<!-- =========================
     HEADER
========================= -->

<header class="site-header">

    <div class="header-inner">

        <a
            href="index.php"
            class="brand"
        >
            JobPortal
        </a>


        <nav class="main-nav">

            <a href="index.php">
                Home
            </a>

            <a href="jobs.php">
                Find Jobs
            </a>

            <a
                href="applications.php"
                class="active"
            >
                My Applications
            </a>

            <a href="profile.php">
                Profile
            </a>

        </nav>


        <a
            href="logout.php"
            class="logout-link"
        >
            Logout
        </a>

    </div>

</header>


<!-- =========================
     MAIN
========================= -->

<main class="page-container">


    <div class="page-heading">

        <span class="page-label">
            CANDIDATE DASHBOARD
        </span>

        <h1>
            My Applications
        </h1>

        <p>
            Track the jobs you have applied for
            and check your application status.
        </p>

    </div>


    <?php if ($result->num_rows > 0): ?>


        <div class="applications-list">


            <?php while ($application = $result->fetch_assoc()): ?>


                <div class="application-card">


                    <!-- JOB INFORMATION -->

                    <div class="application-main">


                        <div class="company-logo">

                            <?php

                            $company_name =
                                $application["company"];

                            echo strtoupper(
                                substr(
                                    $company_name,
                                    0,
                                    1
                                )
                            );

                            ?>

                        </div>


                        <div class="job-info">

                            <h2>

                                <?php

                                echo htmlspecialchars(
                                    $application["title"]
                                );

                                ?>

                            </h2>


                            <p class="company">

                                <?php

                                echo htmlspecialchars(
                                    $application["company"]
                                );

                                ?>

                            </p>


                            <p class="location">

                                📍

                                <?php

                                echo htmlspecialchars(
                                    $application["location"]
                                );

                                ?>

                            </p>

                        </div>

                    </div>


                    <!-- APPLICATION DETAILS -->

                    <div class="application-details">


                        <!-- STATUS -->

                        <div class="status-section">

                            <span class="detail-label">
                                APPLICATION STATUS
                            </span>


                            <?php

                            $status =
                                strtolower(
                                    trim(
                                        $application["status"]
                                    )
                                );


                            $status_class = "pending";


                            if (
                                $status === "approved" ||
                                $status === "accepted"
                            ) {

                                $status_class = "approved";

                            } elseif (
                                $status === "rejected"
                            ) {

                                $status_class = "rejected";

                            }

                            ?>


                            <span
                                class="status <?php echo $status_class; ?>"
                            >

                                <?php

                                echo htmlspecialchars(
                                    $application["status"]
                                );

                                ?>

                            </span>

                        </div>


                        <!-- APPLICATION ID -->

                        <div class="date-section">

                            <span class="detail-label">
                                APPLICATION ID
                            </span>

                            <strong>

                                #<?php

                                echo htmlspecialchars(
                                    $application["id"]
                                );

                                ?>

                            </strong>

                        </div>


                        <!-- RESUME -->

                        <div class="resume-section">

                            <span class="detail-label">
                                RESUME
                            </span>


                            <?php if (
                                !empty(
                                    $application["resume"]
                                )
                            ): ?>


                                <a
                                    href="<?php

                                    echo htmlspecialchars(
                                        $application["resume"]
                                    );

                                    ?>"
                                    target="_blank"
                                    class="resume-link"
                                >
                                    View Resume
                                </a>


                            <?php else: ?>


                                <span>
                                    Not available
                                </span>


                            <?php endif; ?>

                        </div>


                    </div>

                </div>


            <?php endwhile; ?>


        </div>


    <?php else: ?>


        <!-- NO APPLICATIONS -->

        <div class="empty-state">


            <div class="empty-icon">
                📄
            </div>


            <h2>
                No applications yet
            </h2>


            <p>
                You haven't applied for any jobs yet.
                Start exploring available opportunities.
            </p>


            <a
                href="jobs.php"
                class="browse-button"
            >
                Browse Jobs
            </a>


        </div>


    <?php endif; ?>


</main>


<!-- =========================
     FOOTER
========================= -->

<footer class="site-footer">

    <div class="footer-inner">


        <div>

            <h3>
                JobPortal
            </h3>

            <p>
                Connecting talented people with
                great opportunities.
            </p>

        </div>


        <div class="footer-links">

            <a href="index.php">
                Home
            </a>

            <a href="jobs.php">
                Find Jobs
            </a>

            <a href="applications.php">
                My Applications
            </a>

        </div>


    </div>


    <div class="footer-bottom">

        © 2026 JobPortal. All Rights Reserved.

    </div>

</footer>


</body>

</html>


<?php

$stmt->close();

$conn->close();

?>