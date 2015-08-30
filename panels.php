<?php
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
  require_once('panels_query.php');

  // User not searched
  if(!isset($_REQUEST['submitForm'])) {
    $mode = 'search';
    $error = false;
  }
  // User not valid
  else if((!isset($_REQUEST['username'])) || (strlen($_REQUEST['username']) < 4)) {
    $mode = 'search';
    $error = 'Please provide a valid user name';
  }
  // User introduced
  else {
    // If not valid
    $checkUser = check_user($_REQUEST['username']);
    if($checkUser !== true) {
      $mode = 'search';
      $error = $checkUser;
    }
    else {
      // If valid
      $mode = 'toggle';
      $error = false;
      $panel_values = get_panel_values($_REQUEST['username']);
      if($panel_values === false) {
        $error = 'User not found';
        $mode = 'search';
      }
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Panel Assignment - EWatcher Users</title>
    <link rel="shortcut icon" href="favicon.ico" />
    <?php
    if($mode == 'search') {
      echo '<link rel="stylesheet" href="lib/style_search.css">';
    } else {
      echo '<link rel="stylesheet" href="lib/style_toggle.css">';
    }
    ?>
    <link rel="stylesheet" type="text/css" href="lib/sweetalert.css">
    <script src="lib/sweetalert.min.js"></script>
    <script src="lib/jquery-2.1.4.min.js"></script>
  </head>
  <body>
    <?php
    if($mode == 'search') {
      ?>
      <form class="sign-up" action="#" method="post">
        <h1 class="sign-up-title">Panel Assignment</h1>
        <input type="text" class="sign-up-input" name="username" placeholder="Username" autofocus>
        <input type="submit" name="submitForm" value="Search User" class="sign-up-button">
      </form>
    <?php
    } else {
    ?>
    <section class="settings">
      <header class="settings-header">
        <h2>Assign Panels</h2>
      </header>
      <form action="#" method="post" class="settings-form">
        <h3>Username: <span id="targetUsername"><?php echo $_REQUEST['username']; ?></span></h3>
        <label>
          <input class="targetPanel" type="checkbox" name="P1" value="P1" <?php if($panel_values['P1']) echo "checked"?>>
          <span class="settings-switch"><span class="settings-switch-handle"></span></span>
          <strong>Panel #1</strong>
          Consumption
        </label>
        <label>
          <input class="targetPanel" type="checkbox" name="P2" value="P2" <?php if($panel_values['P2']) echo "checked"?>>
          <span class="settings-switch"><span class="settings-switch-handle"></span></span>
          <strong>Panel #2</strong>
          Consumption - Queries
        </label>
        <label>
          <input class="targetPanel" type="checkbox" name="P3" value="P3" <?php if($panel_values['P3']) echo "checked"?>>
          <span class="settings-switch"><span class="settings-switch-handle"></span></span>
          <strong>Panel #3</strong>
          PV
        </label>
        <label>
          <input class="targetPanel" type="checkbox" name="P4" value="P4" <?php if($panel_values['P4']) echo "checked"?>>
          <span class="settings-switch"><span class="settings-switch-handle"></span></span>
          <strong>Panel #4</strong>
          PV - Queries
        </label>
        <label>
          <input class="targetPanel" type="checkbox" name="P5" value="P5" <?php if($panel_values['P5']) echo "checked"?>>
          <span class="settings-switch"><span class="settings-switch-handle"></span></span>
          <strong>Panel #5</strong>
          PV - Daily values
        </label>
      </form>
    </section>
    <?php
    }
    ?>
    <div class="about">
      <p class="about-author">
        <?php if($mode == 'toggle') echo '<a href="panels.php">Back</a> | '; ?>
        <a href="index.php">User Creation</a> | <a href="delete.php">User Deletion</a>
      </p>
    </div>
    <script>
      <?php
        if($error !== false) {
          ?>
            swal({
              title: "Error",
              text: <?php echo '"' . $error . '"' ?>,
              type: "error",
              confirmButtonText: "Continue"
            });
          <?php
        }
      ?>
    </script>
    <script>
      // Make AJAX call on input toggle
      $(".targetPanel").change(function() {
        var setTo = this.checked ? "1" : "0";
        var query = "panels_query.php?user=" + $('#targetUsername').text() + "&togglePanel=" + this.value + "&set=" + setTo;
        $.ajax({
          url: query,
          error: function(xhr, status, error) {
            swal({
              title: "Error",
              text: xhr.responseText,
              type: "error",
              confirmButtonText: "Continue"
            });
            setTimeout(function() {location.reload();}, 3000);
          },
          success: function() {
            this.checked = !this.checked;
            swal({
              title: "Success",
              text: "Panel assigned successfully",
              type: "success",
              confirmButtonText: "Continue"
            });
          }
        });

      });
    </script>
  </body>
</html>
