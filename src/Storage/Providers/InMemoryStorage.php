<?php
/**
 * Created by PhpStorm.
 * User: Ghazaleh
 * Date: 3/6/18
 * Time: 11:31 AM
 */

namespace Asg\Storage\Providers;


use Asg\Storage\StorageInterface;

class InMemoryStorage implements StorageInterface
{

    private $session = [];

    /**
     * @param string $key ;
     * @return mixed|null;
     * */
    public function get($key)
    {
        return $this->has($key)?$this->session[$key]:null;
    }

    /**
     * @param string $key ;
     * @param mixed $value ;
     * @return StorageInterface;
     * */
    public function put($key, $value)
    {
        $this->session[$key] = $value;
    }

    /**
     * @param array $values ;
     * @return StorageInterface;
     * */
    public function replace(array $values)
    {
        foreach($values as $k => $v){
            $this->session[$k] = $v;
        }
    }

    /**
     * @return mixed[];
     * */
    public function all()
    {
        return $this->session;
    }

    /**
     * @param string $key ;
     * @return boolean;
     * */
    public function has($key)
    {
        return isset($this->session[$key]);
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
        unset($this->session[$key]);
    }

    /**
     * @return StorageInterface;
     * */
    public function clear()
    {
        $this->session = [];
        return $this;
    }

    /**
     * @return int;
     * */
    public function count()
    {
        return count($this->all());
    }
}