<?php

namespace App\Libraries;

class Date
{

    public static function date_format($date) {
		$bulan = date("m", strtotime($date));
		
		if($bulan == 1){
			$formated = "Januari";
		}else if($bulan == 2){
			$formated = "Februari";
		}else if($bulan == 3){
			$formated = "Maret";
		}else if($bulan == 4){
			$formated = "April";
		}else if($bulan == 5){
			$formated = "Mei";
		}else if($bulan == 6){
			$formated = "Juni";
		}else if($bulan == 7){
			$formated = "Juli";
		}else if($bulan == 8){
			$formated = "Agustus";
		}else if($bulan == 9){
			$formated = "September";
		}else if($bulan == 10){
			$formated = "Oktober";
		}else if($bulan == 11){
			$formated = "November";
		}else if($bulan == 12){
			$formated = "Desember";
		}

		if(date("D", strtotime($date)) == "Sun"){
			$day = "Minggu";
		}
		else if(date("D", strtotime($date)) == "Mon"){
			$day = "Senin";
		}
		else if(date("D", strtotime($date)) == "Tue"){
			$day = "Selasa";
		}
		else if(date("D", strtotime($date)) == "Wed"){
			$day = "Rabu";
		}
		else if(date("D", strtotime($date)) == "Thu"){
			$day = "Kamis";
		}
		else if(date("D", strtotime($date)) == "Fri"){
			$day = "Jumat";
		}
		else if(date("D", strtotime($date)) == "Sat"){
			$day = "Sabtu";
		}

		return $day .", ".date("d", strtotime($date)) ." ". $formated ." ". date("Y", strtotime($date));		
	}
	
	public static function dateAndTime_format($date) {
		$bulan = date("m", strtotime($date));
		
		if($bulan == 1){
			$formated = "Januari";
		}else if($bulan == 2){
			$formated = "Februari";
		}else if($bulan == 3){
			$formated = "Maret";
		}else if($bulan == 4){
			$formated = "April";
		}else if($bulan == 5){
			$formated = "Mei";
		}else if($bulan == 6){
			$formated = "Juni";
		}else if($bulan == 7){
			$formated = "Juli";
		}else if($bulan == 8){
			$formated = "Agustus";
		}else if($bulan == 9){
			$formated = "September";
		}else if($bulan == 10){
			$formated = "Oktober";
		}else if($bulan == 11){
			$formated = "November";
		}else if($bulan == 12){
			$formated = "Desember";
		}
		return date("d", strtotime($date)) ." ". $formated ." ". date("Y", strtotime($date)) .' / '.date("h:m:s", strtotime($date)) ;		
	}
	
	public static function dateAndTime_unformat($date) {
		return date("Y", strtotime($date)) . "-".  date("m", strtotime($date)) ."-". date("d", strtotime($date));		
	}
	
	public static function romanMonth_format($date) {
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
		return $formated;		
	}

	public static function durationDayBetween($dateStart ,  $dateEnd , $additional) {
		$startTimeStamp = strtotime(date("Y/m/d H:i:m", strtotime($dateStart)));
		$endTimeStamp = strtotime(date("Y/m/d H:i:m", strtotime($dateEnd)));

		$timeDiff = abs($endTimeStamp - $startTimeStamp);

		$numberOfDiffm = intval($timeDiff / (86400 / 24 / 60));
		$numberOfDiffH = intval($timeDiff / (86400 / 24));
		$numberOfDiffD = intval($timeDiff / (86400));
		$numberOfDiffM = intval($timeDiff / (86400 * 30));
		$numberOfDiffY = intval($timeDiff / (86400 * 30 * 12));
		$numberOfDiffDecade = intval($timeDiff / (86400 * 30 * 12 * 10));
		$numberOfDiffCentury = intval($timeDiff / (86400 * 30 * 12 * 10 * 10));
		
		if($numberOfDiffm < 60){
			$numberOfDiff = $numberOfDiffm." Menit yang Lalu";
		}
		
		if($numberOfDiffm >= 60 && $numberOfDiffH > 0){
			$numberOfDiff = $numberOfDiffH." Jam yang Lalu";
		}
		
		if($numberOfDiffH >= 24 && $numberOfDiffD > 0){
			$numberOfDiff = $numberOfDiffD." Hari yang Lalu";
		}
		
		if($numberOfDiffD >= 30 && $numberOfDiffM > 0){
			$numberOfDiff = $numberOfDiffM." Bulan yang Lalu";
		}
		
		if($numberOfDiffM >= 12 && $numberOfDiffY > 0){
			$numberOfDiff = $numberOfDiffY." Tahun yang Lalu";
		}
		
		if($numberOfDiffY >= 10 && $numberOfDiffDecade > 0){
			$numberOfDiff = $numberOfDiffDecade." Dekade yang Lalu";
		}
		
		if($numberOfDiffDecade >= 10 && $numberOfDiffCentury > 0){
			$numberOfDiff = $numberOfDiffCentury." Abad yang Lalu";
		}
		

		return $numberOfDiff;
	}

	public static function difference($dateStart ,  $dateEnd) {
		$startTimeStamp = strtotime(date("Y/m/d H:i:m", strtotime($dateStart)));
		$endTimeStamp = strtotime(date("Y/m/d H:i:m", strtotime($dateEnd)));

		$timeDiff = $endTimeStamp - $startTimeStamp;
		$numberOfDiffm = intval($timeDiff / (86400 / 24 / 60));

		return $numberOfDiffm;
	}
	
}
?>