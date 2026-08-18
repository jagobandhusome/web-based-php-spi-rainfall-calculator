<?php
class Gamma{

public static $_errorCodes    = array( 'null'                => '#NULL!',
                                         'divisionbyzero'    => '#DIV/0!',
                                         'value'            => '#VALUE!',
                                         'reference'        => '#REF!',
                                         'name'                => '#NAME?',
                                         'num'                => '#NUM!',
                                         'na'                => '#N/A',
                                         'gettingdata'        => '#GETTING_DATA'
                                       );



    public static function GAMMADIST($value,$a,$b,$cumulative) {
        $value    = self::flattenSingleValue($value);
        $a        = self::flattenSingleValue($a);
        $b        = self::flattenSingleValue($b);
 
        if ((is_numeric($value)) && (is_numeric($a)) && (is_numeric($b))) {
            if (($value < 0) || ($a <= 0) || ($b <= 0)) {
                return self::$_errorCodes['num'];
            }
            if ((is_numeric($cumulative)) || (is_bool($cumulative))) {
                if ($cumulative) {
                    return self::_incompleteGamma($a,$value / $b) / self::_gamma($a);
                } else {
                    return (1 / (pow($b,$a) * self::_gamma($a))) * pow($value,$a-1) * exp(0-($value / $b));
                }
            }
        }
        return self::$_errorCodes['value'];
    }    //    function GAMMADIST()



        public static function flattenSingleValue($value = '') {
        if (is_array($value)) {
            return self::flattenSingleValue(array_pop($value));
        }
        return $value;
    }    //    function flattenSingleValue()


        public static function _incompleteGamma($a,$x) {
        static $max = 32;
        $summer = 0;
        for ($n=0; $n<=$max; ++$n) {
            $divisor = $a;
            for ($i=1; $i<=$n; ++$i) {
                $divisor *= ($a + $i);
            }
            $summer += (pow($x,$n) / $divisor);
        }
        return pow($x,$a) * exp(0-$x) * $summer;
    }    //    function _incompleteGamma()


        public static function _gamma($data) {
        if ($data == 0.0) return 0;
 
        static $p0 = 1.000000000190015;
        static $p = array ( 1 => 76.18009172947146,
                            2 => -86.50532032941677,
                            3 => 24.01409824083091,
                            4 => -1.231739572450155,
                            5 => 1.208650973866179e-3,
                            6 => -5.395239384953e-6
                          );
 
        $y = $x = $data;
        $tmp = $x + 5.5;
        $tmp -= ($x + 0.5) * log($tmp);
 
        $summer = $p0;
        for ($j=1;$j<=6;++$j) {
            $summer += ($p[$j] / ++$y);
        }
        return exp(0 - $tmp + log(SQRT2PI * $summer / $x));
    }    //    function _gamma()


    public static function NORMINV($probability,$mean,$stdDev) {
        $probability    = self::flattenSingleValue($probability);
        $mean            = self::flattenSingleValue($mean);
        $stdDev            = self::flattenSingleValue($stdDev);
 
        if ((is_numeric($probability)) && (is_numeric($mean)) && (is_numeric($stdDev))) {
            if (($probability < 0) || ($probability > 1)) {
                return self::$_errorCodes['num'];
            }
            if ($stdDev < 0) {
                return self::$_errorCodes['num'];
            }
            return (self::_inverse_ncdf($probability) * $stdDev) + $mean;
        }
        return self::$_errorCodes['value'];
    }    //    function NORMINV()

    private static function _inverse_ncdf($p) {
        //    Inverse ncdf approximation by Peter J. Acklam, implementation adapted to
        //    PHP by Michael Nickerson, using Dr. Thomas Ziegler's C implementation as
        //    a guide. http://home.online.no/~pjacklam/notes/invnorm/index.html
        //    I have not checked the accuracy of this implementation. Be aware that PHP
        //    will truncate the coeficcients to 14 digits.
 
        //    You have permission to use and distribute this function freely for
        //    whatever purpose you want, but please show common courtesy and give credit
        //    where credit is due.
 
        //    Input paramater is $p - probability - where 0 < p < 1.
 
        //    Coefficients in rational approximations
        static $a = array(    1 => -3.969683028665376e+01,
                            2 => 2.209460984245205e+02,
                            3 => -2.759285104469687e+02,
                            4 => 1.383577518672690e+02,
                            5 => -3.066479806614716e+01,
                            6 => 2.506628277459239e+00
                         );
 
        static $b = array(    1 => -5.447609879822406e+01,
                            2 => 1.615858368580409e+02,
                            3 => -1.556989798598866e+02,
                            4 => 6.680131188771972e+01,
                            5 => -1.328068155288572e+01
                         );
 
        static $c = array(    1 => -7.784894002430293e-03,
                            2 => -3.223964580411365e-01,
                            3 => -2.400758277161838e+00,
                            4 => -2.549732539343734e+00,
                            5 => 4.374664141464968e+00,
                            6 => 2.938163982698783e+00
                         );
 
        static $d = array(    1 => 7.784695709041462e-03,
                            2 => 3.224671290700398e-01,
                            3 => 2.445134137142996e+00,
                            4 => 3.754408661907416e+00
                         );
 
        //    Define lower and upper region break-points.
        $p_low = 0.02425;            //Use lower region approx. below this
        $p_high = 1 - $p_low;        //Use upper region approx. above this
 
        if (0 < $p && $p < $p_low) {
            //    Rational approximation for lower region.
            $q = sqrt(-2 * log($p));
            return ((((($c[1] * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5]) * $q + $c[6]) /
                    (((($d[1] * $q + $d[2]) * $q + $d[3]) * $q + $d[4]) * $q + 1);
        } elseif ($p_low <= $p && $p <= $p_high) {
            //    Rational approximation for central region.
            $q = $p - 0.5;
            $r = $q * $q;
            return ((((($a[1] * $r + $a[2]) * $r + $a[3]) * $r + $a[4]) * $r + $a[5]) * $r + $a[6]) * $q /
                   ((((($b[1] * $r + $b[2]) * $r + $b[3]) * $r + $b[4]) * $r + $b[5]) * $r + 1);
        } elseif ($p_high < $p && $p < 1) {
            //    Rational approximation for upper region.
            $q = sqrt(-2 * log(1 - $p));
            return -((((($c[1] * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5]) * $q + $c[6]) /
                     (((($d[1] * $q + $d[2]) * $q + $d[3]) * $q + $d[4]) * $q + 1);
        }
        //    If 0 < p < 1, return a null value
        return self::$_errorCodes['null'];
    }    //    function _inverse_ncdf()
 
  } //End of the GAMMADIST Class.