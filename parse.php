<?php
$url="http://www.sweden4rus.nu/rus/anons/poisksub";
$fp = fopen("emails.txt", "a");
//$uagent = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.205 Safari/534.16";
$curl = curl_init($url);
//curl_setopt($curl, CURLOPT_USERAGENT, $uagent);  // useragent
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
$result = curl_exec($curl);

$regexp = '|(.*)<img.*email.png.*>(.*)|';

preg_match_all($regexp, $result, $match,PREG_SET_ORDER); 

if(!$match==FALSE ||!$match==0){
	foreach($match as $value){
		$emails=$value[1].'@'.$value[2];
		echo $emails.'<br>';
		fwrite($fp, trim($emails).PHP_EOL);
	}
}else{

	exit("Unable to connect to $url");
	curl_close($curl);
	fclose($fp);
}



?>