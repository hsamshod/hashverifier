<?php
	include 'app/config.php';
	include 'app/utils.php';
	include 'app/app.php';

	if ($_POST['check']) {
		$time_start = microtime(true);
		switch ($s = verify()) {
			case VERIFY_FILE_ERR:
				setFlash('error',1);
				break;
			case VERIFY_SIGN_ERR:
				setFlash('sign_not_found',1);
				break;
			case VERIFY_ERR:
				setFlash('not_verified',1);
				break;
			case VERIFY_OK:
				setFlash('verified',1);
				break;
		}
		$work_time = microtime(true) - $time_start;
		setFlash('work_time', $work_time);
		redirect('/');
	}

	$viewData = [];
	if (hasFlash('verified')) {
		$viewData = array_merge($viewData, parse_ini_file('certinfo/certificate.txt'));
	}

	view('content', $viewData);