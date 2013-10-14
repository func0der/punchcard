<?php
	// This element is a little dirty since I in the "else" case there is too much logic.
	if($this->Time->isToday($date)){
		echo __d('report', 'Today');
	}
	elseif($this->Time->wasYesterday($date)){
		echo __d('report', 'Yesterday');
	}
	else{
		$d = $this->Time->format($date, "%w");
		$day = array(
			__d('cake', 'Sunday'),
			__d('cake', 'Monday'),
			__d('cake', 'Tuesday'),
			__d('cake', 'Wednesday'),
			__d('cake', 'Thursday'),
			__d('cake', 'Friday'),
			__d('cake', 'Saturday')
		);
		echo $this->Time->format(
			$date,
			__c(
				'default_day_date_format',
				CAKE_LC_TIME,
				$day[$d]
			)
			
		);
	}
?>