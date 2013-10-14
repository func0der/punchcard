<?php
	$difference = $timeWorked - Configure::read('Time.WorkingHours');

	if($difference > 0){
		$prefix = '+';
		$spanClass = 'positive';
	}
	elseif($difference == 0){
		$prefix = '&plusmn;';
		$spanClass = 'neutral';
	}
	else{
		$prefix = '';
		$spanClass = 'negative';
	}

	$difference = $difference;
?>
<span class="<?php echo $spanClass; ?>">
<?php
	echo $prefix . ReportSupporter::decimalToTime($difference);
?>
</span>