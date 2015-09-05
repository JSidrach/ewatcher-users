<?php
  // Database settings
  $db_server   = "DB_SERVER";
  $db_name     = "DB_NAME";
  $db_username = "DB_USERNAME";
  $db_password = "DB_PASSWORD";

  // Emoncms settings
  $user_zone   = "Europe/Madrid";
  $user_lang   = "es_ES";
  $user_node   = 7;
  // Feed default interval (in seconds)
  $user_interval = 10;

  // Base url
  $base_url    = "http://localhost/emoncms/";

  // Redis
  $redis_enabled = false;
  $redis_server = "127.0.0.1";

  // User profiles (for each key, the following files must exist: data/key_inputs.json, data/key_feeds.json, data/key_processes.json)
  $user_profiles = array(
    "pv" => "PV",
    "consumption" => "Consumption"
  );

  // eWatcher table schema
  $schema['ewatcher'] = array(
    "userid" => array("type" => "int(11)", "Null" => "NO"),
    "P1" => array("type" => "tinyint(1)", "Null" => "NO", "default" => 0),
    "P2" => array("type" => "tinyint(1)", "Null" => "NO", "default" => 0),
    "P3" => array("type" => "tinyint(1)", "Null" => "NO", "default" => 0),
    "P4" => array("type" => "tinyint(1)", "Null" => "NO", "default" => 0),
    "P5" => array("type" => "tinyint(1)", "Null" => "NO", "default" => 0),
    "cIn" => array("type" => "float", "Null" => "NO", "default" => 0.1244),
    "cOut" => array("type" => "float", "Null" => "NO", "default" => 0.054),
    "units" => array("type" => "char(8)", "Null" => "NO", "default" => "€")
  );
?>
