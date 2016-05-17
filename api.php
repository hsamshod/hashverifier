<?php
    include 'app/app.php';

    const API_ALLOWED_ACTIONS = [
        'selectByStatus',
        'updateKeys'
    ];

    function selectByStatus ($params = []) {
        $sql = 'select * '.
               'from cert c '.
               'join user_data ud on c.userid = ud.userid '.
               'where c.status = :status';
        return DB::query($sql, [':status' => 5])->fetchAll();
    }

    function updateKeys ($params = []) {
        $sql = 'update cert c '.
               'set c.cert = :cert, c.key1 = :key1, c.key2 = :key2, c.status = :status '.
               'where c.userid = :userid';
        return DB::query($sql, $params);
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