<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>About Us | JobPortal</title>

    <link rel="stylesheet" href="css/about.css?v=1">

</head>

<body>

<!-- HEADER -->

<header class="site-header">

    <div class="header-container">

        <a href="index.php" class="logo">
            JobPortal
        </a>

        <nav class="navigation">

            <a href="index.php">Home</a>

            <a href="jobs.php">Find Jobs</a>

            <a href="dashboard.php">Dashboard</a>

            <a href="profile.php">Profile</a>

            <a href="contact.php">Contact</a>

            <a href="about.php" class="active">About</a>

            <a href="logout.php">Logout</a>

        </nav>

    </div>

</header>


<!-- HERO -->

<section class="about-hero">

    <div class="hero-content">

        <span class="hero-label">
            ABOUT JOBPORTAL
        </span>

        <h1>
            Connecting people with the right opportunities.
        </h1>

        <p>
            JobPortal is a modern platform designed to make finding
            and applying for jobs simple, professional and accessible.
        </p>

    </div>

</section>


<!-- ABOUT -->

<main>

    <section class="about-section">

        <div class="about-container">

            <div class="about-text">

                <span class="section-label">
                    WHO WE ARE
                </span>

                <h2>
                    Your career journey starts here.
                </h2>

                <p>
                    JobPortal brings job seekers and employers together
                    through an easy-to-use online platform.
                </p>

                <p>
                    Job seekers can discover opportunities, view job
                    details, submit applications and track their
                    application status from one place.
                </p>

                <p>
                    Our goal is to provide a simple and organized
                    experience for people searching for their next
                    career opportunity.
                </p>

            </div>


            <div class="about-card">

                <div class="card-icon">
                    ✓
                </div>

                <h3>
                    Simple Job Search
                </h3>

                <p>
                    Find relevant opportunities and explore job
                    information before applying.
                </p>

            </div>

        </div>

    </section>


    <!-- FEATURES -->

    <section class="features-section">

        <div class="features-container">

            <div class="section-heading">

                <span class="section-label">
                    OUR PLATFORM
                </span>

                <h2>
                    Everything you need for your job search
                </h2>

                <p>
                    JobPortal provides useful tools to help make
                    your job search more organized.
                </p>

            </div>


            <div class="features-grid">

                <div class="feature-card">

                    <div class="feature-icon">
                        🔎
                    </div>

                    <h3>
                        Find Jobs
                    </h3>

                    <p>
                        Search and explore available job opportunities
                        based on your interests and skills.
                    </p>

                </div>


                <div class="feature-card">

                    <div class="feature-icon">
                        📄
                    </div>

                    <h3>
                        Apply Online
                    </h3>

                    <p>
                        Complete your application and submit your
                        resume directly through the platform.
                    </p>

                </div>


                <div class="feature-card">

                    <div class="feature-icon">
                        📊
                    </div>

                    <h3>
                        Track Applications
                    </h3>

                    <p>
                        Keep track of the jobs you have applied for
                        and check your application status.
                    </p>

                </div>

            </div>

        </div>

    </section>


    <!-- CALL TO ACTION -->

    <section class="cta-section">

        <div class="cta-container">

            <h2>
                Ready to find your next opportunity?
            </h2>

            <p>
                Explore available jobs and take the next step
                in your career.
            </p>

            <a href="jobs.php" class="cta-button">
                Explore Jobs
            </a>

        </div>

    </section>

</main>


<!-- FOOTER -->

<footer class="site-footer">

    <div class="footer-container">

        <div class="footer-brand">

            <h3>
                JobPortal
            </h3>

            <p>
                Connecting job seekers with meaningful
                career opportunities.
            </p>

        </div>


        <div class="footer-links">

            <a href="index.php">Home</a>

            <a href="jobs.php">Find Jobs</a>

            <a href="contact.php">Contact</a>

            <a href="about.php">About</a>

        </div>

    </div>


    <div class="footer-bottom">

        © <?php echo date("Y"); ?> JobPortal.
        All rights reserved.

    </div>

</footer>

</body>

</html>