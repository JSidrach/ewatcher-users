<?php
  error_reporting(E_ALL);
  ini_set("display_errors", 1);

  // Settings
  require_once('settings.php');

  // Form not submitted yet
  if (!isset($_REQUEST['submitForm'])){
    $result = false;
  }
  // Form submitted
  else {
    // Create user, feeds and inputs
    require_once('query.php');
    $result = create_linked_user($_REQUEST['username'], $_REQUEST['email'], $_REQUEST['password'], $_REQUEST['panelType']);
    // Clear the form if the action has completed successfully
    if($result === true) {
      $_REQUEST = array();
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>User Creation - EWatcher Users</title>
    <link rel="shortcut icon" href="favicon.ico" />
    <link rel="stylesheet" href="lib/style_index.css">
    <link rel="stylesheet" type="text/css" href="lib/sweetalert.css">
    <script src="lib/sweetalert.min.js"></script>
  </head>
<body>
  <h1 class="register-title">Fill the data</h1>
  <form class="register" action="#" method="post">
    <div class="register-switch" style="height: <?php echo strval(32*sizeof($user_profiles)) . "px"; ?>;">
      <?php
        $i = 0;
        foreach($user_profiles as $key => $value) {
          echo "<input type='radio' name='panelType' value='$key' id='$key' class='register-switch-input'";
          if(isset($_REQUEST['panelType'])) {
            if($_REQUEST['panelType'] === $key) {
              echo 'checked';
            }
          } else {
            if($i == 0) {
              echo 'checked';
            }
          }
          $i++;
          echo "><label for='$key' class='register-switch-label'>$value</label>";
        }
      ?>
    </div>
    <input type="username" name="username" class="register-input" placeholder="User Name" <?php if(isset($_REQUEST['username'])) echo 'value="' . $_REQUEST['username']. '"'; ?>>
    <input type="email" name="email" class="register-input" placeholder="E-Mail" <?php if(isset($_REQUEST['email'])) echo 'value="' . $_REQUEST['email']. '"'; ?>>
    <input type="password" name="password" class="register-input" placeholder="Password">
    <input type="submit" name="submitForm" value="Create Account" class="register-button">
  </form>
  <div class="about">
    <p class="about-author">
      <a href="panels.php">Panel Assignment</a>  | <a href="delete.php">User Deletion</a>
    </p>
  </div>
  <script>
    <?php
      if($result === true) {
        ?>
          swal({
            title: "Success",
            text: "User, inputs and feeds successfully created",
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
