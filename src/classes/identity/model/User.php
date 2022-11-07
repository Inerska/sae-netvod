<?php

namespace Application\identity\model;

use Exception;

class User
{

    private int $id;
    private string $email;

    //private int $role;

    public function __construct(int $id, string $email/*int $role*/)
    {
        $this->email = $email;
        $this->id = $id;
        //$this->role = $role;
    }

    /**
     * @throws Exception
     */
    public function __get(string $at): mixed
    {
        if (property_exists($this, $at)) {
            return $this->$at;
        }

        throw new Exception ("$at: invalid property");
    }


    /**
     * @throws Exception
     */
    public function __set(string $at, mixed $val): void
    {
        if (property_exists($this, $at)) {
            $this->$at = $val;
        } else {
            throw new Exception ("$at: invalid property");
        }
    }

    public function __isset(string $name): bool
    {
        return isset($this->$name);
    }


}