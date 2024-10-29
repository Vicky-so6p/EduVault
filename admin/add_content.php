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
    $id = unique_id();
    $status = filter_var(trim($_POST['status']), FILTER_SANITIZE_STRING);
    $title = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
    $description = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);
    $playlist = filter_var(trim($_POST['playlist']), FILTER_SANITIZE_STRING);
    
    // Get and sanitize the is_free input
    $is_free = filter_var($_POST['is_free'], FILTER_SANITIZE_NUMBER_INT); // Sanitize the input

    // Handle thumbnail image upload
    $thumb = $_FILES['thumb']['name'];
    $thumb_ext = pathinfo($thumb, PATHINFO_EXTENSION);
    $rename_thumb = unique_id() . '.' . $thumb_ext;
    $thumb_size = $_FILES['thumb']['size'];
    $thumb_tmp_name = $_FILES['thumb']['tmp_name'];
    $thumb_folder = '../uploaded_files/' . $rename_thumb;

    // Handle video upload
    $video = $_FILES['video']['name'];
    $video_ext = pathinfo($video, PATHINFO_EXTENSION);
    $rename_video = unique_id() . '.' . $video_ext;
    $video_tmp_name = $_FILES['video']['tmp_name'];
    $video_folder = '../uploaded_files/' . $rename_video;

    // Validate file sizes
    //if ($thumb_size > 2000000) {
    //   $message[] = 'Thumbnail image size is too large! Maximum size is 2MB.';
    //} elseif ($_FILES['video']['size'] > 20000000) { // Assuming max video size is 20MB
    //    $message[] = 'Video size is too large! Maximum size is 20MB.';
    //} else {
        // Insert the content with is_free status
        $add_content = $conn->prepare("INSERT INTO `content`(id, tutor_id, playlist_id, title, description, video, thumb, status, is_free) VALUES(?,?,?,?,?,?,?,?,?)");
        $add_content->execute([$id, $tutor_id, $playlist, $title, $description, $rename_video, $rename_thumb, $status, $is_free]);

        // Move uploaded files
        move_uploaded_file($thumb_tmp_name, $thumb_folder);
        move_uploaded_file($video_tmp_name, $video_folder);
        $message[] = 'New course uploaded!';
    }
//}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Upload Content</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="video-form">
   <h1 class="heading">Upload Content</h1>

   <?php if (!empty($message) && is_array($message)): ?>
        <div class="message">
            <?php foreach ($message as $msg): ?>
                <p><?php echo htmlspecialchars($msg); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>


   <form action="" method="post" enctype="multipart/form-data">
      <p>Video Status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="" selected disabled>-- select status</option>
         <option value="active">Active</option>
         <option value="deactive">Deactive</option>
      </select>
      <p>Video Title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="enter video title" class="box">
      <p>Video Description <span>*</span></p>
      <textarea name="description" class="box" required placeholder="write description" maxlength="1000" cols="30" rows="10"></textarea>
      <p>Video Playlist <span>*</span></p>
      <select name="playlist" class="box" required>
         <option value="" disabled selected>--select playlist</option>
         <?php
         $select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
         $select_playlists->execute([$tutor_id]);
         if ($select_playlists->rowCount() > 0) {
            while ($fetch_playlist = $select_playlists->fetch(PDO::FETCH_ASSOC)) {
         ?>
         <option value="<?= $fetch_playlist['id']; ?>"><?= htmlspecialchars($fetch_playlist['title']); ?></option>
         <?php
            }
         } else {
            echo '<option value="" disabled>no playlist created yet!</option>';
         }
         ?>
      </select>
      <p>Select Thumbnail <span>*</span></p>
      <input type="file" name="thumb" accept="image/*" required class="box">
      <p>Select Video <span>*</span></p>
      <input type="file" name="video" accept="video/*" required class="box">
      
      <!-- Is Free Input -->
      <p>Is Free? <span>*</span></p>
      <select name="is_free" class="box" required>
         <option value="0" selected>Paid</option>
         <option value="1">Free</option>
      </select>

      <input type="submit" value="upload video" name="submit" class="btn">
   </form>

</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
