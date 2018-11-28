<?php

function curl_exec_follow(/*resource*/ $ch, /*int*/ &$maxredirect = null) {
    $mr = $maxredirect === null ? 5 : intval($maxredirect);
    if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0);
        curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
    } else {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        if ($mr > 0) {
            $newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

            $rch = curl_copy_handle($ch);
            curl_setopt($rch, CURLOPT_HEADER, true);
            curl_setopt($rch, CURLOPT_NOBODY, true);
            curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
            curl_setopt($rch, CURLOPT_RETURNTRANSFER, true);
            do {
                curl_setopt($rch, CURLOPT_URL, $newurl);
                $header = curl_exec($rch);
                if (curl_errno($rch)) {
                    $code = 0;
                } else {
                    $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                    if ($code == 301 || $code == 302) {
                        preg_match('/Location:(.*?)\n/', $header, $matches);
                        $newurl = trim(array_pop($matches));
                    } else {
                        $code = 0;
                    }
                }
            } while ($code && --$mr);
            curl_close($rch);
            if (!$mr) {
                if ($maxredirect === null) {
                    trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING);
                } else {
                    $maxredirect = 0;
                }
                return false;
            }
            curl_setopt($ch, CURLOPT_URL, $newurl);
        }
    }
    return curl_exec($ch);
} 

function prettyDate($date) {

  $timeString = date('H:ia', strtotime($date));
  $dateString = '';
  $theDate = date('Y-m-d', strtotime($date));
  if ($theDate == date('Y-m-d'))
    $dateString = 'Today';
  else if ($theDate == date('Y-m-d', strtotime('-1 Day')))
    $dateString = 'Yesterday';
  else
    $dateString = date('l, M d', strtotime($date));

  return $dateString.' '.$timeString;

}

function forceHTTPS() {

  if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    if(!headers_sent()) {
      header("Status: 301 Moved Permanently");
      header(sprintf(
         'Location: https://%s%s',
         $_SERVER['HTTP_HOST'],
         $_SERVER['REQUEST_URI']
      ));
      exit();
    }
  } 

}

function isValidEmail($email) {

  $pattern = "/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/";
  return preg_match($pattern, $email);

}

function qq($str) {
	
	return '"'.$str.'"';
	
}

function r_debug($str) {
	
	if (R_DEBUG)
		error_log($str);
	
}

function ezjson($str) {
	
	return "'".str_replace(array("'", '"'), array('\\\'', '&quot;'), $str)."'";
	
}

function rbug($str) {
	
	echo "\n\t".$str;
	
}

function arrayHasNoDuplicates($array) {
	
	$unique = array_unique($array);
	return (count(array_diff($a, $b)) == 0);
	
}

function createDropDownNum($from, $to, $by, $select) {
	
	$index = array();
	for ($i = $from; $i <= $to; $i += $by)
		$index[] = $i;
		
	return createDropDownOptions($index, $select);
	
}

function dec2($float) {
	
	return number_format($float, 2, '.', '');
	
}

function base62($dec) {
	
	$seed = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	
	if ($dec < 62)
		return $seed[$dec];
	
	$rem = $dec % 62;
	return $seed[($dec - $rem) / 62].$seed[$rem];
	
}

function prel($arrayOrObject) {
	
	error_log(print_r($arrayOrObject,true));
	
}

function logThisError($msg) {
	
	global $db;
	
	error_log("LOG ERROR: ".$msg);
	
}

function primeURL($url) {
	
	return str_replace("www.", "", parse_url($url, PHP_URL_HOST));
	
}

function jsonError($error, $errorCodes = array(), $data = NULL, $returnString = false) {

	$array = array(
		"success"	=> false,
		"error"		=> $error,
    'errorCodes' => $errorCodes,
		"request"	=> $_POST
	);
	if ($data)
		$array['data'] = $data;
		
	if ($returnString)
		return json_encode($array);
	
	if (defined('__JS_BUILD') && __JS_BUILD)
		unset($array['request']['task']);
		
	die(json_encode($array));
	
}

