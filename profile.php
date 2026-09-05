<?php
session_start();

require_once "includes/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];


/* Get user information */

$stmt = $conn->prepare("
    SELECT id, name, email, profile_picture
    FROM users
    WHERE id = ?
");

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "User not found.";
    exit();
}

$user = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Profile | JobPortal</title>

    <link
        rel="stylesheet"
        href="css/profile.css?v=4"
    >

</head>

<body>


<!-- =================================
     HEADER
================================= -->

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

            <a href="applications.php">
                My Applications
            </a>

            <a
                href="profile.php"
                class="active"
            >
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


<!-- =================================
     MAIN
================================= -->

<main class="profile-container">


    <div class="page-heading">

        <span class="page-label">
            CANDIDATE PROFILE
        </span>

        <h1>
            My Profile
        </h1>

        <p>
            View and manage your JobPortal account information.
        </p>

    </div>


    <div class="profile-layout">


        <!-- =================================
             SIDEBAR
        ================================= -->

        <aside class="profile-sidebar">


            <div class="profile-avatar">

                <?php if (
                    !empty($user["profile_picture"]) &&
                    file_exists(
                        "uploads/profile/" .
                        $user["profile_picture"]
                    )
                ): ?>

                    <img
                        src="<?php
                        echo htmlspecialchars(
                            "uploads/profile/" .
                            $user["profile_picture"]
                        );
                        ?>"
                        alt="Profile Picture"
                    >

                <?php else: ?>

                    <?php

                    echo htmlspecialchars(
                        strtoupper(
                            substr(
                                $user["name"],
                                0,
                                1
                            )
                        )
                    );

                    ?>

                <?php endif; ?>

            </div>


            <h2>

                <?php

                echo htmlspecialchars(
                    $user["name"]
                );

                ?>

            </h2>


            <p class="profile-email">

                <?php

                echo htmlspecialchars(
                    $user["email"]
                );

                ?>

            </p>


            <div class="profile-role">
                Job Seeker
            </div>


        </aside>


        <!-- =================================
             PROFILE CARD
        ================================= -->

        <section class="profile-card">


            <div class="card-header">

                <div>

                    <h2>
                        Personal Information
                    </h2>

                    <p>
                        Your registered account information
                    </p>

                </div>


                <a
                    href="edit-profile.php"
                    class="edit-button"
                >
                    Edit Profile
                </a>

            </div>


            <div class="information-grid">


                <div class="information-item">

                    <span class="information-label">
                        FULL NAME
                    </span>

                    <strong>

                        <?php

                        echo htmlspecialchars(
                            $user["name"]
                        );

                        ?>

                    </strong>

                </div>


                <div class="information-item">

                    <span class="information-label">
                        EMAIL ADDRESS
                    </span>

                    <strong>

                        <?php

                        echo htmlspecialchars(
                            $user["email"]
                        );

                        ?>

                    </strong>

                </div>


                <div class="information-item">

                    <span class="information-label">
                        ACCOUNT TYPE
                    </span>

                    <strong>
                        Job Seeker
                    </strong>

                </div>


                <div class="information-item">

                    <span class="information-label">
                        ACCOUNT STATUS
                    </span>

                    <span class="profile-status">
                        Active
                    </span>

                </div>


                <div class="information-item full-width">

                    <span class="information-label">
                        ACCOUNT ID
                    </span>

                    <strong>

                        #<?php

                        echo htmlspecialchars(
                            $user["id"]
                        );

                        ?>

                    </strong>

                </div>


            </div>


            <!-- ACCOUNT INFORMATION -->

            <div class="account-section">

                <h3>
                    Account Information
                </h3>


                <div class="account-row">

                    <span>
                        Account ID
                    </span>

                    <strong>

                        #<?php

                        echo htmlspecialchars(
                            $user["id"]
                        );

                        ?>

                    </strong>

                </div>


                <div class="account-row">

                    <span>
                        Account Type
                    </span>

                    <strong>
                        Job Seeker
                    </strong>

                </div>


                <div class="account-row">

                    <span>
                        Profile Status
                    </span>

                    <span class="profile-status">
                        Active
                    </span>

                </div>

            </div>


            <!-- ACTION BUTTONS -->

            <div class="profile-actions">

                <a
                    href="edit-profile.php"
                    class="primary-button"
                >
                    Edit Profile
                </a>


                <a
                    href="jobs.php"
                    class="secondary-button"
                >
                    Browse Jobs
                </a>


                <a
                    href="applications.php"
                    class="secondary-button"
                >
                    My Applications
                </a>

            </div>


        </section>

    </div>

</main>


<!-- =================================
     FOOTER
================================= -->

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
$conn->close();
?>