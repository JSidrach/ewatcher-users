<?php
  // Settings
  require_once('settings.php');
  // Emoncms definitions
  require_once('defs_emoncms.php');

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
      end_connection($connection, true);
      return $ret;
    }

    // Create user
    if(create_user($username, $email, $password, $userid, $connection) !== true) {
      end_connection($connection, true);
      return 'El nombre de usuario ya existe';
    }

    // Set the type of user data
    $prefix = 'data/';
    // PV
    if($panelType == 'PV') {
      $prefix .= 'pv';
    }
    // Consumption
    else {
      $prefix .= 'consumption';
    }

    // Create feeds
    if(create_feeds($prefix . '_feeds.json', $userid, $feeds, $connection) !== true) {
      end_connection($connection, true);
      return 'Fallo al crear los feeds';
    }

    // Create inputs
    if(create_inputs($prefix . '_inputs.json', $userid, $inputs, $connection) !== true) {
      end_connection($connection, true);
      return 'Fallo al crear los inputs';
    }

    // Create processes
    if(create_processes($prefix . '_processes.json', $feeds, $inputs, $connection) !== true) {
      end_connection($connection, true);
      return 'Fallo al crear los procesos';
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
      return true;
    }
    // Disable autocommit (begin transaction)
    $connection->autocommit(FALSE);
    return true;
  }

  // Ends the connection
  //
  // Parameters:
  //   $error: if true, the changes will be rolled back, otherwise, they will be commited
  //   $connection: connection to the database
  function end_connection($error, &$connection) {
    // Error, rollback changes
    if($error === true) {
      $connection->rollback();
    }
    // No error, commit the changes
    else {
      $connection->commit();
    }
    $connection->autocommit(TRUE);
    $connection->close();
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
    // Global variables
    global $user_zone, $user_lang;

    // Rest of the parameters
    $hash = hash('sha256', $password);
    $string = md5(uniqid(mt_rand(), true));
    $salt = substr($string, 0, 3);
    $hash = hash('sha256', $salt . $hash);
    $apikey_write = md5(uniqid(mt_rand(), true));
    $apikey_read = md5(uniqid(mt_rand(), true));

    // Query
    $sqlQuery = "INSERT INTO users (username, password, email, salt ,apikey_read, apikey_write, admin, timezone, language)
                 VALUES ('$username', '$hash', '$email', '$salt', '$apikey_read', '$apikey_write', 0, '$user_zone', '$user_lang');";
    if ($connection->query($sqlQuery) === FALSE) {
      return false;
    }

    // Asign userid
    $userid = $connection->insert_id;
    return true;
  }

  // Create the feeds
  //
  // Parameters:
  //   $datafile: path to the feeds data
  //   $userid: id of the user
  //   $feeds: output parameter for the feeds, in the format 'feedName'=>'feedId'
  //   $connection: connection with the database
  //
  // Returns
  //   true: feeds successfully created
  //   false: error creating the feeds
  function create_feeds($datafile, $userid, &$feeds, $connection) {
    // Read the feeds from file
    $feedArray = json_decode(file_get_contents($datafile));

    // Create each feed
    foreach($feedArray as $feed) {
      // Query
      $datatype = get_type_id($feed->type);
      $engine = get_engine_id($feed->engine);
      $sqlQuery = "INSERT INTO feeds (userid, name, tag, datatype, public, engine)
                   VALUES ($userid, '$feed->name', '$feed->description', $datatype, 0, $engine);";
      if ($connection->query($sqlQuery) === FALSE) {
        return false;
      }
      // Assign the created feed id to the feeds array
      $feeds[$feed->name] = $connection->insert_id;
    }

    return true;
  }

  // Create the inputs
  //
  // Parameters:
  //   $datafile: path to the inputs data
  //   $userid: id of the user
  //   $inputs: output parameter for the inputs, in the format 'inputName'=>'inputId'
  //   $connection: connection with the database
  //
  // Returns
  //   true: inputs successfully created
  //   false: error creating the inputs
  function create_inputs($datafile, $userid, &$inputs, $connection) {
    // Global variables
    global $user_node;

    // Create the inputs from file
    $inputArray = json_decode(file_get_contents($datafile));

    // Create each input
    foreach($inputArray as $input) {
      // Query
      $sqlQuery = "INSERT INTO input (userid, name, description, nodeid)
                   VALUES ($userid, '$input->name', '$input->description', $user_node);";
      if ($connection->query($sqlQuery) === FALSE) {
        return false;
      }
      // Assign the created input id to the feeds array
      $inputs[$input->name] = $connection->insert_id;
    }
    return true;
  }

  // Create the processes
  //
  // Parameters:
  //   $datafile: path to the processes data
  //   $feeds: array of feed ids, in the format 'feedName'=>'feedId'
  //   $inputs: array of input ids, in the format 'inputName'=>'inputId'
  //   $connection: connection with the database
  //
  // Returns
  //   true: processes successfully created
  //   false: error creating the processes
  function create_processes($datafile, $feeds, $inputs, $connection) {
    // Read the processes from file
    $processArray = json_decode(file_get_contents($datafile));

    // Create each process
    foreach($processArray as $process) {
      $inputId = $inputs[$process->input];

      // Translate process
      $processesStrings = array();
      foreach($process->processes as $function) {
        // Translate function
        $functionId = get_function_id($function->function);

        // Arguments
        if(!empty($function->arguments)) {
          $argumentsArray = array();
          foreach($function->arguments as $argument) {
            // Get the id of the feed name
            if($argument->type == "feed") {
              $argumentsArray[] = $feeds[$argument->value];
            }
            // Get the id of the input name
            else if($argument->type == "input") {
              $argumentsArray[] = $inputs[$argument->value];
            }
            // Get the value
            else {
              $argumentsArray[] = strval($argument->value);
            }
          }
          $arguments = implode(':', $argumentsArray);
        } else {
          $arguments = "0";
        }
        $translatedFunction = strval($functionId) . ":$arguments";

        // Add the translated string
        $processesStrings[] = $translatedFunction;
      }
      $processes = implode(',', $processesStrings);

      // Query
      $sqlQuery = "UPDATE input SET processList='$processes' WHERE id=$inputId;";
      if ($connection->query($sqlQuery) === FALSE) {
        return false;
      }
    }

    return true;
  }
?>
