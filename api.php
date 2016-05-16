<?php
    include 'app/app.php';

    const API_ALLOWED_ACTIONS = [
        'select',
        'insert',
        'update'
    ];

//    function select () {
//        return Database::getInstance()->query('select * from cert');
//    }
//
//    function insert () {
//        return Database::getInstance()->query('select * from cert');
//    }
//
//    function update () {
//        return Database::getInstance()->query('select * from cert');
//    }

    if (isset($_POST['action']) && checkToken()) {
        $action = $_POST['action'];

        if (in_array($action, API_ALLOWED_ACTIONS)) {
            if (is_callable($action) && function_exists($action)) {
                //@todo process functions
                $result = $action();
                echo json_encode($result);
            }
        }
    }