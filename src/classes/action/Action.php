<?php

declare(strict_types=1);

namespace Application\action;

abstract class Action
{
    protected ?string $httpMethod = null;
    protected ?string $hostName = null;
    protected ?string $scriptName = null;

    public function __construct()
    {
        $this->httpMethod = $_SERVER['REQUEST_METHOD'];
        $this->hostName = $_SERVER['HTTP_HOST'];
        $this->scriptName = $_SERVER['SCRIPT_NAME'];
    }

    abstract public function execute(): string;
}