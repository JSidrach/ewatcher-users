<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

// Settings
require_once('settings.php');
require_once('query.php');
require_once('MailFuncs.php');


$ServerURL = "http://localhost/emoncms";
$NotificaionTime = 120;  //minutos mínimos entre dos notificaciones de alarma
$RadThreshold = 50; //W/m2
//vamos a usar los id de los inputs
$rad_power_alarms = array();
$rad_power_alarms[] = array(7,399, 294);
$rad_power_alarms[] = array(7,399, 295);
$rad_power_alarms[] = array(7,399, 296);


//alarmas generales
// $general_alarms = array();
// $general_alarms[] = 402;
// $general_alarms[] = 403;
// $general_alarms[] = 404;
// $general_alarms[] = 405;
// $general_alarms[] = 406;
// $general_alarms[] = 407;
// $general_alarms[] = 408;
// $general_alarms[] = 409;
// $general_alarms[] = 410;
// $general_alarms[] = 411;
// $general_alarms[] = 412;
// $general_alarms[] = 413;

//******************************************************************************
//******************************************************************************

UserMailInfo(7);



//******************************************************************************
//******************************************************************************
	//devuelve un array de string
	//cada string será una linea para el mail que hay que enviar
	//si el array está vacio entonces significa que no es necesario enviar el mail
	function UserMailInfo($userid)
	{
		if(create_connection($connection))
		{
			$userInfo = getUserInfo($connection,$userid);
			//0: id 1: username 2: mail 3: password 4: salt 5: apikey_write 6: apikey_read 7: lastlogin 8: admin 9: gravatar 10: name 11: location 12: timezone 13: language 14: bio
			if($userInfo != null)
			{
				echo "Buscando alarmas para usuario: [".$userInfo[0]."] ".$userInfo[1]." - ".$userInfo[10]."<br>";
				$userActiveAlarms = getUserActiveAlarms($connection, $userid);
				$userRadPowerAlarms = getUserRadPowerAlarms($connection, $userid);
				
				$userAlarms = array_merge($userActiveAlarms, $userRadPowerAlarms);
				
				if(count($userAlarms) > 0)
				{
					echo "Alarmas encontradas, enviando mensaje...<br>";
					$bodyText = formatMailBody($userAlarms);
					$subject = "Alarmas detectadas - ISMSOLAR";

					$mailbody = file_get_contents("/var/www/html/ewatcher-users/MailBody.html");

					$mailbody = str_replace("[BODY]", $bodyText, $mailbody);
					
				    $MailSentOk = sendMail($mailbody, $subject, $userInfo[2], $userInfo[10]);
					if($MailSentOk)
					{
						echo "llamando a markAlarmsAsNotified<br>";
						markAlarmsAsNotified($connection, $userAlarms);
					}
				}
				else
				{
					echo "No se encontraron alarmas activas para notificar.<br>";
				}
			}
		}
		else
		{
			echo "Error de conexión a la base de datos...<br>";
		}
	}
	
	
	//**************************************************************************
	function formatMailBody($userAlarms)
	{
		$result = "ALARMAS ACTIVADAS<br>";
		for($r=0; $r<count($userAlarms); $r++)
		{
			$result .= $userAlarms[$r][1];
		}
		return $result;
	}
	
	
	//**************************************************************************
	//Devolvemos un array con las alarmas activas
	//en cada fila del array se devuelve un array que contiene
	//el ID del input y el text de la alarma en HTML
	//el ID del INPUT lo usaremos mas tarde para marcar la alarma como notificada
	//una alarma no se considera activa si ha sido notificada recientemente...
	function getUserActiveAlarms($connection, $userid)
	{
		echo "Buscando alarmas activadas...<br>";
		$query = "select id, userid, name, description, nodeid, processList, time, value ";
		$query .= "from input ";
		$query .= "where userid = ".$userid." ";
		$query .= "and value !=0 ";
		$query .= "and (name like '%alarm%' or name like '%alrm%') ";
		$query .= "and name not like '%notified%'";
		 
 		$sql_result = $connection->query($query);
		$result = array();
		
		if($sql_result)
		{
			while ($row = $sql_result->fetch_row())
			{
				$inputid = $row[0];
				$alarmText = "<h3>Alarma activada: ".$row[2]." (".$row[3].") - NODO: ".$row[4]."</h3><br>";
				
				if(!inputHasRecentNotification($connection, $row))
				{
					echo "Se va a notificar: <br>".$alarmText;
					$result_row = array();
					$result_row[0] = $inputid; // id del INPUT
					$result_row[1] = $alarmText;
					$result[] = $result_row;
				}
				else
				{
					echo "No se notifica ya que se ha notificado recientemente: <br>".$alarmText;
				}
			}
		}
		return $result;
	}				 
				 
				 
	//**************************************************************************
	function getUserRadPowerAlarms($connection, $userid)
	{
		global $rad_power_alarms;
		global $RadThreshold;
		$result = array();
		
		echo "Buscando alarmas por radiación/potencia...<br>";
		
		for($r=0; $r < count($rad_power_alarms); $r++)
		{
			//es una alarma de nuestro usuario
			if($rad_power_alarms[$r][0] == $userid)
			{
				$rad_inputid = $rad_power_alarms[$r][1];
				$power_inputid = $rad_power_alarms[$r][2];

				$rad_InputInfo = getInputInfo($connection,$rad_inputid);		
				$power_InputInfo = getInputInfo($connection,$power_inputid);
				//0: id //1: userid //2: name //3: description //4: nodeid //5: processList //6: time //7: value

				if (($rad_InputInfo != null) && ($power_InputInfo != null))
				{
					$rad_value = $rad_InputInfo[7];	
					$power_value = $power_InputInfo[7];
					
					if (($rad_value > $RadThreshold) && ($power_value == 0))
					{
						$alarmText = "<h3>Potencia baja: MEDIDA: ".$power_InputInfo[2]." (".$power_InputInfo[3].") - NODO: ".$power_InputInfo[4]." - VALOR: ".$power_value."</h3><br>";
						$inputId = $power_InputInfo[0];
						
						if(!inputHasRecentNotification($connection, $power_InputInfo))
						{
							echo "Se va a notificar: <br>".$alarmText;
							$result_row = array();
							$result_row[0] = $inputId;
							$result_row[1] = $alarmText;
							$result[] = $result_row;
						}
						else
						{
							echo "No se notifica ya que se ha notificado recientemente: <br>".$alarmText;
						}
					}
				}
			}
		}
		return $result;
	}

	
	//**************************************************************************
	function getInputNotificationName($inputInfo)
	{
		return $inputInfo[2]."_NOTIFIED";
	}
	
	
	//**************************************************************************
	function markAlarmsAsNotified($connection,$userAlarms)
	{
		global $ServerURL;
		for ($i=0; $i < count($userAlarms); $i++)
		{
			$inputInfo = getInputInfo($connection,$userAlarms[$i][0]);
			$userid = $inputInfo[1];
			$name = $inputInfo[2];
			$nodeId = $inputInfo[4];
			
			$inputNotificationName = getInputNotificationName($inputInfo);
			//echo "inputNotificationName: ".$inputNotificationName."<br>";
			
			$userInfo = getUserInfo($connection,$userid);
			$apiKeyWrite = $userInfo[5];
			//echo "APIKEY : ".$apiKeyWrite;
			//echo "<br>";
			
            //url = "http://" + serverUrl + "/input/post.json";
			$url = $ServerURL."/input/post.json";			

            $url .= "?node=".$nodeId;
            $url .= "&json=";

            $url .= $inputNotificationName.":0";

            $url .= "&apikey=".$apiKeyWrite;		
			
			//$response = http_get($url, array("timeout"=>10), $info);
			//$result = file_get_contents($url);
			//print_r($info);
			///tmp/pear/temp/pecl_http/configure --with-http-zlib-dir=/usr --with-http-libcurl-dir=/usr --with-http-libevent-dir=/usr --with-http-libidn-dir=/usr' failed
			
			echo "URL para anotar notificación: <br>";
			echo "URL: ".$url;
			echo "<br>";
			
			// create curl resource 
			$ch = curl_init();	 

			// set url 
			curl_setopt($ch, CURLOPT_URL, $url); 

			//return the transfer as a string 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

			// $output contains the output string 
			$output = curl_exec($ch); 
			
			echo "Respuesta del servidor a la URL: <br>";
			echo $output;
			echo "<br>";
			

			// close curl resource to free up system resources 
			curl_close($ch);  
		}
	}
	
	
	//**************************************************************************
	function inputHasRecentNotification($connection, $inputInfo)
	{
		global $NotificaionTime;
		$result = false;
		$nodeId = $inputInfo[4];
		$inputNotificationName = getInputNotificationName($inputInfo);
		
		$inputNotificationInfo = getInputInfoByNameNode($connection,$inputNotificationName,$nodeId);

		if ($inputNotificationInfo != null)
		{
			$notificationTime = $inputNotificationInfo[6];
			
			
			$d1 = strtotime($notificationTime);
			$d2 = time();
			
			$dateDiff = round(abs($d1 - $d2) / 60);
			
			$date1 = date("Y-m-d H:i:s", $d1);
			$date2 = date("Y-m-d H:i:s", $d2);
			
			echo "Fecha ultima notificación: ".$date1."<br>";
			echo "Fecha actual: ".$date2."<br>";

			//$d1 = new DateTime($date1);
			//$d2 = new DateTime($date2);

			//$interval = date_diff($d1,$d2);
			//if ($interval->i < $NotificaionTime)
			if($dateDiff < $NotificaionTime)
			{
				$result = true;
				echo "La alarma ".$inputNotificationInfo[2]." se notificó hace ".$dateDiff." minutos y se notificará de nuevo cuando hayan transcurrido al menos ".$NotificaionTime." minutos...<br>";
			}
			else
			{
				echo "La alarma ".$inputNotificationInfo[2]." se notificó hace ".$dateDiff." minutos y se volverá a notificar ahora...<br>";
			}
		}
		
		return $result;
	}
	
	//**************************************************************************
	
	//**************************************************************************
	// FUNCIONES AUXILIARES DE LA BASE DE DATOS
	//**************************************************************************
	
	//**************************************************************************
	function getInputInfo($connection,$inputid)
	{
		$sql_result = $connection->query("SELECT id, userid, name, description, nodeid, processList, time, value FROM input WHERE id = ".$inputid.";");
			
		if ($sql_result)
		{		
			$row = $sql_result->fetch_row();
			
			return $row;
		}
		else
		{
			return null;
		}
	}
	
	//**************************************************************************
	function getInputInfoByNameNode($connection,$inputNotificationName,$nodeId)
	{
		$sql_result = $connection->query("SELECT id, userid, name, description, nodeid, processList, time, value FROM input WHERE name = '".$inputNotificationName."' and nodeid = ".$nodeId.";");
			
		if ($sql_result)
		{		
			$row = $sql_result->fetch_row();
			
			return $row;
		}
		else
		{
			return null;
		}
	}

	//**************************************************************************
	function getUserInfo($connection,$userid)
	{
		$query = "SELECT id, username, email, password, salt, apikey_write, apikey_read, lastlogin, admin, gravatar, name, location, timezone, language, bio FROM users WHERE id = ".$userid.";";
		$sql_result = $connection->query($query);
			
		if ($sql_result)
		{		
			$row = $sql_result->fetch_row();
			return $row;
		}
		else
		{
			echo "Error en la consulta: ".$query."<br/>";
			return null;
		}
	}

?>