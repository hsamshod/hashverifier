<?php
    include 'app/app.php';

    const API_ALLOWED_ACTIONS = [
        'selectByStatus',
        'updateKeys'
    ];

    function selectByStatus ($params = []) {
        $sql = 'select c.cid, c.userid, c.cert_date, c.cert_ending,'.
                      'ud.postindex, ud.country_id, ud.region_id,'.
                      'ud.node_id, ud.city_id, ud.street_id, ud.house,'.
                      'ud.korp, ud.str, ud.edu_email, ud.edu_phone,'.
                      'ud.edu_boss, ud.edu_desc, ud.inn_kpp '.
               'from cert c '.
               'join user_data ud on c.userid = ud.userid '.
               'where c.status = :status';
        $result = DB::query($sql, $params);

        if ($result) {
            return $result->fetchAll(PDO::FETCH_OBJ);
        } else {
            return $result;
        }
    }

    function updateKeys ($params = []) {
        $sql = 'update cert c '.
               'set c.cid = :cid, c.cert = :cert, c.key1 = :key1, c.key2 = :key2, c.status = :status '.
               'where c.userid = :userid';
        
        return DB::query($sql, $params) ? 'ok' : 'error';
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