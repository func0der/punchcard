<?php
	// To make this less complicated we are using the given callback method to make an object out of the json code
	echo $this->request->query['callback'] . '(' . $this->Js->object($labels) .')';
	exit;
?>