function jsonSuccess($additionalFields = array(), $returnString = false) {
	
	$array = array("success"	=> true,
				   "request"	=> $_POST);
	foreach ($additionalFields as $field => $value)
		$array[$field] = $value;
		
	if (defined('__JS_BUILD') && __JS_BUILD)
		unset($array['request']['task']);
		
	if ($returnString)
		return json_encode($array);
		
	die(json_encode($array));
	
}

function addLeadingZeros($str, $totalLength) {
	
	while (strlen($str) < $totalLength)
		$str = "0".$str;
		
	return $str;
	
}

function isValidURL($url) {

	return (preg_match("^(http|https)\://[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(:[a-zA-Z0-9]*)?/?([a-zA-Z0-9\-\._\?\,\'/\\\+&amp;%\$#\=~])*[^\.\,\)\(\s]$", $url));	
	
}

function removeTag($str, $rel) {

	return preg_replace('/<.*?rel="'.$rel.'".*?>.*<\/.*?>/', '', $str);
	
}

function es($string) {
	
	return str_replace(" ", '\ ', $string);
	
}

function truncate($string, $charLimit) {
	
	return (strrpos($string," ") > $charLimit - 5) ? substr(substr($string,0,$charLimit - 5),0,strrpos(substr($string,0,$charLimit - 5)," ")).". . ." : $string;
	
}

function hardTruncate($string, $charLimit, &$marker = NULL, $ellipsis = true) {

	$marker = strlen($string) > $charLimit;
	return ($marker) ? substr($string, 0, $charLimit - 3).($ellipsis ? '...' : '') : $string;

}

function symArray($array) {
	
	$return = array();
	foreach ($array as $item)
		$return[$item] = $item;
	return $return;
	
}

function toDate($format, &$string, $returnResult = false) {
	if ($returnResult)
		return (empty($string)) ? "" : date($format, strtotime($string));
	else {
		$string = toDate($format, $string, true);
		return true;
	}
}

function html($string) {

	return str_replace(array("\n\r","\r\n","\n","\r"), "<br/>", stripslashes($string));

}

function money($value, $addUSD = false, $round = false) {
	
	return ($addUSD ? 'USD ' : '').'$'.number_format((double)$value,($round ? 0 : 2));
	
}

function buildHash($length) {
	
	$hash = "";
	$seed = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	mt_srand(10000000 * (double)microtime());
	for ($i = 0; $i < $length; $i++)
		$hash .= $seed[mt_rand(0,strlen($seed) - 1)];
	return $hash;
	
}

//Converts a space-separated string to camel case
function regToCamelCase($string) {
	
	return toCamelCase(strtolower(str_replace(" ", "_", $string)));
	
}

//Converts string of the form str_str_str to camel case, removing the underscores
function toCamelCase($string, $stripFirstTokenForDB = false) {
	
	$return = "";
	$tmp = explode("_",$string);
  if (count($tmp) == 1)
    return strtolower($tmp[0]);
	for ($i = $start = ($stripFirstTokenForDB ? 1 : 0); $i < count($tmp); $i++)
		$return .= ($i == $start ? strtolower($tmp[$i]) : ucwords(strtolower($tmp[$i])));
	return $return;
	
}

function getCurrentLocation() {
	
	$location = str_replace("index.php","",$_SERVER['PHP_SELF']);
	if (!empty($_SERVER['QUERY_STRING']))
		$location .= "?".$_SERVER['QUERY_STRING'];
	return $location;

}

function daysFromSeconds($seconds) {

	return round($seconds / (60*60*24));

}

//  Start at $date, then get next date of next @weekday
function dateOfNextWeekday($date, $weekday) {

  $current = $date.' 0:00:00';
  while (date('D', strtotime($current)) != $weekday)
    $current = date('Y-m-d', strtotime($current.' +1 Day'));

  return $current;

}

