<?php

//namespace DS_GOST_34_10_2012;
    /*
     * Описание класса точки эллептической кривой заданной уравнением: y^2=x^3+ax+(b % p).
     */
    class CECPoint {
        public $a;
        public $b;
        public $x;
        public $y;
        public $fieldChar;

        public function __construct(CECPoint $p = null) {
            if ($p) {
                $this->a = $p->a;
                $this->b = $p->b;
                $this->x = $p->x;
                $this->y = $p->y;
                $this->fieldChar = $p->fieldChar;
            }
        }

        public static function sum (CECPoint $p1, CECPoint $p2) {
            $res = new CECPoint();
            $res->a = $p1->a;
            $res->b = $p1->b;
            $res->fieldChar = $p1->fieldChar;

            $dx = $p2->x - $p1->x;
            $dy = $p2->y - $p1->y;

            if ($dx < 0)
                $dx += $p1->fieldChar;
            if ($dy < 0)
                $dy += $p1->fieldChar;

            $t = ($dy * gmp_invert($dx, $p1->fieldChar)) % $p1->fieldChar;

            if ($t < 0)
                $t += $p1->fieldChar;

            $res->x = ($t * $t - $p1->x - $p2->x) % $p1->fieldChar;
            $res->y = ($t * ($p1->x - $res->x) - $p1->y) % $p1->fieldChar;

            if ($res->x < 0)
                $res->x += $p1->fieldChar;
            if ($res->y < 0)
                $res->y += $p1->fieldChar;

            return $res;
        }

        public static function doubling(CECPoint $p)
        {
            $res = new CECPoint;

            $res->a = $p->a;
            $res->b = $p->b;
            $res->fieldChar = $p->fieldChar;

            $dx = 2 * $p->y;
            $dy = 3 * $p->x * $p->x + $p->a;

            if ($dx < 0)
                $dx += $p->fieldChar;
            if ($dy < 0)
                $dy += $p->fieldChar;

            $t = ($dy * gmp_invert($dx, $p->fieldChar)) % $p->fieldChar;
            $res->x = ($t*$t - $p->x - $p->x) % $p->fieldChar;
            $res->y = ($t * ($p->x - $res->x) - $p->y) % $p->fieldChar;

            if ($res->x < 0)
                $res->x += $p->fieldChar;
            if ($res->y < 0)
                $res->y += $p->fieldChar;

            return $res;
        }

        public static function multiply(CECPoint $p, $c)
        {
            $res = $p;
            $c = $c - 1;
            while($c!=0)
            {
                if (($c%2) != 0)
                {
                    if (($res->x == $p->x) || ($res->y == $p->y))
                        $res = self::doubling($res);
                    else
                        $res = self::sum($res, $p);
                    $c = $c - 1;
                }

                $c = $c / 2;
                $p = self::doubling($p);
            }

            return $res;
        }
    }