<?php
include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Courses</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- courses section starts  -->


<section class="courses">

   <h1 class="heading">Welcome to the Course Platform</h1>



   <div class="box-container">
      <div class="box tutor">
         <h3 class="title">AI Speech Recognition</h3>
         <p>Use Voice Commands To Navigate Through Website!</p>
         <button id="voice-recognition-btn" class="inline-btn">
            <h3 id="button-text">Enable Voice</h3>
         </button>
      </div>



      <?php
         $select_courses = $conn->prepare("SELECT * FROM playlist WHERE status = ? ORDER BY date DESC");
         $select_courses->execute(['active']);
         if($select_courses->rowCount() > 0){
            while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)){
               $course_id = $fetch_course['id'];

               $select_tutor = $conn->prepare("SELECT * FROM tutors WHERE id = ?");
               $select_tutor->execute([$fetch_course['tutor_id']]);
               $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
      ?>


      <div class="box">
         <div class="tutor">
            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
            <div>
               <h3><?= htmlspecialchars($fetch_tutor['name']); ?></h3>
               <span><?= htmlspecialchars($fetch_course['date']); ?></span>
            </div>
         </div>
         <img src="uploaded_files/<?= $fetch_course['thumb']; ?>" class="thumb" alt="">
         <h3 class="title"><?= htmlspecialchars($fetch_course['title']); ?></h3>
         <div>
         <h1 class="price"><?= htmlspecialchars($fetch_course['price']); ?> USD</h1> <!-- Displaying course price -->
         </div>
         <a href="playlist.php?get_id=<?= $course_id; ?>" class="inline-btn">View Playlist</a>
         <a href="payment.php?playlist_id=<?= $course_id; ?>" class="inline-btn buy-now">Buy Now</a> <!-- Buy Now button -->
      </div>
      <?php
            }
         } else {
            echo '<p class="empty">No courses added yet!</p>';
         }
      ?>

   </div>

</section>

<!-- courses section ends -->

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
<script src="js/voice-navigation.js"></script>
   
</body>
</html>
