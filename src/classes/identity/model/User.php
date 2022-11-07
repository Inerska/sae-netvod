<?php

namespace Application\identity\model;

use Exception;

class User{

    private string $email;
    private string $mdp;


    public function __construct(string $email, string $mdp){
        $this->email = $email;
        $this->mdp = $mdp;
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