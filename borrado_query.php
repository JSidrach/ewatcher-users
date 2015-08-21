<?php
  // Settings
  require_once('settings.php');

  // Delete an user, and all its linked data (feeds, inputs)
  //
  // Parameters:
  //   $username: username
  //
  // Returns
  //   true: everything deleted successfully
  //   *anything else*: error string
  function delete_user($username) {
    // Connect to the DB
    $ret = create_connection($connection);
    if($ret !== true) {
      return $ret;
    }

    // Validate input
    $ret = validate_input($username);
    if($ret !== true) {
      $connection->close();
      return $ret;
    }

    // Get user data
    $user_data = get_user_data($username, $connection);
    if($user_data === false) {
      $connection->close();
      return 'El usuario dado no existe';
    }

    // Delete feeds
    if(delete_feeds($user_data, $connection) !== true) {
      $connection->close();
      return 'Error al borrar los feeds';
    }

    // Delete inputs
    if(delete_inputs($user_data, $connection) !== true) {
      $connection->close();
      return 'Error al borrar los inputs';
    }

    // Delete EWatcher panels
    if(delete_ewatcher($user_data, $connection) !== true) {
      $connection->close();
      return 'Error al borrar la configuración de EWatcher';
    }

    // Delete user
    if(delete_user_data($user_data, $connection) !== true) {
      $connection->close();
      return 'Error al borrar los datos del usuario';
    }

    $connection->close();
    return true;
  }

  // Validate the input
  //
  // Parameters:
  //   $username: username
  //
  // Returns
  //   true: parameters valid
  //   *anything else*: error string
  function validate_input($username) {
    // Username
    if((!isset($username)) || (strlen($username) == 0)) {
      return 'Introduzca un nombre de usuario';
    }
    if(!ctype_alnum($username)) {
      return 'El nombre de usuario solo puede contener letras y números';
    }
    if(strlen($username) < 4) {
      return 'El nombre de usuario debe tener al menos 4 caracteres';
    }
    if(strlen($username) > 30) {
      return 'El nombre de usuario no puede tener más de 30 caracteres';
    }

    return true;
  }

  // Create a connection with the database
  //
  // Parameters:
  //   $connection: output parameter, connection
  //
  // Returns
  //   true: connection successfully created
  //   *anything else*: error string
  function create_connection(&$connection) {
    // Global variables
    global $db_server, $db_username, $db_password, $db_name;

    $connection = new mysqli($db_server, $db_username, $db_password, $db_name);
    if($connection->connect_error) {
      return false;
    }
    return true;
  }

  // Gets user data
  //
  // Parameters:
  //   $username: name of the user
  //   $connection: database connection
  //
  // Returns
  //   false: error fetching user data
  //   *object*: user data
  function get_user_data($username, $connection) {
    // Get user
    $result = $connection->query("SELECT * FROM users WHERE username='$username';");
    if($result->num_rows == 0) {
      return false;
    }

    return $result->fetch_object();
  }

  // Delete user feeds
  //
  // Parameters:
  //   $user_data: user data as in the database
  //   $connection: database connection
  //
  // Returns
  //   true: feeds deleted successfully
  //   false: error deleting feeds
  function delete_feeds($user_data, $connection) {
    // Global variables
    global $base_url;

    $result = $connection->query("SELECT * FROM feeds WHERE userid=$user_data->id;");
    if($result === FALSE) {
      return false;
    }
    while ($feed = $result->fetch_assoc()) {
      $feedId = $feed["id"];
      $url = "$base_url/feed/delete.json?id=$feedId&apikey=$user_data->apikey_write";
      $queryResult = json_decode(file_get_contents($url), true);
      if($queryResult === false) {
        return false;
      }
    }
    return true;
  }

  // Delete user inputs
  //
  // Parameters:
  //   $user_data: user data as in the database
  //   $connection: database connection
  //
  // Returns
  //   true: inputs deleted successfully
  //   false: error deleting inputs
  function delete_inputs($user_data, $connection) {
    // Global variables
    global $base_url;

    $result = $connection->query("SELECT * FROM input WHERE userid=$user_data->id;");
    if($result === FALSE) {
      return false;
    }
    while ($input = $result->fetch_assoc()) {
      $inputId = $input["id"];
      $url = "$base_url/input/delete.json?inputid=$inputId&apikey=$user_data->apikey_write";
      $queryResult = json_decode(file_get_contents($url), true);
      if($queryResult === false) {
        return false;
      }
    }
    return true;
  }

  // Delete EWacther user configuration
  //
  // Parameters:
  //   $user_data: user data as in the database
  //   $connection: database connection
  //
  // Returns
  //   true: ewatcher configuration deleted successfully
  //   false: error deleting ewatcher configuration
  function delete_ewatcher($user_data, $connection) {
    $result = $connection->query("SHOW TABLES LIKE 'ewatcher';");
    if($result->num_rows !== 0) {
      if($connection->query("DELETE FROM ewatcher WHERE userid=$user_data->id;") !== TRUE) {
        return false;
      }
    }
    return true;
  }

  // Delete user data
  //
  // Parameters:
  //   $user_data: user data as in the database
  //   $connection: database connection
  //   $redis: redis connection
  //
  // Returns
  //   true: user deleted successfully
  //   false: error deleting user
  function delete_user_data($user_data, $connection) {
    if($connection->query("DELETE FROM users WHERE id=$user_data->id;") !== TRUE) {
      return false;
    }
    return true;
  }
?>
