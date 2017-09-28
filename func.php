<?php
	
	return function () {
		
		$generate = function ($client, $server) {
			$time = microtime();
			$space = strpos($time, ' ');
			$period = strpos($time, '.');
			$date = substr($time, $space+1, strlen($time)-($space+1));
			$timestamp = date('YmdHis', $date).substr($time, $period+1, 6);
			$client = str_pad(preg_replace('/[^a-z0-9]/', '', strtolower(substr($client, 0, 10))), 16, '0', STR_PAD_LEFT);
			$server = str_pad(preg_replace('/[^a-z0-9]/', '', strtolower(substr($server, 0, 10))), 16, '0', STR_PAD_LEFT);
			$rand = substr(hash('sha512', $server.$client.rand()), 0, 44);
			return $timestamp.$rand;
		};
		
		$pack = function ($uuid) {
			$string = pack('H*', $uuid);
			return strtr(rtrim(base64_encode($string), '='), '+/', '-_');
		};
		
		$toDatetime = function ($uuid, $timezone=false, $timezonedst=false) {
			$timezone = intval($timezone);
			$timezonearray = (($timezonedst==true)?array('-4'=>'EDT','-5'=>'CDT','-6'=>'MDT','-7'=>'PDT'):array('-5'=>'EST','-6'=>'CST','-7'=>'MST','-8'=>'PST'));
			$timezonestring = (($timezone==0)?'UTC':((isset($timezonearray[$timezone]))?$timezonearray[$timezone]:'GMT'.$timezone.':00'));
			return date('Y-m-d H:i:s', strtotime(substr($uuid, 0, 14))+(($timezone===false)?0:$timezone*3600)).' '.(($timezone===false)?'UTC':$timezonestring);
		};
		
		$toTimestamp = function ($uuid) {
			return '[ISO 8601 (UTC)] '.substr($uuid, 0, 4).'-'.substr($uuid, 4, 2).'-'.substr($uuid, 6, 2).'T'.substr($uuid, 8, 2).':'.substr($uuid, 10, 2).':'.substr($uuid, 12, 2).'Z';
		};
		
		$unpack = function ($string) {
			$array = unpack('H*', base64_decode(strtr($string.'=', '-_', '+/')));
			return array_shift($array);
		};
		
		$validate = function ($string) {
			$string = substr(preg_replace('/[^a-f0-9]/', '', strtolower($string)), 0, 64);
			return preg_match('/^[0-9]{20}[a-f0-9]{44}$/', $string) ? $string : false;
		};
		
		return array(
			'generate' => $generate,
			'pack' => $pack,
			'toDatetime' => $toDatetime,
			'toTimestamp' => $toTimestamp,
			'unpack' => $unpack,
			'validate' => $validate
		);
		
	};
	
?>