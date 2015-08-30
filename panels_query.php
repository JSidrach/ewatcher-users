<?php
  // Settings
  require_once('settings.php');

  // AJAX calls, example: paneles_query.php?user=john&togglePanel=P1&set=0
  if(isset($_REQUEST['togglePanel'])) {
    // Check valid username
    if((!isset($_REQUEST['user'])) || (strlen($_REQUEST['user']) < 4) || (strlen($_REQUEST['user']) > 30) || (!ctype_alnum($_REQUEST['user']))) {
      echo 'Uername not valid';
      http_response_code(400);
      exit;
    }
    // Check valid panel
    if(($_REQUEST['togglePanel'] !== 'P1') &&  ($_REQUEST['togglePanel'] !== 'P2') && ($_REQUEST['togglePanel'] !== 'P3') && ($_REQUEST['togglePanel'] !== 'P4') && ($_REQUEST['togglePanel'] !== 'P5')) {
      echo 'Panel name not valid';
      http_response_code(400);
      exit;
    }
    // Check valid set to
    if((!isset($_REQUEST['set'])) || (($_REQUEST['set'] !== '0') && ($_REQUEST['set'] !== '1'))) {
      echo 'Bad panel assignment';
      http_response_code(400);
      exit;
    }

    // Connect to the database
    $connection = new mysqli($db_server, $db_username, $db_password, $db_name);
    if($connection->connect_error) {
      echo 'Error connecting to the database';
      http_response_code(400);
      exit;
    }

    // Get the userid
    $result = $connection->query("SELECT id FROM users WHERE username='" . $_REQUEST['user'] . "';");
    if(($result === FALSE) || (empty($result))) {
      $connection->close();
      echo 'Username does not exist';
      http_response_code(400);
      exit;
    }
    $userid = $result->fetch_object()->id;

    // Toggle panel
    if($connection->query("UPDATE ewatcher SET " . $_REQUEST['togglePanel'] . "=" . $_REQUEST['set'] . " WHERE userid=$userid;") === FALSE) {
      $connection->close();
      echo 'Error while updating EWatcher panel configuration';
      http_response_code(400);
      exit;
    }

    $connection->close();
    http_response_code(200);
    exit;
  }

  // Functions

  // Check if user exists
  //
  // Parameters:
  //   $username: name of the user
  //
  // Returns
  //   true: user exists
  //   false: user does not exist
  function check_user($username) {
    // Global variables
    global $db_server, $db_username, $db_password, $db_name, $schema;

    // Check valid username
    if((!isset($username)) || (strlen($username) < 4) || (strlen($username) > 30) || (!ctype_alnum($username))) {
      return 'Username not valid';
    }

    // Create connection
    $connection = new mysqli($db_server, $db_username, $db_password, $db_name);
    if($connection->connect_error) {
      return 'Error while connecting to the database';
    }

    // Create table if it does not exist
    if(check_table($connection, 'ewatcher', $schema['ewatcher']) === false) {
      $connection->close();
      return 'Error while creating the EWatcher configuration table';
    }

    // Check if user exists in the users table, get userid
    $result = $connection->query("SELECT id FROM users WHERE username='$username';");
    if(($result === FALSE) || (empty($result)) || ($result->num_rows == 0)) {
      $connection->close();
      return 'Username does not exist';
    }
    $userid = $result->fetch_object()->id;

    // Check if user exists in the ewatcher table
    $result = $connection->query("SELECT * FROM ewatcher WHERE userid=$userid;");
    if(($result === FALSE) || (empty($result)) || ($result->num_rows == 0)) {
      // Create ewatcher user config if it does not exist
      if($connection->query("INSERT INTO ewatcher (userid) VALUES ($userid);") === FALSE) {
        $connection->close();
        return 'Error while creating user configuration (EWatcher)';
      }
    }

    $connection->close();
    return true;
  }

  // Get panel values for a given user
  //
  // Parameters:
  //   $username: name of the user
  //
  // Returns
  //   false: error
  //   *array*: array of panel toggles, 'PanelName' => true, or 'PanelName' => false
  function get_panel_values($username) {
    // Global variables
    global $db_server, $db_username, $db_password, $db_name;

    // Create connection
    $connection = new mysqli($db_server, $db_username, $db_password, $db_name);
    if($connection->connect_error) {
      return false;
    }

    // Get panel values
    $result = $connection->query("SELECT id FROM users WHERE username='$username';");
    if(($result === FALSE) || (empty($result)) || ($result->num_rows == 0)) {
      $connection->close();
      return false;
    }
    $userid = $result->fetch_object()->id;
    $result = $connection->query("SELECT * FROM ewatcher WHERE userid=$userid;");
    if(($result === FALSE) || (empty($result)) || ($result->num_rows == 0)) {
      $connection->close();
      return false;
    }
    $userData = $result->fetch_object();

    // Return array of panel values
    for ($i = 1; $i <= 5; $i++) {
      $panelId = "P" . $i;
      $panel[$panelId] = ($userData->$panelId == 1) ? true : false;
    }
    return $panel;
  }

  // Create table if it does not exist
  //
  // Parameters:
  //   $connection: database connection
  //   $table: name of the table
  //   $schema: schema of the table
  //
  // Returns
  //   false: error
  //   true: success
  function check_table($connection, $table, $schema) {
    $connection->set_charset("utf8");
    $result = $connection->query("SHOW TABLES LIKE '$table';");
    if(($result === FALSE) || (empty($result)) || ($result->num_rows == 0)) {
      // Create table
      $createQuery = "CREATE TABLE $table (";
      foreach($schema as $column => $properties) {
        $createQuery .= $column . ' ' . $properties['type'];
        if($properties['type'] == 'text') {
          $createQuery .= ' character set utf8';
        }
        // Not null
        if((isset($properties['Null'])) && ($properties['Null'] == 'NO')) {
          $createQuery .= ' NOT NULL';
        }
        if(isset($properties['default'])) {
          $createQuery .= ' DEFAULT ';
          if(is_string($properties['default'])) {
            $createQuery .= "'" . $properties['default'] . "'";
          } else {
            $createQuery .= $properties['default'];
          }
        }
        // Default value
        $createQuery .= ', ';
      }
      $createQuery = substr($createQuery, 0, -strlen(', '));
      $createQuery .= ');';

      if($connection->query($createQuery) === FALSE) {
        // Error creating the table
        return false;
      }
    }
    return true;
  }
?>
