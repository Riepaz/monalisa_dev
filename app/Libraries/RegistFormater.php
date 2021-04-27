<?php

namespace App\Libraries;

class RegistFormater
{
		
	public static function format($number , $prefix , $infix) {
		$date = date('m');
		
		if($date == 1){
			$formated = "I";
		}else if($date == 2){
			$formated = "II";
		}else if($date == 3){
			$formated = "III";
		}else if($date == 4){
			$formated = "IV";
		}else if($date == 5){
			$formated = "V";
		}else if($date == 6){
			$formated = "VI";
		}else if($date == 7){
			$formated = "VII";
		}else if($date == 8){
			$formated = "VIII";
		}else if($date == 9){
			$formated = "IX";
		}else if($date == 10){
			$formated = "X";
		}else if($date == 11){
			$formated = "XI";
		}else if($date == 12){
			$formated = "XII";
		}
		return $number.'/'.$prefix.'/'.$infix.'/'.$formated.'/'.date('Y');		
	}

	
}
?>