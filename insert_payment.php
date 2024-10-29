<?php
session_start();
include 'components/connect.php';

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Check if data is valid
if (isset($data['user_id'], $data['playlist_id'])) {
    $user_id = filter_var($data['user_id'], FILTER_SANITIZE_NUMBER_INT);
    $playlist_id = filter_var($data['playlist_id'], FILTER_SANITIZE_NUMBER_INT);

    // Check if the playlist ID exists and is valid
    $check_playlist = $conn->prepare("SELECT * FROM playlist WHERE id = ?");
    $check_playlist->execute([$playlist_id]);

    if ($check_playlist->rowCount() === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Playlist not found.']);
        exit();
    }

    // Insert purchase into the database
    $insert_purchase = $conn->prepare("INSERT INTO purchases (user_id, playlist_id, purchase_date) VALUES (?, ?, NOW())");

    if ($insert_purchase->execute([$user_id, $playlist_id])) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to log purchase.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data received.']);
}
