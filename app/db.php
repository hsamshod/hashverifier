<?php

/**
 * Class DB for interacting with db.
 * @depency app\utils
 */

    class DB
    {
        protected $_db;
        static $_instance;

        protected function __construct($dbHost, $dbName, $dbUser, $dbPass) {
            try {
                $this->_db = new PDO('mysql:host='.$dbHost.';dbname='.$dbName.';charset=UTF8', $dbUser, $dbPass);
                $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                dd(static::class.' : smth went wrong!');
            }
        }

        private function __clone(){}

        public static function getInstance() {
            if (!(static::$_instance instanceof static)) {
                static::$_instance = new static();
            }
            return static::$_instance;
        }

        public static function query($sql, $params = []) {
            try {
                $sth = self::getInstance()->_db->prepare($sql);
                $sth->execute($params);
                return $sth;
            } catch (PDOException $e) {
                file_put_contents(
                    time().'.log',
                    "sql: ".var_export($sql, 1).
                    "\nparams: ".var_export($params,1).
                    "\nmessage: ".$e->getMessage()
                );
            }

            return false;
        }

    }

    class CERT_DB extends DB
    {
        protected function __construct() {
            parent::__construct(CERT_DB_HOST, CERT_DB_NAME, CERT_DB_USER, CERT_DB_PASS);
        }
    }

    class ADDR_DB extends DB
    {
        protected function __construct() {
            parent::__construct(ADDR_DB_HOST, ADDR_DB_NAME, ADDR_DB_USER, ADDR_DB_PASS);
        }
    }