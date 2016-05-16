<?php

    class StringHash
    {
        //бинарная матрица A, используется для функции перемножения (L-преобразование).
        private $A;

        //таблица подстановок, используется для функции подстановки (S-преобразование).
        private $Sbox;

        //таблица перестановок, используется для функции перестановки (P-преобразование).
        private $Tau;

        //константный набор значений, для формирования временного ключа.
        private $C;

        private $iv = [];

        private $N = [];

        private $Sigma = [];

        public $outLen = 0;

        function __construct($outputLength) {
            global $A, $Sbox, $Tau, $C;

            $this->A = $A;
            $this->Sbox = $Sbox;
            $this->Tau = $Tau;
            $this->C = $C;

            if ($outputLength == 512) {
                for ($i = 0; $i < 64; $i++) {
                    $this->N[$i] = 0x00;
                    $this->Sigma[$i] = 0x00;
                    $this->iv[$i] = 0x00;
                }
                $this->outLen = 512;
            } else if ($outputLength == 256) {
                for ($i = 0; $i < 64; $i++) {
                    $this->N[$i] = 0x00;
                    $this->Sigma[$i] = 0x00;
                    $this->iv[$i] = 0x01;
                }
                $this->outLen = 256;
            }
        }

        private function AddModulo512 ($a, $b) {
            $temp = [];
            $i = 0; $t = 0;
            $tempA = [];
            $tempB = [];
            array_copy($a, 0, $tempA, 64 - count($a), count($a));
            array_copy($b, 0, $tempB, 64 - count($b), count($b));
            for ($i = 63; $i >= 0; $i--) {
                $t = $tempA[$i] + $tempB[$i] + ($t >> 8);
                $temp[$i] = ($t & 0xFF);
            }
            return $temp;
        }

        private function AddXor512($a, $b) {
            $c = [];
            for ($i = 0; $i < 64; $i++) {
                $c[$i] = ($a[$i] ^ $b[$i]);
            }
            return $c;
        }

        private function S($state) {
            $result = [];
            for ($i = 0; $i < 64; $i++)
                $result[$i] = $this->Sbox[$state[$i]];
            return $result;
        }

        private function P($state) {
            $result = [];
            for ($i = 0; $i < 64; $i++) {
                $result[$i] = $state[$this->Tau[$i]];
            }
            return $result;
        }

        private function L($state) {
            $result = [];
            for ($i = 0; $i < 8; $i++) {
                $t = 0;
                $tempArray = [];
                array_copy($state, $i * 8, $tempArray, 0, 8);
                $tempArray = array_reverse($tempArray);
                $tempBits1 = bit_array($tempArray);
                $tempBits = [];
                $tempBits = $tempBits1;    //$tempBits1.CopyTo($tempBits, 0);
                $tempBits = array_reverse($tempBits);
                for ($j = 0; $j < 64; $j++) {
                    if ($tempBits[$j] != false) {
                        $t = $t ^ $this->A[$j];
                    }
                }
                $ResPart = array_reverse(BitConverter::Get8Bytes($t));
                array_copy($ResPart, 0, $result, $i * 8, 8);
            }
            return $result;
        }

        private function KeySchedule ($K, $i) {
            $K = $this->AddXor512($K, $this->C[$i]);
            $K = $this->S($K);
            $K = $this->P($K);
            $K = $this->L($K);
            return $K;
        }

        private function E ($K, $m) {
            $state = $this->AddXor512($K, $m);
            for ($i = 0; $i < 12; $i++) {
                $state = $this->S($state);
                $state = $this->P($state);
                $state = $this->L($state);
                $K = $this->KeySchedule($K, $i);
                $state = $this->AddXor512($state, $K);
            }
            return $state;
        }

        private function G_n ($N, $h, $m) {
            $K = $this->AddXor512($h, $N);
            $K = $this->S($K);
            $K = $this->P($K);
            $K = $this->L($K);
            $t = $this->E($K, $m);
            $t = $this->AddXor512($t, $h);
            $newh = $this->AddXor512($t, $m);
            return $newh;
        }

        public function GetHash ($message) {
            $paddedMes = [];
            $len = count($message) * 8;
            $h = [];
            array_copy_n($this->iv, $h, 64);
            $N_0 = [
                0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,
                0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,
                0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,
                0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00
            ];
            if ($this->outLen == 512) {
                for ($i = 0; $i < 64; $i++) {
                    $this->N[$i] = 0x00;
                    $this->Sigma[$i] = 0x00;
                    $this->iv[$i] = 0x00;
                }
            }
            else if ($this->outLen == 256) {
                for ($i = 0; $i < 64; $i++) {
                    $this->N[$i] = 0x00;
                    $this->Sigma[$i] = 0x00;
                    $this->iv[$i] = 0x01;
                }
            }
            $N_512 = BitConverter::Get4Bytes(512);
            $inc = 0;
            while ($len >= 512) {
                $inc++;
                $tempMes = [];
                array_copy($message, count($message) - $inc*64, $tempMes, 0, 64);
                $h = $this->G_n($this->N, $h, $tempMes);
                $this->N = $this->AddModulo512($this->N, array_reverse($N_512));
                $this->Sigma = $this->AddModulo512($this->Sigma, $tempMes);
                $len -= 512;
            }
            $message1 = []; //[count($message) - $inc * 64];
            array_copy($message, 0, $message1, 0, count($message) - $inc * 64);
            if (count($message1) < 64) {
                for ($i = 0; $i < (64 - count($message1) - 1); $i++) {
                    $paddedMes[$i] = 0;
                }
                $paddedMes[64 - count($message1) - 1] = 0x01;
                array_copy($message1, 0, $paddedMes, 64 - count($message1), count($message1));
            }
            $h = $this->G_n($this->N, $h, $paddedMes);
            $MesLen = BitConverter::Get4Bytes(count($message1) * 8);
            $this->N = $this->AddModulo512($this->N, array_reverse($MesLen));
            $this->Sigma = $this->AddModulo512($this->Sigma, $paddedMes);
            $h = $this->G_n($N_0, $h, $this->N);
            $h = $this->G_n($N_0, $h, $this->Sigma);
            if ($this->outLen == 512) {
                return $h;
            } else {
                $h256 = [];
                array_copy($h, 0, $h256, 0, 32);
                return $h256;
            }
        }

        public function GetSHA512Hash($message) {
            return hash('SHA512', $message);
        }

        public function GetGostHash($message) {
            return hash('gost-crypto', $message);
        }
    }