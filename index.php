<?php
	include 'app/config.php';
	include 'app/utils.php';
	include 'app/app.php';

	if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']) {
		
		$result = verify();
		if (!is_array($result)) {
			$result = ['err' => $result];
		}	
		echo json_encode($result);
	}