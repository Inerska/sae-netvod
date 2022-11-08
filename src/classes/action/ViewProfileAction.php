<?php

namespace Application\action;

class ViewProfileAction extends Action
{
    public function execute(): string
    {
        $html = <<<END
        <div class="flex flex-col items-center">
            <h1 class="text-3xl">Mon profil</h1>
            <div class="flex flex-col items-center">
                <div class="flex flex-col items-center">
                    <h2 class="text-2xl">Mes informations</h2>
                    <div class="flex flex-col items-center">
                        <div class="flex flex-col items-center">
                            <h3 class="text-xl">Nom d'utilisateur</h3>
                            <p>{$_SESSION['username']}</p>
                        </div>
                        <div class="flex flex-col items-center">
                            <h3 class="text-xl">Adresse e-mail</h3>
                            <p>{$_SESSION['email']}</p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <h2 class="text-2xl">Mes abonnements</h2>
                    <div class="flex flex-col items-center">
                        <div class="flex flex-col items-center">
                            <h3 class="text-xl">Séries</h3>
                            <p>TODO</p>
                        </div>
                        <div class="flex flex-col items-center">
                            <h3 class="text-xl">Films</h3>
                            <p>TODO</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        END;

        return $html;
    }
}