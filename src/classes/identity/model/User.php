<?php

namespace Application\identity\model;

use Exception;

class User{

    private int $id;
    private string $email;
    private string $mdp;
    //private int $role;

    public function __construct(int $id, string $email, string $mdp /*int $role*/){
        $this->email = $email;
        $this->mdp = $mdp;
        $this->id = $id;
        //$this->role = $role;
    }

    public function __get(string $at):mixed {
        if (property_exists ($this, $at)) {
            return $this->$at;
        }else {
            throw new Exception ("$at: invalid property");
        }
    }


    public function __set(string $at,mixed $val):void {
        if ( property_exists ($this, $at) ) {
            $this->$at = $val;
        } else throw new Exception ("$at: invalid property");
    }



}