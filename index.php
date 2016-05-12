<?php
//groups[]=10,groups[]=11&businessHours=1&duration=24&days_open=mon,tue,wed,thu,fri&open=09:00&close=17:00&timezone=Pacific/Midway&users[]=100&users[]=200&users[]=300&users[]=400&users[]=500&users[]=600&users[]=700&users[]=800

$groups = isset($_GET["groups"]) ? $_GET["groups"] : array("10", "11"); //two groups, each containing 4 users
$businessHours = isset($_GET["businessHours"]) ? $_GET["businessHours"] : 1; //only send during business hours
$companyId = 1; // just something to reference
$duration = isset($_GET["duration"]) ? $_GET["duration"] : 24; // 24 hours

//SELECT days_open, open_hour, close_hour, timezone from company  WHERE id = $companyId;

$daysOpen = isset($_GET["days_open"]) ? $_GET["days_open"] : "Mon, Tue, Wed, Thu, Fri"; //mon-fri
$daysOpen = explode(",",$daysOpen);
$openHour = isset($_GET["open"]) ? $_GET["open"] : "09:00"; //9am
$closeHour = isset($_GET["close"]) ? $_GET["close"] : "17:00"; //5pm
$timeZone = isset($_GET["timezone"]) ? $_GET["timezone"] : "Pacific/Midway";

//SELECT userId from assigned_groups WHERE groupId = $groups;
$userList = isset($_GET["users"]) ? $_GET["users"] : array("100", "200", "300", "400", "500", "600", "700", "800"); // 8 users, 4 per groupId

if($businessHours) {
	$usersCuantity = count($userList);
	$tz = new DateTimeZone($timeZone);
	$today = Date("Y-m-d");
	$start = new DateTime($today.$openHour, $tz);
	$stop = new DateTime($today.$closeHour, $tz);

    $hoursAvailable = intval($stop->diff($start)->format("%H"));
    
    //Hour interval = users / hours;
    $alertInterval = $hoursAvailable / $usersCuantity;
    
    foreach($daysOpen as $day){
		$alarmDay = new DateTime();
		$alarmDay->setTimestamp(strtotime($day . " next week"));
		foreach($userList as $key => $user) {
			$alarmDateTime = DateTime::createFromFormat("Y-m-d H:m", $alarmDay->format("Y-m-d ") . $start->format("H:00"));
			$sumTime = $alertInterval * $key;
			$thisAlarmDateTime = $alarmDateTime->add(new DateInterval("PT".$sumTime."H"))->format("Y-m-d h:i");
			echo "INSERT INTO alerts (companyId, userId, sendTime) values (" . $companyId .", " . $user . ", " . $thisAlarmDateTime . ");<br/>";
		}
	}
}

//What should happen now

//Example 8 hour campaign

//8 rows with the first one starting at 9AM and the last one at 5PM.

//If the numbers above changed to be 4 users over a 24 hour

//4 rows with the 
//first one starting at 9AM, 
//second at 3PM, 
//third at 9AM (the next day) and 
//fourth at 3PM (the next day.) 
//This is because the alerts should be spaced out over the duration and 4 alerts 
//over 24 hours is one alert every 6 hours.
?>
