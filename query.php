<?php
  // Create an user, asign feeds and inputs
  //
  // Parameters:
  //   $username: username
  //   $email: email of the user
  //   $password: password of the user
  //   $panelType: type of the panel ("PV" or "Consumption")
  //
  // Returns
  //   true: user, feeds and inputs successfully created
  //   *anything else*: error string
  function create_linked_user($username, $email, $password, $panelType) {
    // Connect to the DB
    $ret = create_connection($connection);
    if($ret !== true) {
      return $ret;
    }

    // Validate input
    $ret = validate_input($username, $email, $password, $panelType);
    if($ret !== true) {
      $connection->close();
      return $ret;
    }

    // Create user
    if(create_user($username, $email, $password, $userid, $connection) !== true) {
      $connection->close();
      return 'El nombre de usuario ya existe';
    }

    // Create feeds&inputs
    // PV
    if($panelType == 'PV') {
      // Feeds
      if(create_feeds_pv($userid, $feeds, $connection) !== true) {
        $connection->close();
        return 'Fallo al crear los feeds';
      }

      // Inputs
      if(create_inputs_pv($userid, $feeds, $connection) !== true) {
        $connection->close();
        return 'Fallo al crear los inputs';
      }
    }

    // Consumption
    else if($panelType == 'Consumption') {
      // Feeds
      if(create_feeds_consumption($userid, $feeds, $connection) !== true) {
        $connection->close();
        return 'Fallo al crear los feeds';
      }

      // Inputs
      if(create_inputs_consumption($userid, $feeds, $connection) !== true) {
        $connection->close();
        return 'Fallo al crear los inputs';
      }
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
    // Get DB settings
    require_once('settings.php');

    $connection = new mysqli($db_server, $db_username, $db_password, $db_name);
    if($connection->connect_error) {
      return true;
    }
    return true;
  }

  // Validate the input
  //
  // Parameters:
  //   $username: username
  //   $email: email of the user
  //   $password: password of the user
  //   $panelType: type of the panel ("PV" or "Consumption")
  //
  // Returns
  //   true: parameters valid
  //   *anything else*: error string
  function validate_input($username, $email, $password, $panelType) {
    // Username
    if((!isset($username)) || (strlen($username) == 0)) {
      echo $username;
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

    // Email
    if((!isset($email)) || (strlen($email) == 0)) {
      return 'Introduzca un email';
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return 'Email no válido';
    }

    // Password
    if((!isset($password)) || (strlen($password) == 0)) {
      return 'Introduzca una contraseña';
    }
    if(strlen($password) < 4) {
      return 'La contraseña debe tener al menos 4 caracteres';
    }
    if(strlen($password) > 30) {
      return 'La contraseña no puede tener más de 30 caracteres';
    }

    // Panel Type
    if((!isset($panelType)) || (strlen($panelType) == 0)) {
      return 'Seleccione un tipo de instalación';
    }
    if(!($panelType === 'PV' || $panelType === 'Consumption')) {
      return 'Seleccione un tipo válido de instalación';
    }
    return true;
  }

  // Create the user
  //
  // Parameters:
  //   $username: username, must be unique
  //   $email: email of the user
  //   $password: password of the user
  //   $userid: output parameter, id of the user created
  //   $connection: connection with the database
  //
  // Returns
  //   true: user successfully created
  //   false: error creating the user (already exists)
  function create_user($username, $email, $password, &$userid, $connection) {
    // TODO
    // Europe/Madrid | es_ES
    /*        // If we got here the username, password and email should all be valid
        $hash = hash('sha256', $password);
        $string = md5(uniqid(mt_rand(), true));
        $salt = substr($string, 0, 3);
        $hash = hash('sha256', $salt . $hash);
        $apikey_write = md5(uniqid(mt_rand(), true));
        $apikey_read = md5(uniqid(mt_rand(), true));
        if (!$this->mysqli->query("INSERT INTO users ( username, password, email, salt ,apikey_read, apikey_write, admin ) VALUES ( '$username' , '$hash', '$email', '$salt', '$apikey_read', '$apikey_write', 0 );")) {
            return array('success'=>false, 'message'=>_("Error creating user"));
        }*/
    return true;
  }

  // Create the PV feeds
  //
  // Parameters:
  //   $userid: id of the user
  //   $feeds: output parameter for the feeds, in the format 'feedName'=>'feedID'
  //   $connection: connection with the database
  //
  // Returns
  //   true: feeds successfully created
  //   false: error creating the feeds
  function create_feeds_pv($userid, &$feeds, $connection) {
    // TODO
    return true;
  }

  // Create the PV inputs
  //
  // Parameters:
  //   $userid: id of the user
  //   $feeds: feeds id array, in the format 'feedName'=>'feedID'
  //   $connection: connection with the database
  //
  // Returns
  //   true: inputs successfully created
  //   false: error creating the inputs
  function create_inputs_pv($userid, $feeds, $connection) {
    // TODO
    return true;
  }

  // Create the consumption feeds
  //
  // Parameters:
  //   $userid: id of the user
  //   $feeds: output parameter for the feeds, in the format 'feedName'=>'feedID'
  //   $connection: connection with the database
  //
  // Returns
  //   true: feeds successfully created
  //   false: error creating the feeds
  function create_feeds_consumption($userid, &$feeds, $connection) {
    // TODO
    return true;
  }

  // Create the PV inputs
  //
  // Parameters:
  //   $userid: id of the user
  //   $feeds: feeds id array, in the format 'feedName'=>'feedID'
  //   $connection: connection with the database
  //
  // Returns
  //   true: inputs successfully created
  //   false: error creating the inputs
  function create_inputs_consumption($userid, $feeds, $connection) {
    // TODO
    return true;
  }
?>
