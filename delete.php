<?php
// Include the file for establishing database connection
include 'connection.php';

// Include the CSS file
echo '<link rel="stylesheet" type="text/css" href="style.css">';

// Check if the album_id is posted via form submission
if (isset($_POST["album_id"])) {
    // Retrieve the album_id from the POST data
    $albumId = $_POST["album_id"];

    // SQL query to delete the album based on the provided album_id
    $sql = "DELETE 
            FROM albums
            WHERE AlbumId = $albumId";

    // Execute the SQL query
    if ($conn->query($sql)) {
        // Redirect to the index page after successful deletion
        header('location: index.php');
    } else {
        // Display error message if deletion fails
        echo "Error Delete Album";
    }
}

// Check if the album_id is set in the URL
if (isset($_GET['album_id'])) {
    // Retrieve the album_id from the URL
    $albumId = $_GET['album_id'];

    // SQL query to fetch album details based on the provided album_id
    $sql = "SELECT A.Title, AT.Name 
            FROM albums A 
            INNER JOIN artists AT ON A.ArtistId = AT.ArtistId 
            WHERE A.AlbumId = $albumId";

    // Execute the SQL query
    $result = $conn->query($sql);

    // Check if a single album is found
    if ($result->num_rows == 1) {
        // Fetch the album details
        $row = $result->fetch_assoc();
        $albumTitle = $row['Title'];
        $artistName = $row['Name'];

        // Display album details and confirmation message for deletion
        echo "<h2>Delete Album</h2>";
        echo "<p>Are you sure you want to delete the album \"$albumTitle\" by \"$artistName\"?</p>";
        echo "<form action='delete.php' method='post'>";
        echo "<input type='hidden' name='album_id' value='$albumId'>"; // Hidden input to pass album_id
        echo "<input type='submit' value='Yes, Delete'>";
        echo '<br><a href="index.php">No</a>';
        echo "</form>";
    } else {
        // Display error message if album not found
        echo "Album not found.";
    }
} else {
    // Display error message if album_id is not provided in the URL
    echo "Album ID not provided.";
}

// Display tracks for the provided album_id
if (isset($_GET['album_id'])) {
    // Retrieve the album_id from the URL
    $album_id = $_GET['album_id'];

    // SQL query to fetch album details
    $album_query = "SELECT Title FROM albums WHERE AlbumId = $album_id";

    // Execute the SQL query
    $album_result = $conn->query($album_query);
    $album_row = $album_result->fetch_assoc();

    // Display album title
    echo "<h2>Album: " . $album_row['Title'] . "</h2>";

    // SQL query to fetch tracks for the album
    $tracks_query = "SELECT TrackId, Name, Composer FROM tracks WHERE AlbumId = $album_id";

    // Execute the SQL query
    $tracks_result = $conn->query($tracks_query);

    // Check if tracks are found for the album
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
        // Display message if no tracks are found for the album
        echo "No tracks found for this album.";
    }
} else {
    // Display error message if album_id is not provided in the URL
    echo "Album ID not provided.";
}
//Close Connection
$conn->close();
echo '<br><button><a href="index.php">Back</a></button>';
?>
