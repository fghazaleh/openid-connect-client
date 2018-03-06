<?php
/**
 * Created by PhpStorm.
 * User: Ghazaleh
 * Date: 3/6/18
 * Time: 11:53 AM
 */

namespace Asg\Support\String;


final class String
{
    /**
     * Safely calculate length of binary string
     * @param string
     * @return int
     */
    public static function length($str)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($str, '8bit');
        }
        return strlen($str);
    }

    /**
     * Where has_equals is not available, this provides a timing-attack safe string comparison
     * @param $str1
     * @param $str2
     * @return bool
     */
    public static function hashEquals($str1, $str2)
    {
        $len1=static::length($str1);
        $len2=static::length($str2);
        //compare strings without any early abort...
        $len = min($len1, $len2);
        $status = 0;
        for ($i = 0; $i < $len; $i++) {
            $status |= (ord($str1[$i]) ^ ord($str2[$i]));
        }
        //if strings were different lengths, we fail
        $status |= ($len1 ^ $len2);
        return ($status === 0);
    }
}