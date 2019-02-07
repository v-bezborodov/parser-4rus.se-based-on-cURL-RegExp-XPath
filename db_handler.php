/********************************************************           
* That sript parses items from csv into CMS OS-Class.  *   
*                                                      *   
* Author:  Slawek                        			   *   
*                                                      *   
* Purpose:  Demonstration of a simple program.         *   
* 
********************************************************/  

   <?php

define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_NAME', 'n45020_db');
define('DB_TABLE_PREFIX', 'oc_');

define('db_operations', TRUE);//set option do DB operatrions or no

$filepath="./announces_test.csv";
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($mysqli->connect_errno) {
    echo "Error MySQL connection: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
if (file_exists($filepath)) {
	$fp = fopen($filepath, "r");
	echo 'file exist..<br>';
$raw_array=array();

while (($data = fgetcsv($fp, 0, '|')) !== FALSE) {
	//echo "data 0=".$data[0]." ".$data[1]." ".$data[2]." data 3=".$data[3]." data 4=".$data[4]." data 5=".$data[5]." data 6=".$data[6]." data 7=".$data[7]." data 8=".$data[8]."<br>";
	if (empty($data[2])||strlen($data[2])<3 ){//some sort of prechecking, making data completed 
		echo "<b>Fix it! Empty row is</b> ".$data[0]." ".$data[2]."<br>";
		echo "<b>script is ended</b><br>";
		break;
	}
	if(empty($data[4])||strlen($data[4])<3){//some sort of prechecking and making data completed
		echo "<b>Fix it! Empty row is</b> ".$data[0]." ".$data[4]."<br>";
		echo "<b>script is ended</b><br>";
		break;
	}else{
		array_push($raw_array,$data[2]);//figuring out what categories there are generally
	}

	if(empty($data[6])){//check if city empty, place there Stockholm
		$data[6]="Стокгольм";
	}
	if(define_categories($data[2])<0){
		echo $data[0]."<b>Row is skipped due of matched filter</b> ".$data[2]."<br>";
		continue;
	}
	if (define_categories($data[2])<0){//exclude filtered categories ( all filteres categories are marked -1)
		echo "<b>Category marked ".$data[2]."</b> is skipped<br>";
		continue;
	}
		

	if(db_operations){
		//echo define_date($data[4])."<br>";
		$db->query("INSERT INTO ".DB_TABLE_PREFIX."t_item SET fk_i_category_id=".define_categories($data[2]).", dt_pub_date = '".define_date($data[3])."' , dt_mod_date='".define_date($data[3])."' , i_price=0, s_contact_name='n/a', s_contact_email='".$data[4]."', b_enabled = 1, b_active=1, b_show_email=0, dt_expiration='9999-12-31 23:59:59'");
	   	$db->affected_rows<0? print "<br>undone..<br>": print "done..<br>" ;
	   	//echo "category id ".$db->insert_id;
	   	$generated=$db->insert_id;
	   	$db->query("INSERT INTO ".DB_TABLE_PREFIX."t_item_description SET fk_i_item_id=".$generated.", fk_c_locale_code = 'ru_RU' , s_title='".$data[7]."' , s_description='".$data[8]."'");
	   	$db->affected_rows<0? print "<br>undone..<br>": print "done..<br>" ;
}
echo define_date($data[3]);
echo "<br>";
}

define_unique_categories($raw_array);

unset($data);
}
else{
	echo "file".$fp."doesn't exist";
}


function define_unique_categories($raw_array){
//$raw_array=trim($raw_array);
$output_unique_categories=array_unique($raw_array,SORT_STRING);
	$i=1;
	foreach ($output_unique_categories as  $value) {
		$value=trim($value);
		print_r($i++." Category . \"".$value."\" defined as ".define_categories($value)."<br>");
	}
}


function define_categories($category){
	$category=trim($category);
	
	if($category=="Деловое предложение"){
		return 5;
	}
	if($category=="Услуги"){
		return 5;
	}
	if($category=="Флирт"){
		return -1;
	}
	if($category=="Куплю"){
		return 96;
	}
	if($category=="Предлагаю работу"){
		return 8;
	}
	if($category=="Сниму"){
		return 98;
	}
	if($category=="Сдам"){
		return 44;
	}
	if($category=="Продам"){
		return 1;
	}
	if($category=="Ищу работу"){
		return 97;
	}else{
		return -1;

	}

	//can't get why that way doesn't work
	/*return  (strcasecmp($category,'Деловое предложение')==0)?3: 
			(strcasecmp($category,'Услуги')==0)? 1 :
			(strcasecmp($category,'Флирт')==0)? 2 : 
			(strcasecmp($category,'Куплю')==0)? 3 : 
			(strcasecmp($category,'Предлагаю работу')==0)? 4 : 
			(strcasecmp($category,'Ищу работу')==0)? 5 :  
			(strcasecmp($category,'Сниму')==0)? 6 :
			(strcasecmp($category,'Сдам')==0)? 7 :  
			(strcasecmp($category,'Продам')==0)? 8 : 
		0;	*/ 


/* First of all need define all uniq categories then define them id according database records

1. Деловое предложение| defined as 5
2. Услуги| defined as 5
3. Сдам| defined as 5
4. Флирт| defined as 0
5. Куплю| defined as 0
6. Предлагаю работу| defined as 0
7. Ищу работу| defined as 0
8. Продам| defined as 0
9. Общий поиск знакомств| defined as 0
10. Сниму| defined as 0

*/	

	//$i=0;
	//foreach ($categories as $value) {
		//$i++;
		//$db->query("INSERT INTO ".DB_TABLE_PREFIX."t_category SET i_expiration_days=0, i_position= ".$i.", b_enabled = 1 , b_price_enabled = 1");
		//$db->affected_rows<0? print "undone..<br>": print "done..<br>" ;

	   	//echo "id ".$db->insert_id;
	   	//$generated=$db->insert_id;
	   	//$db->query("INSERT INTO ".DB_TABLE_PREFIX."t_category_description SET fk_i_category_id=".$generated.", fk_c_locale_code='ru_RU', s_name='".(string)$value."', s_slug='".translit((string)$value)."'");
		//$db->affected_rows<0? print "undone..<br>": print "done..<br>" ;
	//}
}
	function define_date($raw_date){
		//2 февраля 2019 г.
		$raw_date=trim($raw_date);

		$regexp = '/(\d+) (\w+) (\d+).*/sui';
		preg_match_all($regexp, $raw_date, $match,PREG_SET_ORDER);

		$match[0][2]=mb_convert_case($match[0][2], MB_CASE_LOWER, "UTF-8"); 
		//echo $match[0][2];

		if((string)$match[0][2]=='января'){$month='01';}
		if((string)$match[0][2]=='февраля'){$month="02";}
		if((string)$match[0][2]=='марта'){$month='03';}
		if((string)$match[0][2]=='апреля'){$month='04';}
		if((string)$match[0][2]=='мая'){$month='05';}
		if((string)$match[0][2]=='июня'){$month='06';}
		if((string)$match[0][2]=='июля'){$month='07';}
		if((string)$match[0][2]=='августа'){$month='08';}
		if((string)$match[0][2]=='сентября'){$month='09';}
		if((string)$match[0][2]=='октября'){$month='10';}
		if((string)$match[0][2]=='ноября'){$month='11';}
		if((string)$match[0][2]=='декабря'){$month='12';}
		//else{$month='0';}

		if(strlen($match[0][1])<2){
			$match[0][1]="0".$match[0][1];
		}
		return $match[0][3]."-".$month."-".$match[0][1]." ".date('H:i:s');
		}

	function translit($s) {
		  $s = (string) $s; 
		  $s = strip_tags($s); 
		  $s = str_replace(array("\n", "\r"), " ", $s); 
		  $s = preg_replace("/\s+/", ' ', $s); 
		  $s = trim($s); 
		  $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
		  $s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
		  $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); 
		  $s = str_replace(" ", "_", $s); 
  return $s; 
}


?>