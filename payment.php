<?php
session_start();
include 'components/connect.php';

// Initialize variables
$title = '';
$amount = 0.0;
$playlist_id = 0;

// Check if POST data is received from courses.php
if (isset($_GET['playlist_id'])) {
    $playlist_id = $_GET['playlist_id'];

    // Fetch playlist title and amount
    $select_playlist = $conn->prepare("SELECT title, price FROM playlist WHERE id = ?");
    $select_playlist->execute([$playlist_id]);
    $playlist = $select_playlist->fetch(PDO::FETCH_ASSOC);

    if ($playlist) {
        $title = $playlist['title'];
        $amount = $playlist['price'];
    } else {
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
    <title>Payment for <?= htmlspecialchars($title); ?></title>
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
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .price {
            font-size: 24px;
            color: #007BFF;
            font-weight: bold;
            text-align: center;
        }
        #paypal-button-container {
            margin: 20px 0;
            display: flex;
            justify-content: center;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Payment for <?= htmlspecialchars($title); ?></h2>
    <p class="price">Price: $<?= number_format($amount, 2); ?></p>
    <div id="paypal-button-container"></div>
</div>

<!-- PayPal SDK -->
<script src="https://www.paypal.com/sdk/js?client-id=AUa1rTPSloRZ4i6DfA85-523yojPriNK1ZL4F1Z6mf0TP5vb1UHPqHEI3nk4tw9eyAcLmsOyTiiO8Hy8"></script>

<script>
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: { value: '<?= number_format($amount, 2, '.', ''); ?>' }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                alert('Transaction completed by ' + details.payer.name.given_name);

                // Get user ID from PHP session
                const userId = <?= isset($_COOKIE['user_id']) ? json_encode($_COOKIE['user_id']) : 'null'; ?>;

                // Directly insert the purchase into the database
                return fetch('insert_purchase.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        playlist_id: <?= $playlist_id; ?>
                    })
                });
            }).then(response => response.json()).then(data => {
                if (data.status === 'success') {
                    window.location.href = 'success.php?playlist_id=<?= $playlist_id; ?>';
                } else {
                    alert('Failed to log payment: ' + data.message);
                }
            }).catch(error => {
                console.error('Error logging payment:', error);
                alert('An error occurred while logging the payment.');
            });
        },
        onError: function(err) {
            console.error('Error during transaction', err);
            alert('An error occurred. Please try again.');
        }
    }).render('#paypal-button-container');
</script>
</body>
</html>
