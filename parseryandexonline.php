﻿<?php
###############################################################
#
#	Parser Yandex pryamoj efir by beerhack from http://beerhack.name
#	ICQ: 274717
#
###############################################################

header('Content-Type: text/html;charset=utf-8');
set_time_limit(0); 
if(!empty($_POST['howmany'])): ?>
<?php
$howmany = $_POST['howmany'];
$seconds = $_POST['sleep'];
$filename = $_POST['filename'];
$unic = (isset($_POST['unic']) && $_POST['unic']=='ok');
function curlFunc($url){ 
	$ch = curl_init(); 
    curl_setopt($ch, CURLOPT_HEADER, 0); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); 
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11"); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_REFERER, 'http://company.yandex.ru/researches/interes/');
	$result = curl_exec($ch); 
	curl_close($ch); 
	return $result; 
}
for($i=1; $i<=$howmany; $i++){
	$url ='http://livequeries-front.corba.yandex.net/queries/?ll1=-1.7356459922329044,-1.9623822499999828&ll2=84.54750547846963,209.32668025&limit=1000';
	$str = curlFunc($url);
	if(preg_match_all('/query text="([^"]+)"/i',$str,$match)>0){
		foreach($match[1] as $key){
			$key = str_replace('&quot;', '"', $key);
			$keyArray[] = trim($key);
		}
		if($unic){
			$keyArray = array_unique($keyArray);
			if(file_exists($filename)){
				$keysInFile = file($filename);
				foreach($keysInFile as $key){
					$keysInFileArray[] = trim($key);
				}
				$unicKeys = array_diff($keyArray, $keysInFileArray);
			} else {
				$unicKeys = $keyArray;
			}
			$howmanyfact += sizeof($unicKeys);
			$keys = $unicKeys;
		} else {
			$keys = $keyArray;
		}
		foreach($keys as $key){
			$f = fopen($filename,'at');
			fwrite($f,$key."\n");
			fclose($f);
		}
		echo "$i тыс. - готово<br>";
		flush();
		ob_flush();
	} else {
		echo "Ой, что-то на $i тыс. какие-то проблемки. Попробуем снова.<br>";
		flush();
		ob_flush();
		$i--;
	}
	sleep($seconds);
}
if($unic){
	echo "Закончили работу. Собрали все $howmany тысяч кейвордов. Из них $howmanyfact уникальных, которые сохранены в файл $filename.";
} else {
	echo "Закончили работу. Собрали все $howmany тысяч кейвордов, которые сохранены в файл $filename. На уникальность не проверялись.";
}
?> <?php else : ?>
<html>
<form method="POST" action="">
Сколько тысяч собрать? <input type="text" name="howmany" value="1"><br>
Можно задержку поставить в секундах <input type="text" name="sleep" value="0"><br>
Куда сохранить? <input type="text" name="filename" value="resultp.txt"><br>
Проверить на уникальность по окончании? <input type="checkbox" name="unic" value="ok"><br>
<input type="submit" value="START">
</form>
</html>
<?php endif; ?>