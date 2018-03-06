<?php
/**
 * Created by PhpStorm.
 * User: Ghazaleh
 * Date: 3/6/18
 * Time: 11:05 AM
 */

namespace Asg\Storage;


interface StorageInterface
{

    /**
     * @param string $key;
     * @return mixed|null;
     * */
    public function get($key);

    /**
     * @param string $key;
     * @param mixed $value;
     * @return StorageInterface;
     * */
    public function put($key,$value);

    /**
     * @param array $values;
     * @return StorageInterface;
     * */
    public function replace(array $values);
    /**
     * @return mixed[];
     * */
    public function all();

    /**
     * @param string $key;
     * @return boolean;
     * */
    public function has($key);

    /**
     * @param string $key;
     * @return boolean;
     * */
    public function delete($key);

    /**
     * @return StorageInterface;
     * */
    public function clear();

    /**
     * @return int;
     * */
    public function count();
}