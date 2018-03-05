<?php
/**
 * Created by PhpStorm.
 * User: Ghazaleh
 * Date: 3/5/18
 * Time: 1:47 PM
 */

//https://github.com/jumbojett/OpenID-Connect-PHP/blob/master/src/OpenIDConnectClient.php

namespace Asg\HttpClient;


class HttpClient implements HttpClientInterface
{
    /**
     * @var null|string
     */
    private $baseUri;
    /**
     * @var int
     */
    private $timeout;

    /**
     * @var mixed|null
     * */
    private $content = null;

    /**
     * @var int|null
     * */
    private $responseCode = null;
    /**
     * @param string|null $baseUri
     * @param int $timeout (timeout in seconds)
     */
    function __construct($baseUri = null, $timeout = 60)
    {
        if (!function_exists('curl_init')) {
            throw new \RuntimeException('HttpClient needs the CURL PHP extension.');
        }
        $this->baseUri = $baseUri;
        $this->timeout = $timeout;
    }
    /**
     * @param string $method ;
     * @param string $url ;
     * @param array $options ;
     *
     * @return mixed
     * */
    public function request($method, $url, array $options = [])
    {
        // Reset request from prev. values if any to default values
        $this->resetRequest();

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
        $this->setResponseCode($info['http_code']);
        if ($output === false) {
            throw new \RuntimeException('Curl error: ' . curl_error($ch));
        }
        $this->setContent($output);
        // Close the cURL resource, and free system resources
        curl_close($ch);
        return $output;
    }

    /**
     * @param string $url ;
     * @param array $options ;
     *
     * @return mixed
     * */
    public function get($url, array $options = [])
    {
        return $this->request(self::HTTP_METHOD_GET,$url,$options);
    }

    /**
     * @param string $url ;
     * @param array $options ;
     *
     * @return mixed
     * */
    public function post($url, array $options = [])
    {
        return $this->request(self::HTTP_METHOD_POST,$url,$options);
    }

    /**
     * @param string $url ;
     * @param array $options ;
     *
     * @return mixed
     * */
    public function put($url, array $options = [])
    {
        throw new \RuntimeException('Implement put() method');
    }

    /**
     * @param string $url ;
     * @param array $options ;
     *
     * @return mixed
     * */
    public function patch($url, array $options = [])
    {
        throw new \RuntimeException('Implement patch() method');
    }

    /**
     * @param string $url ;
     * @param array $options ;
     *
     * @return mixed
     * */
    public function delete($url, array $options = [])
    {
        throw new \RuntimeException('Implement delete() method');
    }

    /**
     * @param string $url ;
     * @param array $options ;
     *
     * @return mixed
     * */
    public function head($url, array $options = [])
    {
        throw new \RuntimeException('Implement head() method');
    }


    /**
     * @return int|null
     * */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @return mixed|null
     * */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Used to reset the prev. request before create a new request
     *
     * @return void;
     * */
    private function resetRequest()
    {
        $this->setContent(null);
        $this->setResponseCode(null);
    }
    /**
     * @param int|null
     * */
    private function setResponseCode($code)
    {
        $this->responseCode = $code;
    }

    /**
     * @param mixed|null
     * */
    private function setContent($content)
    {
        $this->content = $content;
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