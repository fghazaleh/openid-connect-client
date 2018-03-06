<?php
/**
 * Created by PhpStorm.
 * User: Ghazaleh
 * Date: 3/5/18
 * Time: 1:47 PM
 */

//https://github.com/jumbojett/OpenID-Connect-PHP/blob/master/src/OpenIDConnectClient.php

namespace Asg\Http\Client;


use Asg\Http\Response\Response;

class HttpClient implements HttpClientInterface
{
    /**
     * @var null|string
     */
    private $baseUri = null;
    /**
     * @var int
     */
    private $timeout;

    /**
     * @param string|null $baseUri
     * @param int $timeout (timeout in seconds)
     */
    function __construct($baseUri = null, $timeout = 60)
    {
        if (!function_exists('curl_init')) {
            throw new \RuntimeException('HttpClient needs the CURL PHP extension.');
        }
        if (parse_url($baseUri, PHP_URL_HOST) !== false) {
            $this->baseUri = $baseUri;
        }
        $this->timeout = $timeout;
    }
    /**
     * @param string $method ;
     * @param string $url ;
     * @param array $options ;
     *
     * @return Response
     * */
    public function request($method, $url, array $options = [])
    {
        $headers = $this->getHeaders($options);
        $url = $this->getFullUrl($url,$this->getQueryString($options));

        $ch = curl_init();

        switch($method) {
            case self::HTTP_METHOD_POST: //POST
                $post_body = $this->getFormParams($options);

                $post_body = http_build_query($post_body,null,'&');

                // curl_setopt($ch, CURLOPT_POST, 1);
                // allows to keep the POST method even after redirect
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
                // Default content type is form encoded
                $content_type = 'application/x-www-form-urlencoded';
                // Determine if this is a JSON payload and add the appropriate content type
                if (is_object(json_decode($post_body))) {
                    $content_type = 'application/json';
                }
                // Add POST-specific headers
                $headers[] = "Content-Type: {$content_type}";
                $headers[] = 'Content-Length: ' . strlen($post_body);

                break;
            default; //OTHER ELSE
        }

        // If we set some headers include them
        if(count($headers) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        // Set URL to download
        curl_setopt($ch, CURLOPT_URL, $url);
        if (isset($this->httpProxy)) {
            curl_setopt($ch, CURLOPT_PROXY, $this->httpProxy);
        }
        // Include header in result? (0 = yes, 1 = no)
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // Allows to follow redirect
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        /**
         * Set cert
         * Otherwise ignore SSL peer verification
         */
        /*if (isset($this->certPath) ) {
            curl_setopt($ch, CURLOPT_CAINFO, $this->certPath);
        }*/

        if( $this->sslHost($options) ) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }
        if( $this->sslPeer($options) ) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        // Should cURL return or print out the data? (true = return, false = print)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        // Download the given URL, and return output
        $output = curl_exec($ch);
        // HTTP Response code from server may be required from subclass
        $info = curl_getinfo($ch);

        if ($output === false) {
            throw new \RuntimeException('Curl error: ' . curl_error($ch));
        }
        // Close the cURL resource, and free system resources
        curl_close($ch);
        return new Response($info['http_code'],$output);
    }

    /**
     * @param string $url ;
     * @param array $options ;
     *
     * @return Response
     * */
    public function get($url, array $options = [])
    {
        return $this->request(self::HTTP_METHOD_GET,$url,$options);
    }

    /**
     * @param string $url ;
     * @param array $options ;
     *
     * @return Response
     * */
    public function post($url, array $options = [])
    {
        return $this->request(self::HTTP_METHOD_POST,$url,$options);
    }

    /**
     * @param string $url ;
     * @param array $options ;
     *
     * @return Response
     * */
    public function put($url, array $options = [])
    {
        throw new \RuntimeException('Implement put() method');
    }

    /**
     * @param string $url ;
     * @param array $options ;
     *
     * @return Response
     * */
    public function patch($url, array $options = [])
    {
        throw new \RuntimeException('Implement patch() method');
    }

    /**
     * @param string $url ;
     * @param array $options ;
     *
     * @return Response
     * */
    public function delete($url, array $options = [])
    {
        throw new \RuntimeException('Implement delete() method');
    }

    /**
     * @param string $url ;
     * @param array $options ;
     *
     * @return Response
     * */
    public function head($url, array $options = [])
    {
        throw new \RuntimeException('Implement head() method');
    }

    /**
     * @param array $options
     * @return bool
     */
    private function sslHost(array $options)
    {
        return (bool)(isset($options['ssl_host'])?$options['ssl_host']:false);
    }

    /**
     * @param array $options
     * @return bool
     */
    private function sslPeer(array $options)
    {
        return (bool)(isset($options['ssl_peer'])?$options['ssl_peer']:false);
    }

    /**
     * @param array $options
     * @return array
     */
    private function getHeaders(array $options)
    {
        return (array)(isset($options['headers'])?$options['headers']:[]);
    }

    /**
     * @param array $options
     * @return array
     */
    private function getQueryString(array $options)
    {
        return (array)(isset($options['query'])?$options['query']:[]);
    }

    /**
     * @param array $options
     * @return array
     */
    private function getFormParams(array $options)
    {
        return (array)(isset($options['form_params'])?$options['form_params']:[]);
    }

    /**
     * @param string $url;
     * @param array $queryString;
     * @return string
     * */
    private function getFullUrl($url,$queryString = [])
    {
        if ($this->baseUri != null){
            $url = rtrim($this->baseUri,'/').'/'.ltrim($url,'/');
        }
        $params = http_build_query($queryString);
        if ( !empty($params) )
        {
            if (substr($url,-1) == '?'){
                $url.='&'.$params;
            }elseif (substr($url,-1) == '&')
            {
                $url.='&'.$params;
            }else{
                $url.='?'.$params;
            }
        }
        return $url;
    }

}