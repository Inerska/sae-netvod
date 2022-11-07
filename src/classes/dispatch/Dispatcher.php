<?php

declare(strict_types=1);

namespace Application\dispatch;

use Application\action\SignupAction;
use Application\action\ViewCatalogueAction;
use Application\exception\datalayer\DatabaseConnectionException;

class Dispatcher
{
    private ?string $action = null;

    public function __construct(string $action)
    {
        $this->action = $action;
    }

    /**
     * @throws DatabaseConnectionException
     */
    final public function dispatch(): void
    {
        switch ($this->action) {
            case "sign-up":
                $action = new SignupAction();
                $html = $action->execute();
                break;
                
            case 'viewCatalogue':
                $action = new ViewCatalogueAction();
                $html = $action->execute();
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