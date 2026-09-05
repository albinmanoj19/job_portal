<?php

session_start();

require_once "includes/db.php";


/* Admin must be logged in */

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin.php");
    exit();
}


/* Check application ID */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Invalid application.");
}


$application_id = (int) $_GET["id"];


/* Get resume filename */

$stmt = $conn->prepare("
    SELECT resume
    FROM applications
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $application_id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Application not found.");
}


$application = $result->fetch_assoc();

$stmt->close();


$resume = trim($application["resume"]);


if ($resume === "") {
    die("No resume was uploaded for this application.");
}


/*
==================================================
FIND THE RESUME
==================================================
*/

$filename = basename($resume);


/*
If the database contains only:
resume.pdf

use:
uploads/resumes/resume.pdf

If it contains:
uploads/resumes/resume.pdf

we still use the filename safely.
*/

$resume_path = __DIR__ . DIRECTORY_SEPARATOR
             . "uploads"
             . DIRECTORY_SEPARATOR
             . "resumes"
             . DIRECTORY_SEPARATOR
             . $filename;


if (!file_exists($resume_path)) {

    die(
        "Resume file was not found.<br><br>" .
        "Expected location:<br>" .
        htmlspecialchars($resume_path)
    );
}


/*
==================================================
OPEN RESUME
==================================================
*/

$extension = strtolower(
    pathinfo($resume_path, PATHINFO_EXTENSION)
);


$mime_types = [

    "pdf"  => "application/pdf",

    "doc"  => "application/msword",

    "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document"

];


$mime = $mime_types[$extension] ?? "application/octet-stream";


header("Content-Type: " . $mime);

header(
    'Content-Disposition: inline; filename="' .
    basename($resume_path) .
    '"'
);

header("Content-Length: " . filesize($resume_path));

readfile($resume_path);

exit();

?>