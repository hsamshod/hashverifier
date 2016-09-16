<?php
    include 'app/app.php';

    const ADMIN_API_ALLOWED_ACTIONS = [
        'selectByStatus',
        'updateKeys',
        'saveFile',
        'updateStatus',
        'updateTimeStamps',
        'updateCert',
        'updateInn',
        'deleteCert',
    ];

    const USER_API_ALLOWED_ACTIONS = [
        'selectData',
        'selectById',
        'selectStatusById',
        'verifyByParams',
        'getDateDiff',
    ];

    const RAW_METHODS = [
        'selectData'
    ];

    function apiDebug() {
        if (API_DEBUG) {
            if (empty($_GET['ts']) || empty($_GET['tkn']) || empty($_GET['t']))
                echo json_encode('params err');
            elseif (time() < $_GET['ts'] || (time() - $_GET['ts']) > 2) {
                echo json_encode('timestamp err');
            } elseif ($_GET['tkn'] === hash('SHA512', $_GET['ts']. (isAdmin() ? APP_SECRET_ADMIN_MODULE : APP_SECRET_USER_MODULE))) {
                echo json_encode('token err: '.$_GET['tkn'] . '!=' . hash('SHA512', $_GET['ts'] . (isAdmin() ? APP_SECRET_ADMIN_MODULE : APP_SECRET_USER_MODULE)));
            }
        }
        die();
    }

    function isAdmin() {
        return $_GET['t'] == 'a';
    }

    function allowedMethods() {
        return $_GET['token'] ? ADMIN_API_ALLOWED_ACTIONS : USER_API_ALLOWED_ACTIONS;
    }

    function checkToken () {
        if (in_array($_GET['action'], ADMIN_API_ALLOWED_ACTIONS)) {
            return $_GET['token'] == APP_SECRET_ADMIN_MODULE;
        }
        return true;

        $timestamp  = $_GET['ts'];
        $token  = $_GET['tkn'];

        $return = time() >= $timestamp  && (time() - $timestamp) < 2 && $token === hash('SHA512', $timestamp . (isAdmin() ? APP_SECRET_ADMIN_MODULE : APP_SECRET_USER_MODULE));
        if (!$return) apiDebug();

        return $return;
    }

    if (isset($_GET['action']) && checkToken()) {
        $action = $_GET['action'];
        if (in_array($action, allowedMethods())) {
            if (is_callable($action) && function_exists($action)) {
                $params = isset($_GET['params']) ? $_GET['params'] : [];
                $result = $action($params);
                if (in_array($action, RAW_METHODS)) {
                    echo $result;
                } else {
                    echo json_encode($result);
                }
            }
        }
    }