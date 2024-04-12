<?php
// Database connection using Include
include 'connection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Chinook</title>
</head>

<body>

    <h2>Chinook</h2>
    <!-- Insert New Record hyperlink -->
    <br>
    <a href="insert.php" style="text-decoration: none">Insert New Record</a>
    <?php
    // Read the database table
    $sql = "SELECT A.AlbumId, A.Title, AT.Name 
            FROM albums A 
            INNER JOIN artists AT ON A.ArtistId = AT.ArtistId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Album</th><th>Artist</th><th>Details</th><th>Update</th><th>Delete</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["Title"] . "</td>";
            echo "<td>" . $row["Name"] . "</td>";
            // Hyperlink for details
            echo "<td><a href='details.php?album_id=" . $row["AlbumId"] . "'>Details</a></td>";
            // Hyperlink for update
            echo "<td><a href='update.php?album_id=" . $row["AlbumId"] . "'>Update</a></td>";
            // Hyperlink for delete
            echo "<td><a href='delete.php?album_id=" . $row["AlbumId"] . "'>Delete</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "0 results";
    }
    ?>
</body>

</html>

<?php
// Close connection
$conn->close();
?>
