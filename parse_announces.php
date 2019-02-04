<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
</head>
<?php
//set_time_limit(0);
$fp = fopen("emails.csv", "r");
$fp_items = fopen("items_announced.csv", "a");
$row=1;
while (($data = fgetcsv($fp, 0, ",")) !== FALSE) {
	$rowSize=count($data);

	/*for($i=1; $i<$rowSize; $i++){
		if($data[$i]==""){
			$data[$i]="n/a";
		}
	}*/
        $url="http://www.sweden4rus.nu/rus/anons/announcement?id=".trim($data[1]);
       //echo $url."<br>";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0); 
		$html = curl_exec($curl);

		$dom = new DOMDocument();
		@$dom->loadHTML($html);
		$xpath = new DOMXPath($dom);
		$tableRows_announce =$xpath->query("//div[contains(@style, 'margin:5px') and contains(@style, 'margin-top:20px')]");
		$tableRows_img =$xpath->query("//div[contains(@style,'margin-left:20px') and contains(@style, 'margin-bottom:15px')]");
		
		
		foreach ($tableRows_announce as $value) {
			//$announcement=$value->nodeValue;
			$j++;
			$nodes_iterator_announce=$value->ownerDocument->saveHTML($value);
			//$morenodes = $x_path->query(".//img", $tableRows);
			}

		foreach ($tableRows_img as $value) {
			//$announcement=$value->nodeValue;
			$j++;
			$nodes_iterator_img=$value->ownerDocument->saveHTML($value);
			//$morenodes = $x_path->query(".//img", $tableRows);
			}

		
		echo $line=$row++."| ".trim($data[1])."| ".trim($data[5])."| ".trim($data[4])."| ".trim($data[2]).", ".trim($data[3])."| ".save_announce($nodes_iterator_announce)."| ".save_img($nodes_iterator_img);
		fwrite($fp_items, $line.PHP_EOL);
		

		
		unset($line,$nodes_iterator_img,$nodes_iterator_announce);
    }
   fclose($fp); 

   	function save_img($raw_img){
	   	$regexp = '/<img border="1" src="..\/..(.*.jpg).*>/sui';
		preg_match_all($regexp, $raw_img, $match,PREG_SET_ORDER);
		//echo count($match);
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


  

/*CASE1
			<div style="margin:5px; margin-top:20px; margin-bottom:15px; line-height: 24px;">
            Hej! Предоставляем изготовление ключей и сопутствующие услуги:<br> - брики 1 шт - 100 кр; 4 шт - 300 кр, более обсуждается индивидуально<br> + доставка бесплатно 2 шт и больше<br> - восстановление нерабочей брики<br> - от дверных, сейфовых, навесных и др. замков, очень широкий ассортимент<br> - автоключи (двери, зажигание), иммобилайзеры некоторых видов<br> - аварийное вскрытие замков<br> - установка замков<br> - создание системы "мастер-ключ"<br> - также можно приобрести дверные и навесные замки<br> Почему мы? <br> ✅ 100% ГАРАНТИЯ КАЧЕСТВА<br> ✅ БЕСПЛАТНЫЙ сервис по нашим брикам<br> ✅ БЕСПЛАТНЫЙ выезд, изготовления/восстановление брик<br> ✅ Изготовление брик/ключей за «5 минут»<br> ✅ БОЛЬШОЙ ассортимент заготовок<br> Потеряли ключ или просто хотите сделать запасной? <br> ☎️ 0765 640 564 (сохраните на всякий случай)<br> Хорошего дня!
            </div><br><div style="margin-left:20px; margin-bottom:15px;">
            
            <img border="1" src="../../_img/_db_img/_anons_db_img/000/292/292001.jpg?v=0,533424">

*/


/*CASE2
            <div style="margin:5px; margin-top:20px; margin-bottom:15px; line-height: 24px;">
            1-комнатная квартира 25 квадратных метров на долгий срок, 10 этаж есть лифт. Fleminsberg. рядом pendeltågstation Feminsberg, автобусы и магазины 0762209462 Цена 10.000 Всё включено
            </div>
*/


?>
</html>