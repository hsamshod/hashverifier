<?php 
	function getPublicKey() {
		return file('keys/public.key');
	}
	function getSign($fileName = false) {
		if ($fileName) {
			$fullPath = SIGN_STORAGE.$fileName.SIGN_EXT;
			if(file_exists($fullPath)) {
				return file_get_contents($fullPath);
			}
		}
		return false;
	}
	function getUploadedFile($fileName = false) {
		if ($fileName) {
			$fullPath = FILE_STORAGE.$fileName;
			if(file_exists($fullPath)) {
				$file = file_get_contents($fullPath);
                deleteUpload($fullPath);
                return $file;
            }
		}
		return false;
	}
	function deleteUpload($fileName) {
		unlink($fileName);
	}
	function processPostData() {
		$f = $_FILES['file'];
		if ($f['error'] == 0) {
	        $tmp_name = $f['tmp_name'];
	        $name = $f['name'];
	        if (move_uploaded_file($tmp_name, FILE_STORAGE.$name)) {
	        	return $name;
	        }
	    }
		return false;
	}
	function view($fileName = '', $data = []) {
        global $l;
		extract($data);
        if ($fileName && file_exists(VIEWS_FOLDER.$fileName.'.php')) {
            include VIEWS_FOLDER.$fileName.'.php';
        }
    }

	/**
	 * @param $url  	File adress to get.
	 */
	function getFile($url) {
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
	 * @param $url  	Address of file sign of to get.
	 */
	function getFileSign($url = false) {
		$file_as_string = getFile($url.SIGN_EXT);
		$file_as_array = false;
		if ($file_as_string) {
			$file_as_array = explode("\n", $file_as_string);
		}
		return $file_as_array;
	}

	/**
	 * Get pair of open keys.
	 * @user
	 */
	function initDB() {
		try {
		    return new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
		} catch (PDOException $e) {
		    print 'smth went wrong!';
		    die();
		}
	}

	function closeDB($db) {
		$db = null;
	}

	function getKeys($db, $verifier_id) {
		$sql =  'select key1, key2 from '.DB_KEYS_TABLE.' '.
				'where userid = :verifier_id and cert_ending > now() and status in (:statuses) '.
				'limit 1';
		$sth = $db->prepare($sql);
		$sth->execute([
			':verifier_id' => $verifier_id, 
			':statuses' => implode(',', CERT_ALLOWED_STATUSES)
		]);
		return $sth->fetch();
	}

	function getVerifierData($db, $verifier_id) {
		$sql =  'select * from '.DB_CERT_TABLE.' '.
				'where cid = :verifier_id '.
				'limit 1';
		$sth = $db->prepare($sql);
		$sth->execute([
			':verifier_id' => $verifier_id 
		]);
		return $sth->fetch();
	}

	function verify() {
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
            	$db = initDB();
            	
            	list($sign, $verifier_id) = $data;
            	if (!($sign && $verifier_id)) {
                	return VERIFY_SIGN_ERR;
            	}
				
				list($x, $y) = getKeys($db);
				if (!($x && $y)) {
                	return VERIFY_KEY_ERR;
				}
				
				$Q = $DS->gDecompression();
				$Q->x = gmp_init('0x' . $x);
				$Q->y = gmp_init('0x' . $y);

				$result = $DS->verifDS($hash, $sign, $Q);

				if ($result == VERIFY_OK) {
	                return getVerifierData($db, $verifier_id);
	            } else {
	                return VERIFY_ERR;
	            }
            }
		}
	}