<?php
/**
 * Created by PhpStorm.
 * User: Ghazaleh
 * Date: 3/5/18
 * Time: 1:40 PM
 */

namespace Asg\Http\Client;


use Asg\Http\Response\Response;

interface HttpClientInterface
{

    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_PUT = 'PUT';
    const HTTP_METHOD_PATCH = 'PATCH';
    const HTTP_METHOD_DELETE = 'DELETE';
    const HTTP_METHOD_HEAD = 'HEAD';


    /**
     * @param string $method;
     * @param string $url;
     * @param array $options;
     *
     * @return Response
     * */
    public function request($method, $url, array $options = []);

    /**
     * @param string $url;
     * @param array $options;
     *
     * @return Response
     * */
    public function get($url, array $options = []);

    /**
     * @param string $url;
     * @param array $options;
     *
     * @return Response
     * */
    public function post($url, array $options = []);

    /**
     * @param string $url;
     * @param array $options;
     *
     * @return Response
     * */
    public function put($url, array $options = []);

    /**
     * @param string $url;
     * @param array $options;
     *
     * @return Response
     * */
    public function patch($url, array $options = []);

    /**
     * @param string $url;
     * @param array $options;
     *
     * @return Response
     * */
    public function delete($url, array $options = []);

    /**
     * @param string $url;
     * @param array $options;
     *
     * @return Response
     * */
    public function head($url, array $options = []);

}