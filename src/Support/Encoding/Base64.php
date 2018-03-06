<?php
/**
 * Created by PhpStorm.
 * User: Ghazaleh
 * Date: 3/6/18
 * Time: 10:46 AM
 */

namespace Asg\Support\Encoding;


final class Base64 {

    /**
     * A wrapper around base64_decode which decodes Base64URL-encoded data,
     * which is not the same alphabet as base64.
     *
     * @param string $base64url;
     * @return string
     */
    public static function toBase64urlDecode($base64url) {
        return base64_decode(static::base64urlToBase64($base64url));
    }

    /**
     * Per RFC4648, "base64 encoding with URL-safe and filename-safe
     * alphabet".  This just replaces characters 62 and 63.  None of the
     * reference implementations seem to restore the padding if necessary,
     * but we'll do it anyway.
     *
     * @param string $base64url;
     * @return string;
     */
    public static function base64urlToBase64($base64url) {
        // "Shouldn't" be necessary, but why not
        $padding = strlen($base64url) % 4;
        if ($padding > 0) {
            $base64url .= str_repeat("=", 4 - $padding);
        }
        return strtr($base64url, '-_', '+/');
    }

}