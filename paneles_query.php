<?php
  // Settings
  require_once('settings.php');

  // AJAX calls
  if(isset($_REQUEST['togglePanel'])) {
    // Toggle panel

    //http_response_code(400);
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
    // Check if user exists
    // TODO
    // TODO: Create panels, all to false
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
    // Get panel values

    // If no row has been created for this user, create it now

    // Return array of panel values
    $panel['P1'] = true;
    $panel['P2'] = false;
    $panel['P3'] = true;
    $panel['P4'] = false;

    return $panel;
  }
?>
