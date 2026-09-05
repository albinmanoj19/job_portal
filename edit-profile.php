<?php
session_start();

require_once "includes/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$message = "";
$error = "";


/* =========================================
   UPDATE PROFILE
========================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);


    /* Basic validation */

    if ($name === "" || $email === "") {

        $error = "Please fill in all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } else {


        /* Check whether email belongs to another user */

        $check = $conn->prepare("
            SELECT id
            FROM users
            WHERE email = ? AND id != ?
        ");

        $check->bind_param(
            "si",
            $email,
            $user_id
        );

        $check->execute();

        $check_result = $check->get_result();


        if ($check_result->num_rows > 0) {

            $error = "This email address is already registered.";

        } else {


            /* =========================================
               PROFILE PICTURE UPLOAD
            ========================================= */

            $new_picture = null;

            if (
                isset($_FILES["profile_picture"]) &&
                $_FILES["profile_picture"]["error"] !== UPLOAD_ERR_NO_FILE
            ) {

                if ($_FILES["profile_picture"]["error"] !== UPLOAD_ERR_OK) {

                    $error = "There was a problem uploading the picture.";

                } else {

                    $file = $_FILES["profile_picture"];

                    $max_size = 5 * 1024 * 1024;

                    if ($file["size"] > $max_size) {

                        $error = "Profile picture must be less than 5 MB.";

                    } else {

                        $allowed_types = [
                            "image/jpeg" => "jpg",
                            "image/png" => "png",
                            "image/webp" => "webp"
                        ];

                        $file_type = mime_content_type(
                            $file["tmp_name"]
                        );


                        if (!isset($allowed_types[$file_type])) {

                            $error = "Only JPG, PNG and WEBP images are allowed.";

                        } else {

                            /* Create upload directory */

                            $upload_dir = "uploads/profile/";

                            if (!is_dir($upload_dir)) {
                                mkdir(
                                    $upload_dir,
                                    0755,
                                    true
                                );
                            }


                            /* Create unique filename */

                            $extension = $allowed_types[$file_type];

                            $new_picture =
                                "profile_" .
                                $user_id .
                                "_" .
                                time() .
                                "." .
                                $extension;


                            $upload_path =
                                $upload_dir .
                                $new_picture;


                            if (!move_uploaded_file(
                                $file["tmp_name"],
                                $upload_path
                            )) {

                                $error =
                                    "Unable to save the profile picture.";

                                $new_picture = null;
                            }
                        }
                    }
                }
            }


            /* =========================================
               UPDATE DATABASE
            ========================================= */

            if ($error === "") {

                if ($new_picture !== null) {

                    $update = $conn->prepare("
                        UPDATE users
                        SET name = ?, email = ?, profile_picture = ?
                        WHERE id = ?
                    ");

                    $update->bind_param(
                        "sssi",
                        $name,
                        $email,
                        $new_picture,
                        $user_id
                    );

                } else {

                    $update = $conn->prepare("
                        UPDATE users
                        SET name = ?, email = ?
                        WHERE id = ?
                    ");

                    $update->bind_param(
                        "ssi",
                        $name,
                        $email,
                        $user_id
                    );
                }


                if ($update->execute()) {

                    $message =
                        "Profile updated successfully.";

                } else {

                    $error =
                        "Unable to update your profile.";
                }


                $update->close();
            }
        }

        $check->close();
    }
}


/* =========================================
   GET CURRENT USER
========================================= */

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

    <title>Edit Profile | JobPortal</title>

    <link
        rel="stylesheet"
        href="css/edit-profile.css?v=2"
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


<!-- =========================================
     MAIN
========================================= -->

<main class="edit-container">


    <div class="page-heading">

        <span class="page-label">
            ACCOUNT SETTINGS
        </span>

        <h1>
            Edit Profile
        </h1>

        <p>
            Update your account information and profile picture.
        </p>

    </div>


    <div class="edit-card">


        <?php if ($message !== ""): ?>

            <div class="success-message">

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php endif; ?>


        <?php if ($error !== ""): ?>

            <div class="error-message">

                <?php
                echo htmlspecialchars($error);
                ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            enctype="multipart/form-data"
        >


            <!-- =================================
                 PROFILE PICTURE
            ================================= -->

            <div class="picture-section">

                <div class="picture-preview">

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

                        <span>

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

                        </span>

                    <?php endif; ?>

                </div>


                <div class="picture-details">

                    <h3>
                        Profile Picture
                    </h3>

                    <p>
                        Upload a professional photo for your profile.
                    </p>


                    <label
                        for="profile_picture"
                        class="upload-button"
                    >
                        Choose Picture
                    </label>

                    <input
                        type="file"
                        id="profile_picture"
                        name="profile_picture"
                        accept="image/jpeg,image/png,image/webp"
                    >

                    <small>
                        JPG, PNG or WEBP. Maximum size: 5 MB.
                    </small>

                </div>

            </div>


            <!-- =================================
                 NAME
            ================================= -->

            <div class="form-group">

                <label for="name">
                    Full Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?php
                    echo htmlspecialchars(
                        $user["name"]
                    );
                    ?>"
                    required
                >

            </div>


            <!-- =================================
                 EMAIL
            ================================= -->

            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php
                    echo htmlspecialchars(
                        $user["email"]
                    );
                    ?>"
                    required
                >

            </div>


            <!-- =================================
                 ACTIONS
            ================================= -->

            <div class="form-actions">

                <a
                    href="profile.php"
                    class="cancel-button"
                >
                    Cancel
                </a>


                <button
                    type="submit"
                    class="save-button"
                >
                    Save Changes
                </button>

            </div>


        </form>

    </div>

</main>


<!-- =========================================
     FOOTER
========================================= -->

<footer class="site-footer">

    <div class="footer-bottom">

        © 2026 JobPortal. All Rights Reserved.

    </div>

</footer>


</body>
</html>

<?php
$conn->close();
?>