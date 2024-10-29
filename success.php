<?php
session_start();
include 'components/connect.php';

// Check if playlist_id is set in the query string
if (isset($_GET['playlist_id'])) {
    $playlist_id = filter_var($_GET['playlist_id'], FILTER_SANITIZE_NUMBER_INT);

    // Fetch playlist details
    $select_playlist = $conn->prepare("SELECT title FROM playlist WHERE id = ?");
    $select_playlist->execute([$playlist_id]);
    $playlist = $select_playlist->fetch(PDO::FETCH_ASSOC);

    if (!$playlist) {
        // Log the error for debugging
        error_log("Playlist not found for playlist_id: $playlist_id");
        echo 'Playlist not found.';
        exit();
    }
} else {
    echo 'Required information is missing.';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 {
            color: #333;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Payment Successful!</h2>
        <p>Thank you for your purchase of <strong><?= htmlspecialchars($playlist['title']); ?></strong>.</p>
        <p>You can now access your content in the playlist.</p>
        <a href="playlist.php?id=<?= $playlist_id; ?>" class="btn">View Playlist</a>
        <a href="index.php" class="btn">Back to Home</a> <!-- Added home link for better navigation -->
    </div>
</body>
</html>
