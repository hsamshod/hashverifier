<?php
    class DB
    {
        private $_db;
        static $_instance;

        private function __construct() {
            try {
                $this->_db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
                $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                print 'smth went wrong!';
                die();
            }
        }

        private function __clone(){}

        public static function getInstance() {
            if (!(self::$_instance instanceof self)) {
                self::$_instance = new self();
            }
            return self::$_instance;
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
        }

    }