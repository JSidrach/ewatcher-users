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
  //   $panelType: type of the panel
  //
  // Returns
  //   true: user, feeds and inputs successfully created
  //   *anything else*: error string
  function create_linked_user($username, $email, $password, $panelType) {
    // Global variables
    global $redis_enabled, $redis_server;

    // Connect to the DB
    $ret = create_connection($connection);
    if($ret !== true) {
      return $ret;
    }

    // Connect to Redis
    if($redis_enabled === true) {
      $redis = new Redis();
      if(!$redis->connect($redis_server)) {
        $redis = false;
      }
    } else {
      $redis = false;
    }

    // Validate input
    $ret = validate_input($username, $email, $password, $panelType);
    if($ret !== true) {
      end_connection(true, $connection);
      return $ret;
    }

    // Create user
    if(create_user($username, $email, $password, $userid, $apikey, $connection) !== true) {
      end_connection(true, $connection);
      return 'Username already exists';
    }

    // Set the type of user profile
    $prefix = 'data/' . $panelType;

    // Create feeds
    if(create_feeds($prefix . '_feeds.json', $feeds, $apikey) !== true) {
      end_connection(true, $connection);
      return 'Error while creating the feeds';
    }

    // Create inputs
    if(create_inputs($prefix . '_inputs.json', $userid, $inputs, $connection, $redis) !== true) {
      end_connection(true, $connection);
      return 'Error while creating the inputs';
    }

    // Create processes
    if(create_processes($prefix . '_processes.json', $feeds, $inputs, $apikey) !== true) {
      end_connection(true, $connection);
      return 'Error while creating the processes';
    }

    end_connection(false, $connection);
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
    // Global variables
    global $user_profiles;

    // Username
    if((!isset($username)) || (strlen($username) == 0)) {
      return 'Please provide an username';
    }
    if(!ctype_alnum($username)) {
      return 'Username must contain only letters and numbers';
    }
    if(strlen($username) < 4) {
      return 'Username must have at least 4 characters';
    }
    if(strlen($username) > 30) {
      return 'Username cannot have more than 30 characters';
    }

    // Email
    if((!isset($email)) || (strlen($email) == 0)) {
      return 'Please provide an email';
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return 'Please provide a valid email';
    }

    // Password
    if((!isset($password)) || (strlen($password) == 0)) {
      return 'Please provide a password';
    }
    if(strlen($password) < 4) {
      return 'Password must have at least 4 characters';
    }
    if(strlen($password) > 30) {
      return 'Password cannot have more than 30 characters';
    }

    // Panel Type
    if((!isset($panelType)) || (strlen($panelType) == 0)) {
      return 'Select a type of profile';
    }
    if(!isset($user_profiles[$panelType])) {
      return 'Please select a valid profile';
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
  //   $apikey: output parameter, write API key of the user
  //   $connection: connection with the database
  //
  // Returns
  //   true: user successfully created
  //   false: error creating the user (already exists)
  function create_user($username, $email, $password, &$userid, &$apikey, $connection) {
    // Global variables
    global $user_zone, $user_lang;

    // Search if user exists
    if($connection->query("SELECT * FROM users WHERE username='$username';")->num_rows != 0) {
      return false;
    }

    // Rest of the parameters
    $hash = hash('sha256', $password);
    $salt = md5(uniqid(mt_rand(), true));
    $password = hash('sha256', $salt . $hash);

    $apikey_write = md5(uniqid(mt_rand(), true));
    $apikey_read = md5(uniqid(mt_rand(), true));
    $apikey = $apikey_write;

    // Query
    $sqlQuery = "INSERT INTO users (username, password, email, salt ,apikey_read, apikey_write, admin, timezone, language)
                 VALUES ('$username', '$password', '$email', '$salt', '$apikey_read', '$apikey_write', 0, '$user_zone', '$user_lang');";
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
  //   $feeds: output parameter for the feeds, in the format 'feedName'=>'feedId'
  //   $apikey: write API key
  //
  // Returns
  //   true: feeds successfully created
  //   false: error creating the feeds
  function create_feeds($datafile, &$feeds, $apikey) {
    // Global variables
    global $base_url;

    // Read the feeds from file
    $feedArray = json_decode(file_get_contents($datafile));

    // Create each feed
    foreach($feedArray as $feed) {
      // Query
      $datatype = get_type_id($feed->type);
      $engine = get_engine_id($feed->engine);
      $url = str_replace(' ', '%20', $base_url . "/feed/create.json?tag=$feed->description&name=$feed->name&datatype=$datatype&engine=$engine&apikey=$apikey&options={\"interval\":10}");
      $result = json_decode(file_get_contents($url), true);
      if($result["success"] !== true) {
        return false;
      }

      // Assign the created feed id to the feeds array
      $feeds[$feed->name] = $result["feedid"];
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
  //   $redis: redis connection
  //
  // Returns
  //   true: inputs successfully created
  //   false: error creating the inputs
  function create_inputs($datafile, $userid, &$inputs, $connection, $redis) {
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
      $inputId = $connection->insert_id;
      $inputs[$input->name] = $inputId;

      // Redis query
      if($redis !== false) {
        $redis->sAdd("user:inputs:$userid", $inputId);
        $redis->hMSet("input:$inputId",array('id'=>$inputId,'nodeid'=>$user_node,'name'=>$input->name,'description'=>$input->description, 'processList'=>""));
      }
    }

    return true;
  }

  // Create the processes
  //
  // Parameters:
  //   $datafile: path to the processes data
  //   $feeds: array of feed ids, in the format 'feedName'=>'feedId'
  //   $inputs: array of input ids, in the format 'inputName'=>'inputId'
  //   $apikey: write API key
  //
  // Returns
  //   true: processes successfully created
  //   false: error creating the processes
  function create_processes($datafile, $feeds, $inputs, $apikey) {
    // Global variables
    global $base_url;

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
      $processes = implode(",", $processesStrings);

      // Query
      $result = file_get_contents("$base_url/input/process/set.json?inputid=$inputId&processlist=$processes&apikey=$apikey");
      if(($result == "false") || ($result === FALSE)) {
        return false;
      }
    }
    return true;
  }
?>
