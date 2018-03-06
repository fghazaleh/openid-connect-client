<?php
/**
 * Created by PhpStorm.
 * User: Ghazaleh
 * Date: 3/6/18
 * Time: 12:29 PM
 */

namespace Asg\Support\Server;


final class Url
{

    /**
     * Thank you
     * http://stackoverflow.com/questions/189113/how-do-i-get-current-page-full-url-in-php-on-a-windows-iis-server
     * Compatibility with multiple host headers.
     * The problem with SSL over port 80 is resolved and non-SSL over port 443.
     * Support of 'ProxyReverse' configurations.
     *
     * @return string
     */

    public static function currentUrl()
    {
        if (isset($_SERVER["HTTP_UPGRADE_INSECURE_REQUESTS"]) && ($_SERVER['HTTP_UPGRADE_INSECURE_REQUESTS'] == 1)) {
            $protocol = 'https';
        } else {
            $protocol = @$_SERVER['HTTP_X_FORWARDED_PROTO']
                ?: @$_SERVER['REQUEST_SCHEME']
                    ?: ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? "https" : "http");
        }
        $port = @intval($_SERVER['HTTP_X_FORWARDED_PORT'])
            ?: @intval($_SERVER["SERVER_PORT"])
                ?: (($protocol === 'https') ? 443 : 80);
        $host = @explode(":", $_SERVER['HTTP_HOST'])[0]
            ?: @$_SERVER['SERVER_NAME']
                ?: @$_SERVER['SERVER_ADDR'];
        $port = (443 == $port) || (80 == $port) ? '' : ':' . $port;

        return sprintf('%s://%s%s/%s', $protocol, $host, $port,
            @trim(reset(explode("?", $_SERVER['REQUEST_URI'])), '/'));
    }

}