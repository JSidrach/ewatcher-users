<?php
  //
  // Auxiliary definition functions. Modify in case emoncms definitions change
  //

  // Gets the type of feed
  //
  // Parameters:
  //   $type: string with the type of feed ('REALTIME' or 'DAILY')
  //
  // Returns
  //   id of the feed's type (number)
  function get_type_id($type) {
    // Definitions
    $types['REALTIME'] = 1;
    $types['DAILY'] = 2;

    // Query
    return $types[$type];
  }

  // Gets the type of feed
  //
  // Parameters:
  //   $type: string with the type of engine ('PHPTIMESERIES', 'PHPFINE' or 'PHPFIWA')
  //
  // Returns
  //   id of the engine (number)
  function get_engine_id($engine) {
    // Definitions
    $engines['PHPTIMESERIES'] = 2;
    $engines['PHPFINA'] = 5;
    $engines['PHPFIWA'] = 6;

    // Query
    return $engines[$engine];
  }

  // Gets the function id
  //
  // Parameters:
  //   $type: string with the function name
  //
  // Returns
  //   id of the function (number)
  function get_function_id($function) {
    // Definitions
    $functions['log_to_feed'] = 1;
    $functions['scale'] = 2;
    $functions['offset'] = 3;
    $functions['power_to_kwh'] = 4;
    $functions['power_to_kwhd'] = 5;
    $functions['times_input'] = 6;
    $functions['input_ontime'] = 7;
    $functions['kwhinc_to_kwhd'] = 8;
    $functions['kwh_to_kwhd_old'] = 9;
    $functions['update_feed_data'] = 10;
    $functions['add_input'] = 11;
    $functions['divide_input'] = 12;
    $functions['phaseshift'] = 13;
    $functions['accumulator'] = 14;
    $functions['ratechange'] = 15;
    $functions['histogram'] = 16;
    $functions['average'] = 17;
    $functions['heat_flux'] = 18;
    $functions['power_acc_to_kwhd'] = 19;
    $functions['pulse_diff'] = 20;
    $functions['kwh_to_power'] = 21;
    $functions['subtract_input'] = 22;
    $functions['kwh_to_kwhd'] = 23;
    $functions['allowpositive'] = 24;
    $functions['allownegative'] = 25;
    $functions['signed2unsigned'] = 26;
    $functions['max_value'] = 27;
    $functions['min_value'] = 28;
    $functions['add_feed'] = 29;
    $functions['sub_feed'] = 30;
    $functions['multiply_by_feed'] = 31;
    $functions['divide_by_feed'] = 32;
    $functions['reset2zero'] = 33;
    $functions['wh_accumulator'] = 34;
    $functions['publish_to_mqtt'] = 35;
    $functions['reset_to_NULL'] = 36;
    $functions['reset_to_original'] = 37;
    $functions['if_!schedule,_zero'] = 38;
    $functions['if_!schedule,_NULL'] = 39;
    $functions['if_schedule,_zero'] = 40;
    $functions['if_schedule,_NULL'] = 41;
    $functions['if_zero,_skip_next'] = 42;
    $functions['if_!zero,_skip_next'] = 43;
    $functions['if_NULL,_skip_next'] = 44;
    $functions['if_!NULL,_skip_next'] = 45;
    $functions['if_>,_skip_next'] = 46;
    $functions['if_>=,_skip_next'] = 47;
    $functions['if_<,_skip_next'] = 48;
    $functions['if_<=,_skip_next'] = 49;
    $functions['if_=,_skip_next'] = 50;
    $functions['if_!=,_skip_next'] = 51;
    $functions['GOTO'] = 52;
    $functions['source_feed'] = 53;

    // Query
    return $functions[$function];
  }
?>