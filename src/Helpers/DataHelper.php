<?php

namespace Core\Helpers;

class DataHelper implements \ArrayAccess
{
    /**
     * Where the data is stored
     *
     * @var array
     */
    protected $data = [];

    /**
     * DataHelper constructor.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Set the data
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Get the data
     */
    public function get($name, $default = null)
    {
        return $this->data[$name] ?? $default;
    }

    /*****************************************************************/
    /*                    Magic Method Implementation                */
    /*****************************************************************/

    /**
     * Set the data
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Get the data
     *
     * @param string $name
     * @return void
     */
    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return null;
    }

    /**
     * Check if the data is set
     *
     * @param string $name
     * @return void
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * Unset the data
     *
     * @param string $name
     * @return void
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    /**
     * Get the data as json string
     *
     * @return string
     */
    function __toString()
    {
        return json_encode($this->data);
    }

    /**
     * Call the data
     *
     * @param string $name
     * @param mixed $arguments
     * @return mixed
     */
    function __call($name, $arguments)
    {
        $default = isset($arguments[0]) ? $arguments[0] : null;

        if (isset($this->data[$name])) {
            return $this->data[$name] ?? $default;
        }

        return $default;
    }

    /*****************************************************************/
    /*                     Array Access Implementation               */
    /*****************************************************************/

    /**
     * Set the data
     *
     * @param string $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * Check if the data is set
     *
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * Unset the data
     *
     * @param string $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    /**
     * Get the data
     *
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    /*****************************************************************/
    /*                    Custom Method Implementation               */
    /*****************************************************************/

    /**
     * Make a new DataHelper instance
     *
     * @param array $data
     * @return self
     */
    public static function make($data = [])
    {
        return new static($data);
    }

    /**
     * Get the data as json string
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->data, JSON_PRETTY_PRINT);
    }

    /**
     * Get the data as array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }
}
