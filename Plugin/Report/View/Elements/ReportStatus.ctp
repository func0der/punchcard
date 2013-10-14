<?php
	if($report['Report']['accepted'] === true){
		echo __d('report', 'Accepted');
	}
	elseif($report['Report']['review'] === true){
		echo __d('report', 'Waiting for review');
	}
	elseif($report['Report']['published'] === true){
		echo __d('report', 'Published');
	}
	else{
		echo __d('report', 'Not published');
	}
