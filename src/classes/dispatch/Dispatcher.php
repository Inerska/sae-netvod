<?php

declare(strict_types=1);

namespace Application\dispatch;

use Application\action\DisplaySerieEpisodeAction;
use Application\action\DisplayUserLikesAction;
use Application\action\SigninAction;

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
            case 'sign-in':
                $act = new SigninAction();
                $html = $act->execute();
                break;
            case 'display-series-episode':
                $act = new DisplaySerieEpisodeAction();
                $html = $act->execute();
                break;

            case 'display-user-likes':
                $act = new DisplayUserLikesAction();
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