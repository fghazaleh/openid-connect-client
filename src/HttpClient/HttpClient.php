<?php
/**
 * Created by PhpStorm.
 * User: Ghazaleh
 * Date: 3/5/18
 * Time: 1:47 PM
 */

namespace Asg\HttpClient;


class HttpClient implements HttpClientInterface
{

    function __construct()
    {
        if (!function_exists('curl_init')) {
            throw new \RuntimeException('HttpClient needs the CURL PHP extension.');
        }
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
        // TODO: Implement request() method.
    }

    /**
     * @param string $url ;
     * @param array $options ;
     *
     * @return mixed
     * */
    public function get($url, array $options = [])
    {
        // TODO: Implement get() method.
    }

    /**
     * @param string $url ;
     * @param array $options ;
     *
     * @return mixed
     * */
    public function post($url, array $options = [])
    {
        // TODO: Implement post() method.
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
     * @param array $options
     * @return bool
     */
    private function ssl(array $options)
    {
        return (bool)(isset($options['ssl'])?$options['ssl']:false);
    }

    /**
     * @param array $options
     * @return array
     */
    private function getHeaders(array $options)
    {
        return (array)isset($options['header'])?$options['header']:[];
    }

    /**
     * @param array $options
     * @return array
     */
    private function getQueryString(array $options)
    {
        return (array)isset($options['query'])?$options['query']:[];
    }

    /**
     * @param array $options
     * @return array
     */
    private function getFormParams(array $options)
    {
        return (array)isset($options['form_params'])?$options['form_params']:[];
    }
}