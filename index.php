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
        if (verifyCaptcha($_POST['g-recaptcha-response'])) {
            setFlash('verify_result', verifyFile());
		} else {
			setFlash('verify_captcha', ['error' => true]);
		}
	}

	view('content');
