<?php
  // Database settings
  $db_server   = "DB_SERVER";
  $db_name     = "DB_NAME";
  $db_username = "DB_USERNAME";
  $db_password = "DB_PASSWORD";

  // System settings
  $user_zone   = "Europe/Madrid";
  $user_lang   = "es_ES";
  $user_node   = 7;

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
    "units" => array("type" => "text", "Null" => "NO", "default" => "€"),
    "config" => array("type" => "text")
  );
?>
