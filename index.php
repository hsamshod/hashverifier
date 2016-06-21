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
		setFlash('showinfo',1);
		if (verifyCaptcha($_POST['captcha'])) {
//			dd($s = verifyFile());
			switch ($s = verifyFile()) {
				case VERIFY_FILE_ERR:
					setFlash('file_err',1);
					break;
				case VERIFY_KEY_ERR:
				case VERIFY_PARAM_ERR:
					setFlash('sign_not_found',1);
					break;
				default:
					if (is_array($s)) {
						if ($s['code'] == VERIFY_OK)
							setFlash('verified',1);
						else if ($s['code'] == VERIFY_ERR)
								setFlash('not_verified',1);
					}
			}
		} else {
			setFlash('captcha_err',1);
		}
	}

	$viewData = is_array($s) ? $s : [];

	view('content', $viewData);
