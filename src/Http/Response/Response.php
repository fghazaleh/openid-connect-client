<?php
/**
 * Created by PhpStorm.
 * User: Ghazaleh
 * Date: 3/6/18
 * Time: 10:38 AM
 */

namespace Asg\Http\Response;


class Response
{
    /**
     * @var int
     */
    private $statusCode;
    /**
     * @var mixed
     */
    private $content;

    /**
     * @param int $statusCode;
     * @param mixed $content
     * */
    function __construct($statusCode,$content)
    {
        $this->statusCode = $statusCode;
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }
}