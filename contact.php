<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contact Us | JobPortal</title>

    <!-- CONTACT CSS -->
    <link rel="stylesheet" type="text/css" href="css/contact.css?v=2">

</head>

<body>

<header class="site-header">

    <div class="header-container">

        <a href="index.php" class="logo">
            JobPortal
        </a>

        <nav class="navigation">

            <a href="index.php">Home</a>

            <a href="jobs.php">Find Jobs</a>

            <?php if (isset($_SESSION["user_id"])): ?>

                <a href="applications.php">My Applications</a>

                <a href="profile.php">Profile</a>

                <a href="logout.php">Logout</a>

            <?php else: ?>

                <a href="login.php">Login</a>

                <a href="register.php">Register</a>

            <?php endif; ?>

        </nav>

    </div>

</header>


<section class="contact-hero">

    <div class="hero-content">

        <span class="hero-label">
            CONTACT US
        </span>

        <h1>
            How can we help?
        </h1>

        <p>
            Have a question about JobPortal or need help with your
            job search? Our team is here to assist you.
        </p>

    </div>

</section>


<main class="contact-container">

    <div class="contact-grid">


        <section class="contact-information">

            <span class="section-title">
                GET IN TOUCH
            </span>

            <h2>
                We'd love to hear from you.
            </h2>

            <p class="description">
                Whether you need help with your account, have a question
                about applying for a job, or want to provide feedback,
                you can contact our team.
            </p>


            <div class="contact-item">

                <div class="contact-icon">
                    @
                </div>

                <div>
                    <h3>Email</h3>
                    <p>support@jobportal.com</p>
                </div>

            </div>


            <div class="contact-item">

                <div class="contact-icon">
                    ☎
                </div>

                <div>
                    <h3>Phone</h3>
                    <p>+91 1800 123 4567</p>
                </div>

            </div>


            <div class="contact-item">

                <div class="contact-icon">
                    ●
                </div>

                <div>
                    <h3>Office</h3>
                    <p>
                        JobPortal Support Center<br>
                        India
                    </p>
                </div>

            </div>


            <div class="support-box">

                <h3>
                    Support Hours
                </h3>

                <p>
                    Monday – Friday
                </p>

                <p>
                    9:00 AM – 6:00 PM
                </p>

            </div>

        </section>


        <section class="contact-card">

            <div class="form-header">

                <span>
                    SEND US A MESSAGE
                </span>

                <h2>
                    Contact our team
                </h2>

                <p>
                    Fill out the form below and we'll get back to you.
                </p>

            </div>


            <form action="#" method="POST">

                <div class="form-row">

                    <div class="form-group">

                        <label for="name">
                            Full Name
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="Enter your full name"
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
                            required
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label for="subject">
                        Subject
                    </label>

                    <input
                        type="text"
                        id="subject"
                        name="subject"
                        placeholder="What is your message about?"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="message">
                        Message
                    </label>

                    <textarea
                        id="message"
                        name="message"
                        rows="7"
                        placeholder="Write your message here..."
                        required
                    ></textarea>

                </div>


                <button
                    type="submit"
                    class="submit-button"
                >
                    Send Message
                </button>

            </form>

        </section>

    </div>

</main>


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

            <a href="index.php">
                Home
            </a>

            <a href="jobs.php">
                Find Jobs
            </a>

            <a href="contact.php">
                Contact
            </a>

        </div>

    </div>


    <div class="footer-bottom">

        © <?php echo date("Y"); ?> JobPortal.
        All rights reserved.

    </div>

</footer>

</body>
</html>