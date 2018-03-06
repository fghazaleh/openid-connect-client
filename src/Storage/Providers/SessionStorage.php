<?php
/**
 * Created by PhpStorm.
 * User: Ghazaleh
 * Date: 3/6/18
 * Time: 11:08 AM
 */

namespace Asg\Storage\Providers;


use Asg\Storage\StorageInterface;

class SessionStorage implements StorageInterface
{
    /**
     * @var string
     * */
    private $bucketId;

    /**
     * @param string $bucketId
     * */
    function __construct($bucketId = 'defaultBucketId')
    {
        $this->bucketId = $bucketId;
        $this->startSession();
    }

    /**
     * @param string $key ;
     * @return mixed|null;
     * */
    public function get($key)
    {
        if ( !$this->has($key) ){
            return null;
        }
        return $this->all()[$key];
    }

    /**
     * @param string $key ;
     * @param mixed $value ;
     * @return StorageInterface;
     * */
    public function put($key, $value)
    {
        $values = $this->all();
        $values[$key] = $value;
        $this->replace($values);
        return $this;
    }

    /**
     * @param array $values ;
     * @return StorageInterface;
     * */
    public function replace(array $values)
    {
        $_SESSION[$this->bucketId] = $values;
        return $this;
    }

    /**
     * @return mixed[];
     * */
    public function all()
    {
        return isset($_SESSION[$this->bucketId])?$_SESSION[$this->bucketId]:[];
    }

    /**
     * @param string $key ;
     * @return boolean;
     * */
    public function has($key)
    {
        return isset($this->all()[$key]);
    }

    /**
     * @param string $key ;
     * @return boolean;
     * */
    public function delete($key)
    {
        if ( !$this->has($key) )
        {
            return false;
        }
        $values = $this->all();
        unset($values[$key]);
        $this->replace($values);
        return true;
    }

    /**
     * @return StorageInterface;
     * */
    public function clear()
    {
        unset($_SESSION[$this->bucketId]);
        return $this;
    }

    /**
     * @return int;
     * */
    public function count()
    {
        return count($this->all());
    }

    /**
     * @return void;
     * */
    private function startSession()
    {
        if ( !isset($_SESSION) ) {
            @session_start();
        }
    }
}