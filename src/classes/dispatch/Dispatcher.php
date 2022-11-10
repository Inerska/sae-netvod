<?php

declare(strict_types=1);

namespace Application\dispatch;

use Application\action\ActivationAction;
use Application\action\AddSeriesToPreferencesAction;
use Application\action\AjouterEpisodeAction;
use Application\action\AjouterSerieAction;
use Application\action\DisplaySerieAction;
use Application\action\DisplaySerieCommentairesAction;
use Application\action\DisplaySerieEnCours;
use Application\action\DisplaySerieEpisodeAction;
use Application\action\DisplayUserLikesAction;
use Application\action\DisplayViewedAction;
use Application\action\ProfileAction;
use Application\action\RemoveSeriesToPreferencesAction;
use Application\action\RenewAction;
use Application\action\RetirerSeriesAction;
use Application\action\SearchSeriesAction;
use Application\action\SigninAction;
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
            case 'sign-in':
                $act = new SigninAction();
                $html = $act->execute();
                break;

            case 'display-series-episode':
                $act = new DisplaySerieEpisodeAction();
                $html = $act->execute();
                break;

            case 'sign-up':
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

            case 'search':
                $action = new SearchSeriesAction();
                $html = $action->execute();
                $this->render_grip($html);
                return;

            case 'search-ajax':
                $action = new SearchSeriesAction();
                $html = $action->execute();
                $this->render_partial($html);
                return;

            case 'preferences':
                $act = new AddSeriesToPreferencesAction();
                $html = $act->execute();
                break;

            case 'commentaires':
                $act = new DisplaySerieCommentairesAction();
                $html = $act->execute();
                break;

            case 'removePreferences':
                $act = new RemoveSeriesToPreferencesAction();
                $html = $act->execute();
                break;

            case 'add-series':
                $act = new AjouterSerieAction();
                $html = $act->execute();
                break;

            case 'remove-series':
                $act = new RetirerSeriesAction();
                $html = $act->execute();
                break;

            case 'add-episodes':
                $act = new AjouterEpisodeAction();
                $html = $act->execute();
                break;



            default:

                $html ='';

                if (!isset($_SESSION['loggedUser'])) {
                    $html = <<<END
                                <div class="flex justify-center items-center flex-col h-screen pb-72">
                                    <div class="bg-gray-50 dark:bg-gray-700 p-10 w-1/2 flex items-center justify-center flex-col">
                                        <h1 class="text-dark text-4xl font-light pb-5 dark:text-white">Vous n'êtes pas connecté, veuillez vous connecter</h1>
                                    </div>
                                </div>
                                END;
                } else {
                    $html = "<div class='flex flex-col'>";
                    $action = new DisplayUserLikesAction();
                    $html .= $action->execute();
                    $action2 = new DisplayViewedAction();
                    $html .= $action2->execute();
                    $action3 = new DisplaySerieEnCours();
                    $html .= $action3->execute();
                    $html .= '</div>';
                }

                break;
        }

        $this->renderCore($html);
    }

    private function render_grip(string $template): void
    {
        $template = <<<END
           <div class='container w-screen'>
            $template 
            </div>
        END;

        $this->render($template);
    }

    private function render(string $template): void
    {
        require_once 'src/views/header.php';
        if(isset($_SESSION['loggedAdmin']) && $_SESSION['loggedAdmin'])
        require_once 'src/views/navAdmin.php';

        echo <<<END
            <!doctype html>
            <html lang="fr" class="transition-colors duration-300">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport"
                      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                <meta http-equiv="X-UA-Compatible" content="ie=edge">
                <title>NetVOD</title>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
                      integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
                      crossorigin="anonymous" referrerpolicy="no-referrer"/>
                <script src="https://code.jquery.com/jquery-3.6.1.min.js"
                        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
                <script src="js/app.js"></script>
                <link rel="stylesheet" href="css/tailwind.css">
            </head>
            <body class="bg-white dark:bg-gray-800 h-full transition-colors duration-700 antialiased">
                $template 
            </body>
            </html>
            END;
    }

    private function render_partial(string $template): void
    {
        echo $template;
    }

    private function renderCore(string $template): void
    {
        $template = <<<END
            <div class='container mx-auto pt-4 w-screen'> 
                $template 
            </div>
                        
            </body>
            </html>
        END;

        $this->render($template);
    }
}
