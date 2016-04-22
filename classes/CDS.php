<?php

//namespace DS_GOST_34_10_2012;

class CDS {
    private $a;
    private $b;
    private $n;
    private $p;
    private $xG;
    private $G;

    public function __construct($p, $a, $b, $n, $xG = []) {
        $this->a = $a;
        $this->b = $b;
        $this->n = $n;
        $this->p = $p;
        $this->xG = $xG;
    }

    public function genPublicKey($d) {
        $G = $this->gDecompression();
        $Q = CECPoint::multiply($G, $d);
        return $Q;
    }

    public function gDecompression() { //:CECPoint
        $G = new CECPoint;
        $G->a = $this->a;
        $G->b = $this->b;
        $G->fieldChar = $this->p;
        $G->x = 2;
        $G->y = gmp_init("4018974056539037503335449422937059775635739389905545080690979365213431566280");
        $this->G = $G;
        return $G;
    }

    public function verifDS(String $H, String $sign, $Q)
    {
        $n_bitCount = 256; //strlen(gmp_strval($this->n));
        $n_bitCount_div_4 = $n_bitCount / 4;
        $Rvector = substr($sign, 0, $n_bitCount_div_4);
        $Svector = substr($sign, $n_bitCount_div_4, $n_bitCount_div_4);

        $r = gmp_init('0x'.$Rvector); // $Rvector is hex
        $s = gmp_init('0x'.$Svector); // $Svector is hex

        if (($r < 1) || ($r > ($this->n - 1)) || ($s < 1) || ($s > ($this->n - 1)))
            return false;

        $a = gmp_init('0x'.$H);
        $e = gmp_mod($a, $this->n);
        if ($e == 0)
            $e = 1;

        $v = gmp_invert($e, $this->n);

        $z1 = ($s * $v) % $this->n;
        $z2 = $this->n + (-1 *(($r * $v) % $this->n));
        $this->G = $this->gDecompression();
        $A = CECPoint::multiply($this->G, $z1);
        $B = CECPoint::multiply($Q, $z2);
        $C = CECPoint::sum($A, $B);
        $R = $C->x % $this->n;
        if ($R == $r)
            return VERIFY_OK;
        else
            return VERIFY_ERR;
    }
}