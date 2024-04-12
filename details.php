<?php
// Include the file for establishing database connection
include 'connection.php';

// Include the CSS file
echo '<link rel="stylesheet" type="text/css" href="style.css">';

// Retrieve album_id from query parameters
if (isset($_GET['album_id'])) {
    $album_id = $_GET['album_id'];

    // Fetch album details
    $album_query = "SELECT Title FROM albums WHERE AlbumId = $album_id";
    $album_result = $conn->query($album_query);
    $album_row = $album_result->fetch_assoc();

    // Display album title
    echo "<h2>Album: " . $album_row['Title'] . "</h2>";

    // Fetch tracks for the album
    $tracks_query = "SELECT TrackId, Name, Composer FROM tracks WHERE AlbumId = $album_id";
    $tracks_result = $conn->query($tracks_query);

    if ($tracks_result->num_rows > 0) {
        // Display tracks in a table
        echo "<table border='1'>";
        echo "<tr><th>Track_ID</th><th>Track Name</th><th>Artist</th></tr>";
        while ($track_row = $tracks_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $track_row["TrackId"] . "</td>";
            echo "<td>" . $track_row["Name"] . "</td>";
            echo "<td>" . $track_row["Composer"] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No tracks found for this album.";
    }
} else {
    echo "Album ID not provided.";
}

// Back hyperlink
echo '<br><a href="index.php">Back</a>';

// Close connection
$conn->close();
?>
