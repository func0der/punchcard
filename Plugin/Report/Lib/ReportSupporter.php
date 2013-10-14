<?php
/**
 * ReportSupporter
 *
 * @author Lars Lenecke
 * @package Plugin.Report.Lib
 */
class ReportSupporter{
/**
 * Validate given time
 *
 * @param string $check The time to check
 * @return boolean
 */
	public static function validation_time($check){
		// Check for the right format
		$result = (preg_match('/^(\d{1,2}):(\d{2})$/', $check, $matches) === 1);

		// Check for right number range
		if($result){
			$hours = $matches[1];
			$minutes = $matches[2];
			/*
			 * We check the time given is somehow out of range.
			 * Checking < 0 is some kind of ambigious, because reg ex would fail
			 * with negative values. But in case we miss some special escape hacking
			 * we check it again anyway.
			 */
			if(
				// Hour (0 - 23)
				$hours < 0 || $hours > 24 ||
				// Minutes (0 - 59)
				$minutes < 0 || $minutes > 59
			){
				$result = false;
			}
		}
		
		return $result;
	}
	
/**
 * Convert time (hh:mm) to sql decimal representation
 *
 * @param string $time
 * @return decimal
 */
	public static function timeToDecimal($time){
		if(!self::validation_time($time)){
			return null;
		}

		$result = explode(':', $time);

		// Invalid content
		if(count($result) > 2){
			// We are ignoring everthing after the first two entries
			$result = array(
				$result[0],
				$result[1]
			);
		}
		elseif(!isset($result[1])){
			$result[1] = 0;
		}

		// We have hours and minutes here
		if(count($result) > 1){
			$result[1] = $result[1] / 60;
		}

		return $result[0] + $result[1];
	}

/**
 * Convert sql decimal to time (hh:mm) representation
 *
 * @param string $time
 * @return decimal
 */
	public static function decimalToTime($decimal){
		$result = $decimal;
		
		// Remember negativ decimal and prepend minus infront of result later
		$prefix = '';
		if($decimal < 0){
			$prefix = '-';
			$result = abs($result);
		}
		
		$result = explode('.', $result);

		// Invalid content
		if(count($result) > 2){
			// We are ignoring everthing after the first two entries
			$result = array(
				$result[0],
				$result[1]
			);
		}
		elseif(!isset($result[1])){
			$result[1] = 0;
		}
		
		// Zeros at the end of decimals are getting trimmed. Add them again to get a right calucation result.
		$result[1] = str_pad($result[1], 2, '0', STR_PAD_RIGHT);

		// We have hours and minutes here
		if(count($result) > 1){
			$result[1] = ($result[1] * 60) / 100;
		}
		
		// Prepend zeros
		if($result[0] < 10){
			$result[0] = '0' . $result[0];
		}
		if($result[1] < 10){
			$result[1] = '0' . $result[1];
		}

		return $prefix . implode(':', $result);
	}
}
?>