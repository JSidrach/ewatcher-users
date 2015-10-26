Alarms configuration

File Alarms.php
Set setver URL:
$ServerURL = "http://localhost/emoncms";

Set notification minimum interval in minutes:
$NotificaionTime = 120;

Set global radiation threshold for notifying zero production alarm, in W/m2
$RadThreshold = 50; 

Set global radiation-power measures relationships for zero production alarms
param 1: user id
param 2: glodal radiation input id
param 3: power input id to be watched

$rad_power_alarms = array();
$rad_power_alarms[] = array(7,399, 294);
$rad_power_alarms[] = array(7,399, 295);
$rad_power_alarms[] = array(7,399, 296);

Email template is located in file EmailTemplate.html

