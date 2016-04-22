<?php

//namespace DS_GOST_34_10_2012;
    class BitConverter {
        private static $maxLength = 70;

        public static function Get8Bytes($number = 0) {
            return self::unpack(self::pack($number),8);

        }

        public static function Get4Bytes($number = 0) {
            return self::unpack(self::pack($number),4);

        }

        public static function pack($number) {
            return pack("P", $number);
        }

        public static function unpack($str,$elemCnt = 0) {
            $elems = array_values(unpack("C*", $str));
            return $elemCnt ? array_slice($elems,0,$elemCnt) : $elems;
        }

        public static function byteArrayToGmp(Array $inData) {
            $dataLength = count($inData) >> 2;

            $leftOver = $dataLength & 0x3;
            if ($leftOver != 0)         // length not multiples of 4
                $dataLength++;


            if ($dataLength > self::$maxLength) {
                echo 'error on line '.__LINE__;
                die();
            }

            for ($i = count($inData) - 1, $j = 0; $i >= 3; $i -= 4, $j++) {
                $data[$j] = ($inData[$i - 3] << 24) + ($inData[$i - 2] << 16) + ($inData[$i - 1] << 8) + $inData[$i];
            }

            if ($leftOver == 1)
                $data[$dataLength - 1] = (integer)$inData[0];
                    else if ($leftOver == 2)
                $data[$dataLength - 1] = (integer)(($inData[0] << 8) + $inData[1]);
                    else if ($leftOver == 3)
                        $data[$dataLength - 1] = (integer)(($inData[0] << 16) + ($inData[1] << 8) + $inData[2]);

            while ($dataLength > 1 && $data[$dataLength - 1] == 0)
                $dataLength--;

            return gmp_init($data);
        }

        public static function getGmpFromBytes (Array $H) {
            $ret  = '';
            foreach ($H as $e) {
                $ret .= pack('C', $e);
            }
            return gmp_init(unpack('Q',$ret));
        }
    }

