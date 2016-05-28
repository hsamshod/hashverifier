<?php
	foreach (['config', 'utils', 'db'] as $file) {
		include dirname(__FILE__).'/'.$file.'.php';
	}

	/**
	 * @param string $url  	File adress to get.
	 *
	 * @return string 		File content
	 */
	function getFile ($url) {
		$opts = [
			'http' => [
				'method'  =>  'GET',
				'header'  =>  'User-agent:EduSite Site Checker v1.2',
				'timeout' =>  20
			]
		];

		$context = stream_context_create($opts);
		return file_get_contents($url, false, $context);
	}
	
	/**
	 * @param string $url  	 Address of file sign of to get.
	 *
	 * @return array 		 File in array form.
	 */
	function getFileSign ($url) {
		$file_as_string = getFile($url.SIGN_EXT);
		$file_as_array = false;
		if ($file_as_string) {
			$file_as_array = explode("\n", $file_as_string);
		}
		return $file_as_array;
	}

	/**
	 * @param resource $db			DB handle.
	 * @param int $verifier_id		Verifier id.
	 *
	 * @return array|false			Verifier data.
	 */
	function getKeys ($verifier_id) {
		$sql =  'select key1, key2 from '.CERT_DB_KEYS_TABLE.' '.
				'where userid = :verifier_id and cert_ending > now() and status in (:statuses) '.
				'limit 1';
		$sth = CERT_DB::exec($sql, [
			':verifier_id' => $verifier_id,
			':statuses' => implode(',', CERT_ALLOWED_STATUSES)
		]);

		if ($sth->num_rows) {
			return $sth->fetch();
		}
		return false;
	}

	/**
	 * fucked up writing docs.
	 */
	function getVerifierData ($verifier_id) {
		$sql =  'select * from '.CERT_DB_CERT_TABLE.' '.
				'where cid = :verifier_id '.
				'limit 1';
		$sth = CERT_DB::exec($sql, [
			':verifier_id' => $verifier_id 
		]);
		return $sth->fetch();
	}

	function verify () {
		$url = urldecode($_SERVER['QUERY_STRING']);
		if (!filter_var($url, FILTER_VALIDATE_URL)) {
			return VERIFY_PARAM_ERR; 
		}

		$p = gmp_init("57896044618658097711785492504343953926634992332820282019728792003956564821041");
		$a = gmp_init("7");
		$b = gmp_init("0x43308876546767276905765904595650931995942111794451039583252968842033849580414");
		$xG = [];
		$n = gmp_init("0x8000000000000000000000000000000150FE8A1892976154C59CFC193ACCF5B3");

		$strHash = new StringHash(512);
		$DS = new CDS($p, $a, $b, $n, $xG);
		
		if(!($fileToVerify = getFile($url))) {
			return VERIFY_FILE_ERR;
		} else {
			$hash = $strHash->GetGostHash($fileToVerify);			
			if(!$data = getFileSign($url)) {
                return VERIFY_SIGN_ERR;
            } else {
            	list($sign, $verifier_id) = $data;
            	if (!($sign && $verifier_id)) {
                	return VERIFY_SIGN_ERR;
            	}
				
				list($x, $y) = getKeys($verifier_id);
				if (!($x && $y)) {
                	return VERIFY_KEY_ERR;
				}
				
				$Q = $DS->gDecompression();
				$Q->x = gmp_init('0x' . $x);
				$Q->y = gmp_init('0x' . $y);

				$result = $DS->verifDS($hash, $sign, $Q);

				if ($result == VERIFY_OK) {
                    $return = getVerifierData($verifier_id);
	            } else {
                    $return = VERIFY_ERR;
	            }

                return $return;
            }
		}
	}