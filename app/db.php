<?php
    class Database
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

        public function query($sql) {
            return $this->_db->query($sql);
        }

        public function exec($sql, $params = []) {
            $sth = $this->_db->prepare($sql);
            return $sth->execute($params);
        }

    }