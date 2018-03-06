<?php
/**
 * Created by PhpStorm.
 * User: Ghazaleh
 * Date: 3/5/18
 * Time: 9:09 PM
 */

class HttpClientTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     * */
    function test_create_http_get_request()
    {
        $httpClient = new \Asg\Http\Client\HttpClient('http://google.com/');

        $output = $httpClient->get('',['query' => ['foo'=>'bar','faz'=>'baz']]);
        $this->assertEquals(200, $output->getStatusCode());
    }

    /**
     * @test
     * */
    function test_create_http_post_request()
    {
        $httpClient = new \Asg\Http\Client\HttpClient('http://google.com/');
        $output = $httpClient->post('',['form_params' => ['foo'=>'bar']]);
        //$this->assertEquals(200, $httpClient->getResponseCode());
    }

}