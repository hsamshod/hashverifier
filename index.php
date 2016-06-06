<?php
	include 'app/app.php';

	if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']) {
		
		$result = verify();
		if (!is_array($result)) {
			$result = ['err' => $result];
		}

		view('splash', $result);
		exit();
	}

	if ($_POST['check']) {
		if (verifyCaptcha($_POST['captcha'])) {
			switch ($s = verifyFile()) {
				case VERIFY_FILE_ERR:
					setFlash('error',1);
					break;
				case VERIFY_SIGN_ERR:
					setFlash('sign_not_found',1);
					break;
				case VERIFY_ERR:
					setFlash('not_verified',1);
					break;
				default:
					setFlash('verified',1);
					break;
			}
		} else {
			setFlash('captcha_err',1);
		}
	}

	$viewData = is_array($s) ? $s : [];

	view('content', $viewData);
