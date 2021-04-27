<?php

namespace App\Libraries;

class Currency
{

    public static function rupiahs($number){
	
		$result = "Rp " . number_format($number,2,',','.');
		return $result;
	 
	}
}
?>