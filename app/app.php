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

	/**
	 * API functions
	 */
	
	function buildName($type_id, $name, $type) {
		if ($type_id == 1029559 || $type_id == 1029562 || ($type_id > 1029566 && $type_id < 1029616)
			|| $type_id == 1029618 || ($type_id > 1029621 && $type_id < 1029635) || $type_id == 1029639
			|| ($type_id > 1029641 && $type_id < 1029648) || ($type_id > 1029651 && $type_id < 1029656)
			|| $type_id == 1029657 || $type_id == 1029662 || ($type_id > 1029665 && $type_id < 1029669)
			|| ($type_id > 1029670 && $type_id < 1029674)) {
			return "$type $name";
		} else {
			return "$name $type";
		}
	}

	function getAddress($user = []) {
		if (is_array($user) && !empty($user)) {
			$user = (object)$user;
			$address = [];

			if($user->postindex > 0){
				$address['postindex'] = $user->postindex;
			}

			if($user->country_id > 0){
				$sql = 'SELECT r.type_id, r.name, t.short_name '.
					'FROM '.ADDR_DB_REGIONS_TABLE.' r '.
					'INNER JOIN '.ADDR_DB_POST_TYPE_TABLE.' t ON r.type_id = t.id '.
					'WHERE r.id= :country_id';

				$data = ADDR_DB::query($sql, [':country_id' => $user->country_id])->fetch(PDO::FETCH_OBJ);
				$address['country_id'] = buildName($data->type_id, $data->name, $data->short_name);
			}

			if($user->region_id > 0){
				$sql = 'SELECT r.type_id, r.name, t.short_name '.
					'FROM '.ADDR_DB_REGIONS_TABLE.' r '.
					'INNER JOIN '.ADDR_DB_POST_TYPE_TABLE.' t ON r.type_id = t.id '.
					'WHERE r.id = :region_id';
				$data = ADDR_DB::query($sql, [':region_id' => $user->region_id])->fetch(PDO::FETCH_OBJ);
				$address['region'] = buildName($data->type_id, $data->name, $data->short_name);
			}

			if($user->node_id > 0){
				$sql = 'SELECT r.type_id, r.name,t.short_name '.
					'FROM '.ADDR_DB_REGIONS_TABLE.' r '.
					'INNER JOIN '.ADDR_DB_POST_TYPE_TABLE.' t ON r.type_id = t.id '.
					'WHERE r.id = :node_id';
				$data = ADDR_DB::query($sql, [':node_id' => $user->node_id])->fetch(PDO::FETCH_OBJ);
				$address['node'] = buildName($data->type_id, $data->name, $data->short_name);
			}

			if($user->city_id > 0){
				$sql = 'SELECT r.type_id, r.name, t.short_name '.
					'FROM '.ADDR_DB_REGIONS_TABLE.' r '.
					'INNER JOIN '.ADDR_DB_POST_TYPE_TABLE.' t ON r.type_id = t.id '.
					'WHERE r.id = :city_id';
				$data = ADDR_DB::query($sql, [':city_id' => $user->city_id])->fetch(PDO::FETCH_OBJ);
				$address['city'] = buildName($data->type_id, $data->name, $data->short_name);
			}

			if($user->street_id > 0){
				$sql = 'SELECT r.type_id, r.name, t.short_name '.
					'FROM '.ADDR_DB_REGIONS_TABLE.' r '.
					'INNER JOIN '.ADDR_DB_POST_TYPE_TABLE.' t ON r.type_id = t.id '.
					'WHERE r.id = :street_id';
				$data = ADDR_DB::query($sql, [':street_id' => $user->street_id])->fetch(PDO::FETCH_OBJ);
				$address['street'] = buildName($data->type_id, $data->name, $data->short_name);
			}

			if(strlen($user->house) > 0){
				$address['house'] = 'дом '.$user->house;
			}

			if($user->korp != '-' && $user->korp != ''){
				$address['korp'] = 'корп. '.$user->korp;
			}

			if($user->str != '-' && $user->str != ''){
				$address['str'] = 'стр. '.$user->str;
			}

			return implode(', ', $address);
		}
		return false;
	}

	function selectByStatus ($params = []) {
		$sql = 'select c.cid, c.userid, from_unixtime(c.cert_date) as cert_date, from_unixtime(c.cert_ending) as cert_ending,'.
			'ud.postindex, ud.country_id, ud.region_id,'.
			'ud.node_id, ud.city_id, ud.street_id, ud.house,'.
			'ud.korp, ud.str, ud.edu_email, ud.edu_phone,'.
			'ud.edu_boss, ud.edu_name, ud.inn_kpp '.
			'from cert c '.
			'join user_data ud on c.userid = ud.userid '.
			'where c.status = :status';
		$result = CERT_DB::query($sql, $params);

		if ($result) {
			$data = $result->fetchAll(PDO::FETCH_OBJ);

			/* получаем адреса */
			foreach ($data as &$d) {

				$d->address = getAddress([
					'postindex' => $d->postindex, 'country_id' => $d->country_id,
					'region_id' => $d->region_id, 'node_id' => $d->node_id,
					'city_id' => $d->city_id, 'street_id' => $d->street_id,
					'house' => $d->house, 'korp' => $d->korp, 'str' => $d->str
				]);
			}
			return $data;
		} else {
			return $result;
		}
	}

	function updateKeys ($params = []) {
		$sql =  'update cert c '.
			    'set c.cert = :cert, c.key1 = :key1, c.key2 = :key2, c.status = :status '.
			    'where c.userid = :userid and c.cid = :cid';
	
		return CERT_DB::query($sql, $params) ? STATUS_OK : STATUS_ERR;
	}

	function updateStatus ($params = []) {
		$sql =  'update cert c '.
                'set c.status = :status '.
                'where c.userid = :userid and c.cid = :cid';

		return CERT_DB::query($sql, $params) ? STATUS_OK : STATUS_ERR;
	}

	function saveFile($params = []) {
		if (isset($params['file_name'])) {
			$file_name = str_replace('/', '_', $params['file_name']) . CERT_FILE_EXT;

			unset($params['file_name']);
			$content_as_array = [];
			foreach ($params as $key => $val) {
				if (in_array($key, CERT_FILE_ALLOWED_DATA)) {
					$content_as_array[] = $key . ' = '. $val;
				}
			}

			return file_put_contents(
				CERT_FILE_FOLDER.'/'.$file_name,
				implode("\n", $content_as_array)
			) ? STATUS_OK : STATUS_ERR;
		}

		return STATUS_ERR;
	}
