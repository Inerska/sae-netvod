<?php

namespace Application\exception;

class EpisodeNotFoundException extends \Exception
{

    /**
     * @param string $string
     */
    public function __construct(string $string)
    {
        parent::__construct($string);
    }
}