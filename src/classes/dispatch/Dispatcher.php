<?php

declare(strict_types=1);

namespace Application\dispatch;

class Dispatcher
{
    private ?string $action = null;

    public function __construct(string $action)
    {
        $this->action = $action;
    }

    final public function dispatch(): void
    {
        switch ($this->action) {
            case 'ViewSerie':
                $act = new \Application\action\ViewSerieAction();
                $html = $act->execute();
                break;
            default:
                $html = "Hello World!";
                break;
        }

        $this->render($html);
    }

    private function render(string $template): void
    {
        echo $template;
    }
}