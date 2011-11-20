<?php
/**
 * Vietnamese Class
 *
 * @package izomi.admin.utils
 * @author Thanh H. Nguyen
 **/
class Vietnamese extends Object
{
	const lower = '
		a|b|c|d|e|f|g|h|i|j|k|l|m|n|o|p|q|r|s|t|u|v|w|x|y|z
		|á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ
		|đ
		|é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ
		|í|ì|ỉ|ĩ|ị
		|ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ
		|ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự
		|ý|ỳ|ỷ|ỹ|ỵ';
	const upper = '
		A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z
		|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ
		|Đ
		|É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ
		|Í|Ì|Ỉ|Ĩ|Ị
		|Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ
		|Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự
		|Ý|Ỳ|Ỷ|Ỹ|Ỵ';

	public static function cv2urltitle($text) {
	
		$text = str_replace(
		array(' ','%',"/","\\",'"','?','<','>',"#","^","`","'","=","!",":" ,",,","..","*","&","__","▄"),
		array('_','' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'-','' ,'-','' ,'' ,'' , "_" ,"" ,""),
		$text); 
		
		$chars = array("a","A","e","E","o","O","u","U","i","I","d", "D","y","Y");
		
		$uni[0] = array("á","à","ạ","ả","ã","â","ấ","ầ", "ậ","ẩ","ẫ","ă","ắ","ằ","ặ","ẳ","� �");
		$uni[1] = array("Á","À","Ạ","Ả","Ã","Â","Ấ","Ầ", "Ậ","Ẩ","Ẫ","Ă","Ắ","Ằ","Ặ","Ẳ","� �");
		$uni[2] = array("é","è","ẹ","ẻ","ẽ","ê","ế","ề" ,"ệ","ể","ễ");
		$uni[3] = array("É","È","Ẹ","Ẻ","Ẽ","Ê","Ế","Ề" ,"Ệ","Ể","Ễ");
		$uni[4] = array("ó","ò","ọ","ỏ","õ","ô","ố","ồ", "ộ","ổ","ỗ","ơ","ớ","ờ","ợ","ở","� �");
		$uni[5] = array("Ó","Ò","Ọ","Ỏ","Õ","Ô","Ố","Ồ", "Ộ","Ổ","Ỗ","Ơ","Ớ","Ờ","Ợ","Ở","� �");
		$uni[6] = array("ú","ù","ụ","ủ","ũ","ư","ứ","ừ", "ự","ử","ữ");
		$uni[7] = array("Ú","Ù","Ụ","Ủ","Ũ","Ư","Ứ","Ừ", "Ự","Ử","Ữ");
		$uni[8] = array("í","ì","ị","ỉ","ĩ");
		$uni[9] = array("Í","Ì","Ị","Ỉ","Ĩ");
		$uni[10] = array("đ");
		$uni[11] = array("Đ");
		$uni[12] = array("ý","ỳ","ỵ","ỷ","ỹ");
		$uni[13] = array("Ý","Ỳ","Ỵ","Ỷ","Ỹ");
		
		for($i=0; $i<=13; $i++) {
			$text = str_replace($uni[$i],$chars[$i],$text);
		}
		
		return $text;
	}
	
	#function convert to viet nam
	public static function unaccent($str)
	{
		$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
		$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
		$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
		$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
		$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
		$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
		$str = preg_replace("/(đ)/", 'd', $str);
		$str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ấ|Ặ|Ẳ|Ẵ)/", 'A', $str);
		$str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
		$str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
		$str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
		$str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
		$str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
		$str = preg_replace("/(Đ)/", 'D', $str);
		$str = preg_replace("/( )/", '_', $str);
		return $str;
	}

	public static function strtolowerv($str)
	{
		$arrayUpper = explode('|',preg_replace("/\n|\t|\r/","",self::upper));
		$arrayLower = explode('|',preg_replace("/\n|\t|\r/","",self::lower));
		return str_replace($arrayUpper,$arrayLower,$str);
	}

	public static function strtoupperv($str)
	{
		$arrayUpper = explode('|',preg_replace("/\n|\t|\r/","",self::upper));
		$arrayLower = explode('|',preg_replace("/\n|\t|\r/","",self::lower));
		return str_replace($arrayLower,$arrayUpper,$str);
	}
} // END class
?>