<?php

declare(strict_types=1);

namespace Application\dispatch;


use Application\action\ActivationAction;
use Application\action\DisplaySerieAction;
use Application\action\DisplaySerieEpisodeAction;
use Application\action\RenewAction;
use Application\action\DisplayUserLikesAction;
use Application\action\SigninAction;
use Application\action\SignupAction;
use Application\action\ViewCatalogueAction;
use Application\action\ProfileAction;
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

            case "sign-up":
                $action = new SignupAction();
                $html = $action->execute();
                break;

            case 'viewSerie':
                $act = new DisplaySerieAction();
                $html = $act->execute();
                break;

            case 'viewCatalogue':
                $action = new ViewCatalogueAction();
                $html = $action->execute();
                break;

            case 'profile':
                $action = new ProfileAction();
                $html = $action->execute();
                break;

            case 'sign-out':
                session_destroy();
                header('Location: index.php');
                exit();
                break;

            case 'activation':
                $action = new ActivationAction();
                $html = $action->execute();
                break;

            case 'renew':
                $action = new RenewAction();
                $html = $action->execute();
                break;
                
            default:
                $action = new DisplayUserLikesAction();
                $html = $action->execute();
                break;
        }

        $this->render($html);
    }

    private function render(string $template): void
    {
        echo $template;
    }
}