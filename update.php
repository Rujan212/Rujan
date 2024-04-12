<?php
// Include the file for establishing database connection
include 'connection.php';

// Initialize variables
$album_id = "";
$title = "";
$artist = "";
$track_names = [];

// Check if album_id is set in the URL
if (isset($_GET['album_id'])) {
    // Get the album_id from the URL
    $album_id = $_GET['album_id'];

    // Fetch album data based on album_id
    $sql = "SELECT * FROM albums WHERE AlbumId = $album_id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Fetch the album details
        $album = $result->fetch_assoc();
        $title = $album['Title'];
        $artist = $album['ArtistId'];

        // Fetch track names associated with this album
        $sql_tracks = "SELECT Name FROM tracks WHERE AlbumId = $album_id";
        $result_tracks = $conn->query($sql_tracks);
        while ($row = $result_tracks->fetch_assoc()) {
            $track_names[] = $row['Name'];
        }
    }
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = $_POST["title"];       // Get album title from form data
    $artist = $_POST["artist"];     // Get artist name from form data
    $album_id = $_POST["album_id"];

    // Update existing album
    if (!empty($album_id)) {
        // Update album details
        $stmt_album = $conn->prepare("UPDATE albums SET Title = ?, ArtistId = ? WHERE AlbumId = ?");
        $stmt_album->bind_param("sii", $title, $artist, $album_id);
        if ($stmt_album->execute()) {
            echo "Album updated successfully<br>";

            // Delete existing tracks associated with this album
            $sql_delete_tracks = "DELETE FROM tracks WHERE AlbumId = $album_id";
            if ($conn->query($sql_delete_tracks)) {
                if (isset($_POST["track_name"])) {
                    $track_names = $_POST["track_name"]; // Get track names from form data
                    // Insert updated track list
                    foreach ($track_names as $track_name) {
                        $stmt_track = $conn->prepare("INSERT INTO tracks (Name, AlbumId, Composer) VALUES (?, ?, ?)");
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
                    header('location: index.php');
                }
            } else {
                echo "Error deleting existing tracks: " . $conn->error . "<br>";
            }
        } else {
            echo "Error updating album: " . $conn->error . "<br>";
        }
        $stmt_album->close();
    }
}

// Close connection 
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Album</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <h2>Update Album</h2>
    <div class="Update">
        <!-- Form creation -->
        <form action="update.php" method="POST" id="albumForm">
            <input type="hidden" name="album_id" value="<?php echo $album_id; ?>">
            <table id="trackTable">
                <tr>
                    <td><label for="title">Album Title:</label></td>
                    <td><input type="text" id="title" name="title" value="<?php echo $title; ?>" required></td>
                </tr>
                <tr>
                    <td><label for="artist">Artist:</label></td>
                    <td><input type="text" id="artist" name="artist" value="<?php echo $artist; ?>" required></td>
                </tr>
                <?php foreach ($track_names as $index => $track_name) { ?>
                    <tr>
                        <td><label for="track_name">Track Name:</label></td>
                        <td>
                            <input type="text" name="track_name[]" value="<?php echo $track_name; ?>" required>
                            <button type="button" onclick="deleteTrack(this)">Delete</button>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <button type="button" onclick="addTrack()">Add Track</button>
            <input type="submit" value="Submit">
        </form>
    </div>

    <script>
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
            var deleteButton = document.createElement("button");
            deleteButton.textContent = "Delete";
            deleteButton.type = "button";
            deleteButton.onclick = function() {
                deleteTrack(this);
            };
            td1.appendChild(label);
            td2.appendChild(input);
            td2.appendChild(deleteButton);
            newRow.appendChild(td1);
            newRow.appendChild(td2);
            trackTable.appendChild(newRow);
        }

        function deleteTrack(button) {
            var row = button.parentNode.parentNode;
            row.parentNode.removeChild(row);
        }
    </script>
</body>

</html>