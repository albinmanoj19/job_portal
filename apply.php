<?php
session_start();

require_once "includes/db.php";

/* =========================
   CHECK LOGIN
========================= */

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];


/* =========================
   CHECK JOB ID
========================= */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: jobs.php");
    exit();
}

$job_id = (int) $_GET["id"];


/* =========================
   GET JOB DETAILS
========================= */

$stmt = $conn->prepare("
    SELECT id, title, company, location, salary, category
    FROM jobs
    WHERE id = ?
");

$stmt->bind_param("i", $job_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: jobs.php");
    exit();
}

$job = $result->fetch_assoc();

$stmt->close();


/* =========================
   GET USER DETAILS
========================= */

$stmt = $conn->prepare("
    SELECT name, email
    FROM users
    WHERE id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$user_result = $stmt->get_result();

$user = $user_result->fetch_assoc();

$stmt->close();


/* =========================
   CHECK EXISTING APPLICATION
========================= */

$stmt = $conn->prepare("
    SELECT id, status
    FROM applications
    WHERE job_id = ? AND user_id = ?
");

$stmt->bind_param("ii", $job_id, $user_id);
$stmt->execute();

$existing_result = $stmt->get_result();

$existing_application = null;

if ($existing_result->num_rows > 0) {
    $existing_application = $existing_result->fetch_assoc();
}

$stmt->close();


$message = "";
$message_type = "";


/* =========================
   SUBMIT APPLICATION
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST" && !$existing_application) {

    $phone = trim($_POST["phone"] ?? "");
    $education = trim($_POST["education"] ?? "");
    $skills = trim($_POST["skills"] ?? "");
    $experience = trim($_POST["experience"] ?? "");
    $expected_salary = trim($_POST["expected_salary"] ?? "");
    $cover_letter = trim($_POST["cover_letter"] ?? "");

    $errors = [];


    /* =========================
       REQUIRED FIELDS
    ========================= */

    if ($phone === "") {
        $errors[] = "Phone number is required.";
    }

    if ($education === "") {
        $errors[] = "Education is required.";
    }

    if ($skills === "") {
        $errors[] = "Skills are required.";
    }

    if ($experience === "") {
        $errors[] = "Experience is required.";
    }

    if ($expected_salary === "") {
        $errors[] = "Expected salary is required.";
    }

    if ($cover_letter === "") {
        $errors[] = "Cover letter is required.";
    }


    /* =========================
       RESUME VALIDATION
    ========================= */

    $resume_path = "";
    $extension = "";

    if (
        !isset($_FILES["resume"]) ||
        $_FILES["resume"]["error"] === UPLOAD_ERR_NO_FILE
    ) {

        $errors[] = "Please upload your resume.";

    } else {

        $resume = $_FILES["resume"];

        if ($resume["error"] !== UPLOAD_ERR_OK) {

            $errors[] = "There was a problem uploading your resume.";

        } else {

            $allowed_extensions = [
                "pdf",
                "doc",
                "docx"
            ];

            $extension = strtolower(
                pathinfo(
                    $resume["name"],
                    PATHINFO_EXTENSION
                )
            );

            if (!in_array($extension, $allowed_extensions)) {
                $errors[] = "Resume must be PDF, DOC or DOCX.";
            }

            if ($resume["size"] > 5 * 1024 * 1024) {
                $errors[] = "Resume must be smaller than 5 MB.";
            }
        }
    }


    /* =========================
       PROCESS APPLICATION
    ========================= */

    if (empty($errors)) {

        /*
         * Resume folder
         */
        $upload_directory = __DIR__ . "/uploads/resumes/";

        /*
         * Create folder automatically
         */
        if (!is_dir($upload_directory)) {

            mkdir(
                $upload_directory,
                0777,
                true
            );
        }


        /*
         * Create a unique filename
         */
        $filename =
            "resume_" .
            $user_id .
            "_" .
            time() .
            "_" .
            uniqid() .
            "." .
            $extension;


        /*
         * Complete server path
         */
        $server_path =
            $upload_directory .
            $filename;


        /*
         * Database path
         */
        $resume_path =
            "uploads/resumes/" .
            $filename;


        /*
         * Move uploaded resume
         */
        if (
            move_uploaded_file(
                $_FILES["resume"]["tmp_name"],
                $server_path
            )
        ) {

            /*
             * Insert application
             */
            $stmt = $conn->prepare("
                INSERT INTO applications
                (
                    job_id,
                    user_id,
                    status,
                    phone,
                    education,
                    skills,
                    experience,
                    expected_salary,
                    resume,
                    cover_letter
                )
                VALUES
                (
                    ?,
                    ?,
                    'Pending',
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                )
            ");


            $stmt->bind_param(
                "iisssssss",
                $job_id,
                $user_id,
                $phone,
                $education,
                $skills,
                $experience,
                $expected_salary,
                $resume_path,
                $cover_letter
            );


            if ($stmt->execute()) {

                $message =
                    "Your application has been submitted successfully.";

                $message_type = "success";

                $existing_application = [
                    "id" => $stmt->insert_id,
                    "status" => "Pending"
                ];

            } else {

                /*
                 * Delete uploaded resume if
                 * database insertion fails
                 */
                if (file_exists($server_path)) {
                    unlink($server_path);
                }

                $message =
                    "Unable to submit your application.";

                $message_type = "error";
            }

            $stmt->close();

        } else {

            $message =
                "Unable to upload your resume. Please try again.";

            $message_type = "error";
        }

    } else {

        $message = implode("<br>", $errors);

        $message_type = "error";
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
        Apply for
        <?php echo htmlspecialchars($job["title"]); ?>
        | JobPortal
    </title>

    <link
        rel="stylesheet"
        href="css/apply.css?v=11"
    >

</head>


<body>


<!-- =========================================
     HEADER
========================================= -->

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

            <a href="dashboard.php">
                Dashboard
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


<!-- =========================================
     PROGRESS
========================================= -->

<div class="progress-wrapper">

    <div class="progress-container">

        <div class="progress-step completed">

            <span>✓</span>

            <p>Job Details</p>

        </div>


        <div class="progress-line completed-line"></div>


        <div class="progress-step active">

            <span>2</span>

            <p>Application</p>

        </div>


        <div class="progress-line"></div>


        <div class="progress-step">

            <span>3</span>

            <p>Submit</p>

        </div>

    </div>

</div>


<!-- =========================================
     MAIN
========================================= -->

<main class="page-container">


    <a
        href="job_details.php?id=<?php echo $job_id; ?>"
        class="back-link"
    >
        ← Back to Job Details
    </a>


    <div class="page-heading">

        <h1>
            Apply for this position
        </h1>

        <p>
            Complete your application and send your profile
            directly to the employer.
        </p>

    </div>


    <!-- =====================================
         MESSAGE
    ====================================== -->

    <?php if ($message !== ""): ?>

        <div
            class="message <?php echo $message_type; ?>"
        >

            <?php echo $message; ?>

        </div>

    <?php endif; ?>


    <?php if (!$existing_application): ?>


    <!-- =====================================
         APPLICATION LAYOUT
    ====================================== -->

    <div class="application-layout">


        <!-- =================================
             JOB SIDEBAR
        ================================== -->

        <aside class="job-sidebar">

            <div class="sidebar-label">
                APPLYING FOR
            </div>


            <h2>

                <?php
                echo htmlspecialchars(
                    $job["title"]
                );
                ?>

            </h2>


            <p class="company-name">

                <?php
                echo htmlspecialchars(
                    $job["company"]
                );
                ?>

            </p>


            <div class="job-information">


                <div class="job-info-item">

                    <span class="info-icon">
                        📍
                    </span>

                    <div>

                        <small>
                            Location
                        </small>

                        <strong>

                            <?php
                            echo htmlspecialchars(
                                $job["location"]
                            );
                            ?>

                        </strong>

                    </div>

                </div>


                <div class="job-info-item">

                    <span class="info-icon">
                        💰
                    </span>

                    <div>

                        <small>
                            Salary
                        </small>

                        <strong>

                            <?php
                            echo htmlspecialchars(
                                $job["salary"]
                            );
                            ?>

                        </strong>

                    </div>

                </div>


                <div class="job-info-item">

                    <span class="info-icon">
                        💼
                    </span>

                    <div>

                        <small>
                            Category
                        </small>

                        <strong>

                            <?php
                            echo htmlspecialchars(
                                $job["category"]
                            );
                            ?>

                        </strong>

                    </div>

                </div>

            </div>


            <div class="sidebar-divider"></div>


            <div class="application-help">

                <h3>
                    Application Tips
                </h3>

                <ul>

                    <li>
                        Keep your information accurate.
                    </li>

                    <li>
                        Upload your latest resume.
                    </li>

                    <li>
                        Highlight relevant skills.
                    </li>

                    <li>
                        Write a professional cover letter.
                    </li>

                </ul>

            </div>

        </aside>


        <!-- =================================
             APPLICATION FORM
        ================================== -->

        <section class="application-card">


            <div class="form-header">

                <div>

                    <h2>
                        Application Form
                    </h2>

                    <p>
                        Tell the employer about your qualifications
                        and experience.
                    </p>

                </div>


                <span class="required-note">
                    * Required
                </span>

            </div>


            <form
                method="POST"
                enctype="multipart/form-data"
            >


                <!-- =========================
                     PERSONAL INFORMATION
                ========================== -->

                <div class="form-section">

                    <div class="section-heading">

                        <span class="section-number">
                            01
                        </span>

                        <div>

                            <h3>
                                Personal Information
                            </h3>

                            <p>
                                Your contact information
                            </p>

                        </div>

                    </div>


                    <div class="form-grid">


                        <div class="form-group">

                            <label>
                                Full Name
                            </label>

                            <input
                                type="text"
                                value="<?php
                                echo htmlspecialchars(
                                    $user["name"]
                                );
                                ?>"
                                readonly
                            >

                        </div>


                        <div class="form-group">

                            <label>
                                Email Address
                            </label>

                            <input
                                type="email"
                                value="<?php
                                echo htmlspecialchars(
                                    $user["email"]
                                );
                                ?>"
                                readonly
                            >

                        </div>


                        <div class="form-group full-width">

                            <label for="phone">

                                Phone Number

                                <span>
                                    *
                                </span>

                            </label>

                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                placeholder="Enter your phone number"
                                required
                            >

                        </div>

                    </div>

                </div>


                <!-- =========================
                     EDUCATION AND SKILLS
                ========================== -->

                <div class="form-section">

                    <div class="section-heading">

                        <span class="section-number">
                            02
                        </span>

                        <div>

                            <h3>
                                Education & Skills
                            </h3>

                            <p>
                                Tell us about your qualifications.
                            </p>

                        </div>

                    </div>


                    <div class="form-grid">


                        <div class="form-group">

                            <label for="education">

                                Education

                                <span>
                                    *
                                </span>

                            </label>

                            <input
                                type="text"
                                id="education"
                                name="education"
                                placeholder="Example: B.Tech Computer Science"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="experience">

                                Experience

                                <span>
                                    *
                                </span>

                            </label>

                            <select
                                id="experience"
                                name="experience"
                                required
                            >

                                <option value="">
                                    Select experience
                                </option>

                                <option value="Fresher">
                                    Fresher
                                </option>

                                <option value="Less than 1 year">
                                    Less than 1 year
                                </option>

                                <option value="1 - 3 years">
                                    1 - 3 years
                                </option>

                                <option value="3 - 5 years">
                                    3 - 5 years
                                </option>

                                <option value="5+ years">
                                    5+ years
                                </option>

                            </select>

                        </div>


                        <div class="form-group full-width">

                            <label for="skills">

                                Skills

                                <span>
                                    *
                                </span>

                            </label>

                            <input
                                type="text"
                                id="skills"
                                name="skills"
                                placeholder="Example: PHP, MySQL, HTML, CSS, JavaScript"
                                required
                            >

                            <small>
                                Separate multiple skills with commas.
                            </small>

                        </div>

                    </div>

                </div>


                <!-- =========================
                     PROFESSIONAL DETAILS
                ========================== -->

                <div class="form-section">

                    <div class="section-heading">

                        <span class="section-number">
                            03
                        </span>

                        <div>

                            <h3>
                                Professional Details
                            </h3>

                            <p>
                                Provide your career expectations.
                            </p>

                        </div>

                    </div>


                    <div class="form-group">

                        <label for="expected_salary">

                            Expected Salary

                            <span>
                                *
                            </span>

                        </label>

                        <input
                            type="text"
                            id="expected_salary"
                            name="expected_salary"
                            placeholder="Example: ₹35,000 per month"
                            required
                        >

                    </div>

                </div>


                <!-- =========================
                     RESUME
                ========================== -->

                <div class="form-section">

                    <div class="section-heading">

                        <span class="section-number">
                            04
                        </span>

                        <div>

                            <h3>
                                Resume
                            </h3>

                            <p>
                                Upload your latest resume.
                            </p>

                        </div>

                    </div>


                    <div
                        class="resume-upload"
                        id="resumeUploadBox"
                    >


                        <div class="upload-icon">
                            ↑
                        </div>


                        <h3 id="resumeTitle">
                            Upload your resume
                        </h3>


                        <p id="resumeText">
                            PDF, DOC or DOCX
                        </p>


                        <p>
                            Maximum size: 5 MB
                        </p>


                        <input
                            type="file"
                            id="resume"
                            name="resume"
                            accept=".pdf,.doc,.docx"
                            required
                        >


                        <label
                            for="resume"
                            class="choose-file"
                        >
                            Choose Resume
                        </label>


                        <!-- SELECTED FILE -->

                        <div
                            id="selectedResume"
                            class="selected-resume"
                            style="display: none;"
                        >

                            <span>
                                ✓
                            </span>

                            <strong
                                id="selectedResumeName"
                            ></strong>

                        </div>

                    </div>

                </div>


                <!-- =========================
                     COVER LETTER
                ========================== -->

                <div class="form-section">

                    <div class="section-heading">

                        <span class="section-number">
                            05
                        </span>

                        <div>

                            <h3>
                                Cover Letter
                            </h3>

                            <p>
                                Explain why you're a good fit.
                            </p>

                        </div>

                    </div>


                    <div class="form-group">

                        <label for="cover_letter">

                            Cover Letter

                            <span>
                                *
                            </span>

                        </label>


                        <textarea
                            id="cover_letter"
                            name="cover_letter"
                            rows="8"
                            placeholder="Write your cover letter here..."
                            required
                        ></textarea>

                    </div>

                </div>


                <!-- =========================
                     CONFIRMATION
                ========================== -->

                <div class="confirmation-box">

                    <input
                        type="checkbox"
                        id="confirmation"
                        required
                    >

                    <label for="confirmation">

                        I confirm that the information provided
                        in this application is accurate and complete.

                    </label>

                </div>


                <!-- =========================
                     SUBMIT
                ========================== -->

                <div class="submit-area">

                    <button
                        type="submit"
                        class="submit-button"
                    >
                        Submit Application
                    </button>


                    <p>
                        Your application will be sent to the employer
                        for review.
                    </p>

                </div>


            </form>

        </section>

    </div>


    <?php else: ?>


    <!-- =====================================
         APPLICATION SUCCESS
    ====================================== -->

    <section class="submitted-card">


        <div class="submitted-icon">
            ✓
        </div>


        <span class="submitted-label">
            APPLICATION RECEIVED
        </span>


        <h2>
            Application submitted successfully
        </h2>


        <p>

            Your application for

            <strong>

                <?php
                echo htmlspecialchars(
                    $job["title"]
                );
                ?>

            </strong>

            at

            <strong>

                <?php
                echo htmlspecialchars(
                    $job["company"]
                );
                ?>

            </strong>

            has been received.

        </p>


        <div class="application-status">

            <span>
                Application Status
            </span>

            <strong>

                <?php
                echo htmlspecialchars(
                    $existing_application["status"]
                );
                ?>

            </strong>

        </div>


        <div class="submitted-actions">

            <a href="applications.php">
                View My Applications
            </a>

            <a href="jobs.php">
                Browse More Jobs
            </a>

        </div>

    </section>


    <?php endif; ?>


</main>


<!-- =========================================
     FOOTER
========================================= -->

<footer class="site-footer">

    <div class="footer-inner">


        <div>

            <h3>
                JobPortal
            </h3>

            <p>
                Connecting talented people with great opportunities.
            </p>

        </div>


        <div class="footer-links">

            <a href="index.php">
                Home
            </a>

            <a href="jobs.php">
                Find Jobs
            </a>

            <a href="dashboard.php">
                Dashboard
            </a>

        </div>

    </div>


    <div class="footer-bottom">

        © 2026 JobPortal. All Rights Reserved.

    </div>

</footer>


<!-- =========================================
     RESUME FILE DISPLAY
========================================= -->

<script>

const resumeInput =
    document.getElementById("resume");

const selectedResume =
    document.getElementById("selectedResume");

const selectedResumeName =
    document.getElementById("selectedResumeName");

const resumeTitle =
    document.getElementById("resumeTitle");

const resumeText =
    document.getElementById("resumeText");


if (resumeInput) {

    resumeInput.addEventListener(
        "change",
        function () {

            if (this.files.length > 0) {

                const file = this.files[0];


                /*
                 * Show selected filename
                 */

                selectedResumeName.textContent =
                    file.name;


                selectedResume.style.display =
                    "flex";


                resumeTitle.textContent =
                    "Resume selected";


                resumeText.textContent =
                    "Your resume is ready to be uploaded.";


            } else {

                selectedResume.style.display =
                    "none";


                resumeTitle.textContent =
                    "Upload your resume";


                resumeText.textContent =
                    "PDF, DOC or DOCX";
            }

        }
    );

}

</script>


</body>

</html>