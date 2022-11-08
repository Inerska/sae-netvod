<?php

namespace Application\render;

use Application\datalayer\factory\ConnectionFactory;

class SeriesCardRenderer implements Renderer
{
    private int $seriesId;
    private ?string $cover;
    private ?string $title;

    public function __construct(?string $cover, ?string $title, int $seriesId)
    {
        $this->cover = $cover;
        $this->title = htmlentities($title);
        $this->seriesId = $seriesId;
    }

    public function render(): string
    {

        $user = unserialize($_SESSION['loggedUser'], ['allowed_classes' => true]);

        $db = ConnectionFactory::getConnection();
        $query = $db->prepare("SELECT * FROM user_serie_pref WHERE idSerie = :serie_id AND idUser = :user_id");
        $query->execute([
            'serie_id' => $this->seriesId,
            'user_id' => $user->id
        ]);

        $html = <<<END
        <div>
            <a href="?action=viewSerie&id={$this->seriesId}">
                <img class="h-58 w-40" src="$this->cover" alt="Couverture du film $this->title">
                <h3 class="dark:text-white text-gray-900 pt-2 leading-tight text-sm">$this->title</h3> 
            </a>
        END;

        if (!$query->fetch()) {
            $html .= "<a href=\"?action=preferences&seriesId=$this->seriesId\" class=\"bg-blue-800 p-2 text-white hover:bg-blue-900 hover:cursor-pointer\">+ préférence</a>";
        }
        $html .= '</div>';

        return $html;
    }
}