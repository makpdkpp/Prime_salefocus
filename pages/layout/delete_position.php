<?php
include("../../connect.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query to delete record from database
    $sql = "DELETE FROM position WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        
        header("location: position_u.php"); // Redirect back to index page
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
