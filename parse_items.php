<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
</head>
<?php
set_time_limit(0);
$fp = fopen("id_emails.csv", "r");
$fp_items = fopen("announces_2.csv", "a");
$row=1;
$deep=52137;//set how deep parser will work (variable (counter) from $fp)
while (($data = fgetcsv($fp, 0, ",")) !== FALSE) {
	if ((check_empty($data[2])=="n/a"&&check_empty($data[3])=="n/a")&&($data[0]==$deep)){//check does node has PHONENUMBER and EMAIL and set deepness
			continue;
	}else{

        $url="http://www.sweden4rus.nu/rus/anons/announcement?id=".trim($data[1]);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0); 
		$html = curl_exec($curl);

		$dom = new DOMDocument();
		@$dom->loadHTML($html);
		$xpath = new DOMXPath($dom);
		$tableRows_announce_title =$xpath->query("//h3");
		$tableRows_announce =$xpath->query("//div[contains(@style, 'margin:5px') and contains(@style, 'margin-top:20px')]");
		$tableRows_img =$xpath->query("//div[contains(@style,'margin-left:20px') and contains(@style, 'margin-bottom:15px')]");
		
		
		foreach ($tableRows_announce as $value) {
			$nodes_iterator_announce=$value->ownerDocument->saveHTML($value);
			}

		foreach ($tableRows_img as $value) {
			$nodes_iterator_img=$value->ownerDocument->saveHTML($value);
			}
		foreach ($tableRows_announce_title as $value) {
			$nodes_iterator_announce_title=$value->nodeValue;
			}

		echo "<br>";
		echo $line=$row++."|".check_empty($data[1])."|".check_empty($data[5])."|".check_empty($data[4])."|".check_empty($data[2])."|".check_empty($data[3])."|".check_empty($nodes_iterator_announce_title)."|".save_announce($nodes_iterator_announce)."|".save_img($nodes_iterator_img);
		fwrite($fp_items, $line.PHP_EOL);
		unset($line,$nodes_iterator_img,$nodes_iterator_announce);
    }
}
   fclose($fp); 

   	function save_img($raw_img){
	   	$regexp = '/<img border="1" src="..\/..(.*.jpg).*>/sui';
		preg_match_all($regexp, $raw_img, $match,PREG_SET_ORDER);
		if(!empty($match)){
			return  $match[0][1];
		}
			return "n/a";	
	}

	function save_announce($raw_announce){
	   	$regexp = '/<div style="margin:5px; margin-top:20px; margin-bottom:15px; line-height: 24px;">(.*?)<\/div>.*/sui';
		preg_match_all($regexp, $raw_announce, $match,PREG_SET_ORDER);
		if(!empty($match)){
			return  $match[0][1];
		}else{
			return "n/a";
		}	
	}

	function check_empty($raw_node){
		trim($raw_node);
		if (strlen($raw_node)>2){
			return trim($raw_node);
		}else{
			return "n/a";
		}
	}

  

/*CASE1, handling with image
			<div style="margin:5px; margin-top:20px; margin-bottom:15px; line-height: 24px;">
            Hej! Предоставляем изготовление ключей и сопутствующие услуги:<br> - брики 1 шт - 100 кр; 4 шт - 300 кр, более обсуждается индивидуально<br> + доставка бесплатно 2 шт и больше<br> - восстановление нерабочей брики<br> - от дверных, сейфовых, навесных и др. замков, очень широкий ассортимент<br> - автоключи (двери, зажигание), иммобилайзеры некоторых видов<br> - аварийное вскрытие замков<br> - установка замков<br> - создание системы "мастер-ключ"<br> - также можно приобрести дверные и навесные замки<br> Почему мы? <br> ✅ 100% ГАРАНТИЯ КАЧЕСТВА<br> ✅ БЕСПЛАТНЫЙ сервис по нашим брикам<br> ✅ БЕСПЛАТНЫЙ выезд, изготовления/восстановление брик<br> ✅ Изготовление брик/ключей за «5 минут»<br> ✅ БОЛЬШОЙ ассортимент заготовок<br> Потеряли ключ или просто хотите сделать запасной? <br> ☎️ 0765 640 564 (сохраните на всякий случай)<br> Хорошего дня!
            </div><br><div style="margin-left:20px; margin-bottom:15px;">
            
            <img border="1" src="../../_img/_db_img/_anons_db_img/000/292/292001.jpg?v=0,533424">

*/


/*CASE2 handling without image
            <div style="margin:5px; margin-top:20px; margin-bottom:15px; line-height: 24px;">
            1-комнатная квартира 25 квадратных метров на долгий срок, 10 этаж есть лифт. Fleminsberg. рядом pendeltågstation Feminsberg, автобусы и магазины 0762209462 Цена 10.000 Всё включено
            </div>
*/


?>
</html>