<?php

session_start();

require_once "includes/db.php";


/*
==================================================
ADMIN LOGIN DETAILS
==================================================
*/

$admin_username = "admin";
$admin_password = "admin123";

$error = "";
$success = "";


/*
==================================================
LOGOUT
==================================================
*/

if (isset($_GET["logout"])) {

    unset($_SESSION["admin_logged_in"]);

    header("Location: admin.php");
    exit();
}


/*
==================================================
ADMIN LOGIN
==================================================
*/

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["admin_login"])) {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if (
        $username === $admin_username &&
        $password === $admin_password
    ) {

        $_SESSION["admin_logged_in"] = true;

        header("Location: admin.php");
        exit();

    } else {

        $error = "Invalid admin username or password.";
    }
}


/*
==================================================
STATUS UPDATE
==================================================
*/

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["update_status"]) &&
    isset($_SESSION["admin_logged_in"])
) {

    $application_id = (int)($_POST["application_id"] ?? 0);
    $status = trim($_POST["status"] ?? "");


    $allowed_statuses = [
        "Under Review",
        "Shortlisted",
        "Selected",
        "Rejected"
    ];


    if (
        $application_id > 0 &&
        in_array($status, $allowed_statuses, true)
    ) {

        $update = $conn->prepare("
            UPDATE applications
            SET status = ?
            WHERE id = ?
        ");

        $update->bind_param(
            "si",
            $status,
            $application_id
        );


        if ($update->execute()) {

            $success = "Application status updated successfully.";

        } else {

            $error = "Unable to update application status.";
        }


        $update->close();

    } else {

        $error = "Invalid application status.";
    }
}


/*
==================================================
SHOW LOGIN PAGE
==================================================
*/

if (!isset($_SESSION["admin_logged_in"])) {
?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Login | JobPortal</title>

    <link
        rel="stylesheet"
        href="css/admin.css?v=2"
    >

</head>


<body class="login-body">


<div class="login-wrapper">

    <div class="login-card">


        <div class="login-brand">
            JobPortal
        </div>


        <div class="admin-label">
            ADMINISTRATION
        </div>


        <h1>
            Admin Login
        </h1>


        <p class="login-description">
            Sign in to manage job applications.
        </p>


        <?php if ($error !== ""): ?>

            <div class="error-message">

                <?php
                echo htmlspecialchars($error);
                ?>

            </div>

        <?php endif; ?>


        <form method="POST">


            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter admin username"
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
                    placeholder="Enter admin password"
                    required
                >

            </div>


            <button
                type="submit"
                name="admin_login"
                class="login-button"
            >
                Sign In
            </button>


        </form>


        <a
            href="index.php"
            class="back-link"
        >
            ← Back to JobPortal
        </a>


    </div>

</div>


</body>

</html>

<?php

exit();

}


/*
==================================================
GET ALL APPLICATIONS
==================================================
*/

$applications = [];


$sql = "
    SELECT
        applications.id,
        applications.job_id,
        applications.user_id,
        applications.status,
        applications.applied_at,
        applications.phone,
        applications.education,
        applications.skills,
        applications.experience,
        applications.expected_salary,
        applications.resume,
        applications.cover_letter,

        users.name AS applicant_name,
        users.email AS applicant_email,

        jobs.title AS job_title,
        jobs.company AS company_name

    FROM applications

    INNER JOIN users
        ON applications.user_id = users.id

    INNER JOIN jobs
        ON applications.job_id = jobs.id

    ORDER BY applications.applied_at DESC
";


$query = $conn->query($sql);


if ($query) {

    while ($row = $query->fetch_assoc()) {

        $applications[] = $row;
    }
}


/*
==================================================
APPLICATION COUNTS
==================================================
*/

$total_applications = count($applications);

$under_review = 0;
$shortlisted = 0;
$selected = 0;
$rejected = 0;


foreach ($applications as $application) {

    $current_status = $application["status"] ?? "Under Review";


    switch ($current_status) {

        case "Pending":
        case "Under Review":

            $under_review++;

            break;


        case "Shortlisted":

            $shortlisted++;

            break;


        case "Selected":

            $selected++;

            break;


        case "Rejected":

            $rejected++;

            break;
    }
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Admin Dashboard | JobPortal
    </title>


    <link
        rel="stylesheet"
        href="css/admin.css?v=2"
    >

</head>


<body>


<!-- =========================================
     HEADER
========================================= -->

<header class="admin-header">

    <div class="admin-header-inner">


        <a
            href="index.php"
            class="admin-brand"
        >
            JobPortal
        </a>


        <div class="admin-header-right">

            <span class="admin-account">
                Administrator
            </span>


            <a
                href="admin.php?logout=1"
                class="logout-button"
            >
                Logout
            </a>

        </div>


    </div>

</header>



<!-- =========================================
     MAIN
========================================= -->

<main class="admin-container">


    <!-- =====================================
         PAGE HEADING
    ====================================== -->

    <div class="page-heading">

        <div>

            <span class="page-label">
                ADMINISTRATION
            </span>


            <h1>
                Application Management
            </h1>


            <p>
                Review and manage applications submitted by job seekers.
            </p>

        </div>

    </div>



    <!-- =====================================
         SUCCESS MESSAGE
    ====================================== -->

    <?php if ($success !== ""): ?>

        <div class="success-message">

            <?php
            echo htmlspecialchars($success);
            ?>

        </div>

    <?php endif; ?>



    <!-- =====================================
         ERROR MESSAGE
    ====================================== -->

    <?php if ($error !== ""): ?>

        <div class="error-message">

            <?php
            echo htmlspecialchars($error);
            ?>

        </div>

    <?php endif; ?>



    <!-- =====================================
         STATISTICS
    ====================================== -->

    <section class="stats-grid">


        <div class="stat-card">

            <div class="stat-title">
                Total Applications
            </div>


            <div class="stat-number">

                <?php
                echo $total_applications;
                ?>

            </div>

        </div>



        <div class="stat-card">

            <div class="stat-title">
                Under Review
            </div>


            <div class="stat-number">

                <?php
                echo $under_review;
                ?>

            </div>

        </div>



        <div class="stat-card">

            <div class="stat-title">
                Shortlisted
            </div>


            <div class="stat-number">

                <?php
                echo $shortlisted;
                ?>

            </div>

        </div>



        <div class="stat-card selected-stat">

            <div class="stat-title">
                Selected
            </div>


            <div class="stat-number">

                <?php
                echo $selected;
                ?>

            </div>

        </div>



        <div class="stat-card rejected-stat">

            <div class="stat-title">
                Rejected
            </div>


            <div class="stat-number">

                <?php
                echo $rejected;
                ?>

            </div>

        </div>


    </section>



    <!-- =====================================
         APPLICATIONS SECTION
    ====================================== -->

    <section class="applications-section">


        <div class="section-header">

            <div>

                <h2>
                    Submitted Applications
                </h2>


                <p>
                    Review candidates and update their application status.
                </p>

            </div>


            <span class="application-count">

                <?php
                echo $total_applications;
                ?>

                Applications

            </span>

        </div>



        <!-- =================================
             NO APPLICATIONS
        ================================== -->

        <?php if ($total_applications === 0): ?>


            <div class="empty-state">

                <div class="empty-icon">
                    —
                </div>


                <h3>
                    No applications yet
                </h3>


                <p>
                    Applications submitted by job seekers will appear here.
                </p>

            </div>


        <?php else: ?>


            <!-- =================================
                 APPLICATION LIST
            ================================== -->

            <div class="application-list">


                <?php foreach ($applications as $application): ?>


                    <?php

                    $applicant_name =
                        $application["applicant_name"] ?? "Unknown Applicant";

                    $applicant_email =
                        $application["applicant_email"] ?? "";

                    $job_title =
                        $application["job_title"] ?? "Job";

                    $company_name =
                        $application["company_name"] ?? "Company";

                    $status =
                        $application["status"] ?? "Under Review";


                    if ($status === "Pending") {
                        $display_status = "Under Review";
                    } else {
                        $display_status = $status;
                    }


                    $status_class =
                        strtolower(
                            str_replace(
                                " ",
                                "-",
                                $display_status
                            )
                        );

                    ?>


                    <!-- =================================
                         APPLICATION CARD
                    ================================== -->

                    <article class="application-card">


                        <!-- =================================
                             APPLICANT HEADER
                        ================================== -->

                        <div class="application-top">


                            <div class="candidate-info">


                                <div class="candidate-avatar">

                                    <?php

                                    echo htmlspecialchars(
                                        strtoupper(
                                            substr(
                                                $applicant_name,
                                                0,
                                                1
                                            )
                                        )
                                    );

                                    ?>

                                </div>



                                <div>

                                    <h3>

                                        <?php

                                        echo htmlspecialchars(
                                            $applicant_name
                                        );

                                        ?>

                                    </h3>


                                    <p>

                                        <?php

                                        echo htmlspecialchars(
                                            $applicant_email
                                        );

                                        ?>

                                    </p>

                                </div>


                            </div>



                            <!-- STATUS -->

                            <div class="application-status">

                                <span
                                    class="status-badge <?php echo htmlspecialchars($status_class); ?>"
                                >

                                    <?php

                                    echo htmlspecialchars(
                                        $display_status
                                    );

                                    ?>

                                </span>

                            </div>


                        </div>



                        <!-- =================================
                             JOB INFORMATION
                        ================================== -->

                        <div class="job-information">


                            <div>

                                <span>
                                    JOB
                                </span>


                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $job_title
                                    );

                                    ?>

                                </strong>

                            </div>



                            <div>

                                <span>
                                    COMPANY
                                </span>


                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $company_name
                                    );

                                    ?>

                                </strong>

                            </div>



                            <div>

                                <span>
                                    APPLIED
                                </span>


                                <strong>

                                    <?php

                                    if (
                                        !empty(
                                            $application["applied_at"]
                                        )
                                    ) {

                                        echo htmlspecialchars(
                                            date(
                                                "d M Y",
                                                strtotime(
                                                    $application["applied_at"]
                                                )
                                            )
                                        );

                                    } else {

                                        echo "N/A";
                                    }

                                    ?>

                                </strong>

                            </div>


                        </div>



                        <!-- =================================
                             CANDIDATE DETAILS
                        ================================== -->

                        <div class="candidate-details">


                            <?php if (!empty($application["phone"])): ?>

                                <div class="detail-item">

                                    <span>
                                        PHONE
                                    </span>


                                    <p>

                                        <?php

                                        echo htmlspecialchars(
                                            $application["phone"]
                                        );

                                        ?>

                                    </p>

                                </div>

                            <?php endif; ?>



                            <?php if (!empty($application["education"])): ?>

                                <div class="detail-item">

                                    <span>
                                        EDUCATION
                                    </span>


                                    <p>

                                        <?php

                                        echo htmlspecialchars(
                                            $application["education"]
                                        );

                                        ?>

                                    </p>

                                </div>

                            <?php endif; ?>



                            <?php if (!empty($application["experience"])): ?>

                                <div class="detail-item">

                                    <span>
                                        EXPERIENCE
                                    </span>


                                    <p>

                                        <?php

                                        echo htmlspecialchars(
                                            $application["experience"]
                                        );

                                        ?>

                                    </p>

                                </div>

                            <?php endif; ?>



                            <?php if (!empty($application["expected_salary"])): ?>

                                <div class="detail-item">

                                    <span>
                                        EXPECTED SALARY
                                    </span>


                                    <p>

                                        <?php

                                        echo htmlspecialchars(
                                            $application["expected_salary"]
                                        );

                                        ?>

                                    </p>

                                </div>

                            <?php endif; ?>



                            <?php if (!empty($application["skills"])): ?>

                                <div class="detail-item full-detail">

                                    <span>
                                        SKILLS
                                    </span>


                                    <p>

                                        <?php

                                        echo nl2br(
                                            htmlspecialchars(
                                                $application["skills"]
                                            )
                                        );

                                        ?>

                                    </p>

                                </div>

                            <?php endif; ?>


                        </div>



                        <!-- =================================
                             COVER LETTER
                        ================================== -->

                        <?php if (!empty($application["cover_letter"])): ?>


                            <div class="cover-letter">


                                <span>
                                    COVER LETTER
                                </span>


                                <p>

                                    <?php

                                    echo nl2br(
                                        htmlspecialchars(
                                            $application["cover_letter"]
                                        )
                                    );

                                    ?>

                                </p>


                            </div>


                        <?php endif; ?>



                        <!-- =================================
                             APPLICATION FOOTER
                        ================================== -->

                        <div class="application-footer">


                            <!-- =================================
                                 RESUME
                            ================================== -->

                            <div class="resume-area">


                                <?php if (!empty($application["resume"])): ?>


                                    <a
                                        href="view_resume.php?id=<?php echo (int)$application["id"]; ?>"
                                        target="_blank"
                                        class="resume-button"
                                    >
                                        View Resume
                                    </a>


                                <?php else: ?>


                                    <span class="no-resume">
                                        No resume uploaded
                                    </span>


                                <?php endif; ?>


                            </div>



                            <!-- =================================
                                 STATUS UPDATE
                            ================================== -->

                            <form
                                method="POST"
                                class="status-form"
                            >


                                <input
                                    type="hidden"
                                    name="application_id"
                                    value="<?php
                                    echo (int)$application["id"];
                                    ?>"
                                >



                                <label
                                    for="status-<?php echo (int)$application["id"]; ?>"
                                >
                                    Update Status
                                </label>



                                <select
                                    id="status-<?php echo (int)$application["id"]; ?>"
                                    name="status"
                                >


                                    <option
                                        value="Under Review"
                                        <?php

                                        if (
                                            $display_status ===
                                            "Under Review"
                                        ) {
                                            echo "selected";
                                        }

                                        ?>
                                    >
                                        Under Review
                                    </option>



                                    <option
                                        value="Shortlisted"
                                        <?php

                                        if (
                                            $display_status ===
                                            "Shortlisted"
                                        ) {
                                            echo "selected";
                                        }

                                        ?>
                                    >
                                        Shortlisted
                                    </option>



                                    <option
                                        value="Selected"
                                        <?php

                                        if (
                                            $display_status ===
                                            "Selected"
                                        ) {
                                            echo "selected";
                                        }

                                        ?>
                                    >
                                        Selected
                                    </option>



                                    <option
                                        value="Rejected"
                                        <?php

                                        if (
                                            $display_status ===
                                            "Rejected"
                                        ) {
                                            echo "selected";
                                        }

                                        ?>
                                    >
                                        Rejected
                                    </option>


                                </select>



                                <button
                                    type="submit"
                                    name="update_status"
                                    class="update-button"
                                >
                                    Update
                                </button>


                            </form>


                        </div>


                    </article>


                <?php endforeach; ?>


            </div>


        <?php endif; ?>


    </section>


</main>



<!-- =========================================
     FOOTER
========================================= -->

<footer class="admin-footer">

    <p>
        JobPortal Administration
    </p>

</footer>


</body>

</html>

<?php

$conn->close();

?>