<?php
include '../components/connect.php';

// Check if the tutor is logged in via cookie
if (isset($_COOKIE['tutor_id'])) {
    $tutor_id = $_COOKIE['tutor_id'];
} else {
    header('location:login.php');
    exit();
}

$message = [];
// Handle form submission
if (isset($_POST['submit'])) {
    // Generate unique ID for the playlist
    $id = unique_id();
    $title = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
    $description = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);
    $status = filter_var(trim($_POST['status']), FILTER_SANITIZE_STRING);

    // Handle thumbnail image upload
    $image = $_FILES['image']['name'];
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $rename = unique_id() . '.' . $ext;
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_files/' . $rename;

    // Get and sanitize the price input
    $price = filter_var(trim($_POST['price']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Validate inputs
    if (empty($title) || empty($description) || empty($status) || empty($rename) || empty($price)) {
        $message[] = 'Please fill all required fields.';
    } elseif ($price < 0) {
        $message[] = 'Price must be a non-negative value.';
    } else {
        // Insert into the playlist table including price
        $add_playlist = $conn->prepare("INSERT INTO `playlist` (id, tutor_id, title, description, thumb, status, price) VALUES (?, ?, ?, ?, ?, ?, ?)");

        // Execute the statement
        if ($add_playlist->execute([$id, $tutor_id, $title, $description, $rename, $status, $price])) {
            // Move the uploaded image to the designated folder
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'New playlist created!';
        } else {
            $message[] = 'Failed to create playlist. Please try again.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Playlist</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="playlist-form">
   <h1 class="heading">Create Playlist</h1>

   <!-- Display messages if any -->
   <?php if (!empty($message)): ?>
       <div class="message">
           <?php foreach ($message as $msg): ?>
               <p><?php echo htmlspecialchars($msg); ?></p>
           <?php endforeach; ?>
       </div>
   <?php endif; ?>

   <form action="" method="post" enctype="multipart/form-data">
      <p>Playlist Status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="" selected disabled>-- select status</option>
         <option value="active">Active</option>
         <option value="deactive">Deactive</option>
      </select>
      <p>Playlist Title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="enter playlist title" class="box">
      <p>Playlist Description <span>*</span></p>
      <textarea name="description" class="box" required placeholder="write description" maxlength="1000" cols="30" rows="10"></textarea>
      <p>Playlist Thumbnail <span>*</span></p>
      <input type="file" name="image" accept="image/*" required class="box">

      <!-- Price Input -->
      <p>Course Price (in USD) <span>*</span></p>
      <input type="number" name="price" required min="0" step="0.01" placeholder="Enter Course Price" class="box">

      <input type="submit" value="create playlist" name="submit" class="btn">
   </form>
</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
