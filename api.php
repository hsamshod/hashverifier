<?php
    include 'app/app.php';

    const API_ALLOWED_ACTIONS = [
        'selectByStatus',
        'selectById',
        'updateKeys',
        'saveFile',
        'updateStatus',
        'selectStatusById',
        'verifyByParams'
    ];

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