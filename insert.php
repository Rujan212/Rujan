<?php
// Database connection using include
include 'connection.php';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = $_POST["title"];       // Get album title from form data
    $artist = $_POST["artist"];     // Get artist name from form data
    $track_names = $_POST["track_name"]; // Get track names from form data

    // Insert new artist if not exists
    $stmt_artist = $conn->prepare("INSERT INTO artists (Name) VALUES (?)");
    $stmt_artist->bind_param("s", $artist);
    if ($stmt_artist->execute()) {
        $artist_id = $stmt_artist->insert_id;
    } else {
        echo "Error inserting artist: " . $conn->error . "<br>";
    }
    $stmt_artist->close();

    // Insert new album
    $stmt_album = $conn->prepare("INSERT INTO albums (Title, ArtistId) VALUES (?, ?)");
    $stmt_album->bind_param("si", $title, $artist_id);
    if ($stmt_album->execute()) {
        echo "New album created successfully<br>";

        // Get the ID of the last inserted album
        $album_id = $stmt_album->insert_id;

        // Insert tracks
        foreach ($track_names as $track_name) {
            $stmt_track = $conn->prepare("INSERT INTO tracks (Name, AlbumId, Composer) VALUES (?, ?,?)");
            $stmt_track->bind_param("sis", $track_name, $album_id, $artist);
            if ($stmt_track->execute()) {
                echo "Track '$track_name' inserted successfully.<br>";
                header('location: index.php');
            } else {
                echo "Error inserting track '$track_name': " . $conn->error . "<br>";
            }
            $stmt_track->close();
        }
    } else {
        echo "Error inserting album: " . $conn->error . "<br>";
    }
    $stmt_album->close();
}

// Close connection 
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Album</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <h2>Add New Album</h2>
    <div class="Insert">
        <!-- Form creation -->
        <form action="insert.php" method="POST" id="albumForm">
            <table id="trackTable">
                <tr>
                    <td><label for="title">Album Title:</label></td>
                    <td><input type="text" id="title" name="title" required></td>
                </tr>
                <tr>
                    <td><label for="artist">Artist:</label></td>
                    <td><input type="text" id="artist" name="artist" required></td>
                </tr>
                <tr id="trackRow">
                    <td><label for="track_name">Track Name:</label></td>
                    <td><input type="text" name="track_name[]" required></td>
                </tr>
            </table>
            <button type="button" onclick="addTrack()">Add Track</button>
            <input type="submit" value="Submit">
        </form>
        <!-- Back Hyperlink -->
        <a href="index.php">Back</a>
    </div>
    <!-- JavaScript Section -->
    <script>
        // Function to add a new row for track name input
        function addTrack() {
            var trackTable = document.getElementById("trackTable");
            var newRow = document.createElement("tr");
            var td1 = document.createElement("td");
            var td2 = document.createElement("td");
            var label = document.createElement("label");
            label.textContent = "Track Name:";
            var input = document.createElement("input");
            input.type = "text";
            input.name = "track_name[]";
            input.required = true;
            td1.appendChild(label);
            td2.appendChild(input);
            newRow.appendChild(td1);
            newRow.appendChild(td2);
            trackTable.appendChild(newRow);
        }
    </script>
</body>

</html>