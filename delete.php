<?php
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
  // Form not submitted yet
  if (!isset($_REQUEST['submitForm'])){
    $result = false;
  }
  // Form submitted
  else {
    // Delete user, feeds and inputs
    require_once('delete_query.php');
    $result = delete_user($_REQUEST['username']);
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>User Deletion - EWatcher Users</title>
    <link rel="shortcut icon" href="favicon.ico" />
    <link rel="stylesheet" href="lib/style_search.css">
    <link rel="stylesheet" type="text/css" href="lib/sweetalert.css">
    <script src="lib/sweetalert.min.js"></script>
    <script src="lib/jquery-2.1.4.min.js"></script>
  </head>
  <body>
    <form class="sign-up" action="#" method="post">
      <h1 class="sign-up-title">User Deletion</h1>
      <input type="text" class="sign-up-input" name="username" placeholder="Username" autofocus>
      <input type="submit" name="submitForm" value="Delete User" class="sign-up-button">
    </form>
    <div class="about">
      <p class="about-author">
        <a href="index.php">User Creation</a> | <a href="panels.php">Panel Assignment</a>
      </p>
    </div>
    <script>
      <?php
        if($result === true) {
          ?>
            swal({
              title: "Success",
              text: "User, inputs and feeds successfully deleted",
              type: "success",
              confirmButtonText: "Continue"
            });
          <?php
          echo '';
        } else if($result === false) {
          // Show nothing
        } else {
          ?>
            swal({
              title: "Error",
              text: <?php echo '"' . $result . '"' ?>,
              type: "error",
              confirmButtonText: "Continue"
            });
          <?php
        }
      ?>
    </script>
  </body>
</html>
