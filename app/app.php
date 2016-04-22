<?php 
	function getPublicKey() : array {
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

	function deleteUpload($fileName) {
		unlink($fileName);
	}

	function verify() {
		$algo = isset($_POST['algo']) && $_POST['algo'] == 'old' ? 'old' : 'new';

		$p = gmp_init("57896044618658097711785492504343953926634992332820282019728792003956564821041");
		$a = gmp_init("7");
		$b = gmp_init("0x43308876546767276905765904595650931995942111794451039583252968842033849580414");
		$xG = [];
		$n = gmp_init("0x8000000000000000000000000000000150FE8A1892976154C59CFC193ACCF5B3");

		$strHash = new StringHash(512);
		$DS = new CDS($p, $a, $b, $n, $xG);

		if(!($uplodedFileName = processPostData())) {
			return VERIFY_FILE_ERR;
		} else {
			$uplodedFile = getUploadedFile($uplodedFileName);
			$hash = $strHash->GetGostHash($uplodedFile);

			if ($algo == 'old') {
				$message = BitConverter::unpack($uplodedFile);
				$hash_array = $strHash->GetHash($message);
			}
			

			if(!$sign = getSign($uplodedFileName)) {
                return VERIFY_SIGN_ERR;
            }

			list($x, $y) = getPublicKey();
			$Q = $DS->gDecompression();
			$Q->x = gmp_init('0x' . $x);
			$Q->y = gmp_init('0x' . $y);

			$result = $DS->verifDS($hash, $sign, $Q);

			if ($result == VERIFY_OK) {
                return VERIFY_OK;
            } else {
                return VERIFY_ERR;
            }
		}
	}

	function view($fileName = '', $data = []) {
        global $l;
		extract($data);
        if ($fileName && file_exists(VIEWS_FOLDER.$fileName.'.php')) {
            include VIEWS_FOLDER.$fileName.'.php';
        }
    }