function throwPopulateError($errorsArray, $errorCodesArray) {

  jsonError('There were errors. Please correct the fields in red.', $errorCodesArray, $errorsArray);

}

function throwErrors($errorCodes) {

  jsonError('', $errorCodes);

}

function throwSingleError($errorCode) {

  jsonError('', array($errorCode));

}

function formatErrorObject(BaseObject $propelObj) {

    $errors = array();
    foreach ($propelObj->getValidationFailures() as $failure) {
      $tmp = explode('.', $failure->getColumn());
      $fieldName = ucwords(toCamelCase($tmp[1]));
      $code = null;
      switch (get_class($failure->getValidator())) {
        case 'MatchValidator':
          $code = 'InvalidEmail';
          break;
        case 'RequiredValidator':
          $code = 'RequiredFieldMissing';
          break;
        case 'UniqueValidator':
          $code = 'DuplicateEntry';
          break;
        case 'MinValueValidator':
          $code = 'MinValue';
          break;
      }
      $errors[] = $code.':'.$fieldName;
    }

    return $errors;

}

function returnErrorObject(BaseObject $propelObj) {

  $errors = formatErrorObject($propelObj);
  throwPopulateError($errors);

}

function createDropDownOptions($options, $selected = NULL) {

  $return = '';
  foreach ($options as $key => $value)
    $return .= '<option value='.$key.($key == $selected ? ' selected' : '').'>'.$value.'</option>';

  return $return;

}

function allSet($data){
    if(isset($data) && !empty($data)){
        return true;
    }return false;
}

function sortArray( $a, $b ){
    $a = $a['distance'];
    $b = $b['distance'];
    if( $a == $b )
         return 0;
    return ( $a < $b ) ? -1 : 1;
}

if( !function_exists( 'array_column' ) ){

    function array_column( array $input, $column_key, $index_key = null ) {

        $result = array();
        foreach( $input as $k => $v )
            $result[ $index_key ? $v[ $index_key ] : $k ] = $v[ $column_key ];

        return $result;
    }
}

function getFullDay($day){
    $weekdays = [];
    $weekdays['Sun'] = "Sunday";
    $weekdays['Mon'] = "Monday";
    $weekdays['Tue'] = "Tuesday";
    $weekdays['Wed'] = "Wednesday";
    $weekdays['Thu'] = "Thursday";
    $weekdays['Fri'] = "Friday";
    $weekdays['Sat'] = "Saturday";

    return $weekdays[$day];
}

function getDateFromDay($day){
    return date('Y-m-d H:i:s', strtotime( $day ) );
}

function timeConstraint( $day ){
    $today = new DateTime( date('d.m.Y H:i:s') );
    $deliveryDate = new DateTime( getDateFromDay( $day ) );
    $result = $today->diff($deliveryDate);
    $hours = $result->h + ( $result->days * 24 );
    if( $hours <= ( OrderPeer::CHARGE_DAYS_BEFORE * 24 ) ){
        throwSingleError( 'OrdersCreated' );
    }
}

function calculateTax( $total, $tax ){
    return round( ( $total * ( $tax/100 ) ), 2 );
}

function logIt($name, $info, $message = NULL)
{
    $log = new \Monolog\Logger($name);

        $html =new \Monolog\Handler\StreamHandler(WEBROOT.'temp/'.$name.'_'.date('Y-m-d').'HTMLlog.html', \Monolog\Logger::INFO);
            $html->setFormatter( new \Monolog\Formatter\HtmlFormatter() );

                $json =new \Monolog\Handler\StreamHandler(WEBROOT.'temp/'.$name.'_'.date('Y-m-d').'JSONlog.log', \Monolog\Logger::INFO);
                    $json->setFormatter( new \Monolog\Formatter\JsonFormatter() );

                        $log->pushHandler( $html );
                            $log->pushHandler( $json );

                                $log->addInfo($message, $info);
                                }


