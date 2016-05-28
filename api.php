<?php
    include 'app/app.php';

    const API_ALLOWED_ACTIONS = [
        'selectByStatus',
        'updateKeys'
    ];

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
        $sql = 'select c.cid, c.userid, c.cert_date, c.cert_ending,'.
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
        $sql = 'update cert c '.
               'set c.cid = :cid, c.cert = :cert, c.key1 = :key1, c.key2 = :key2, c.status = :status '.
               'where c.userid = :userid';

        return CERT_DB::query($sql, $params) ? 'ok' : 'error';
    }

    function checkToken () {
        /**
         * @todo finish
         */
        return true;
    }

    if (isset($_GET['action']) && checkToken()) {
        $action = $_GET['action'];
        if (in_array($action, API_ALLOWED_ACTIONS)) {
            if (is_callable($action) && function_exists($action)) {
                $params = isset($_GET['params']) ? $_GET['params'] : [];
                $result = $action($params);
                echo json_encode($result);
            }
        }
    }