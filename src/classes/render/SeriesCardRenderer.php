<?php

namespace Application\render;

use Application\datalayer\factory\ConnectionFactory;

class SeriesCardRenderer implements Renderer
{
    private int $seriesId;
    private ?string $cover;
    private ?string $title;
    private ?string $url;

    public function __construct(?string $cover, ?string $title, int $seriesId)
    {
        $this->cover = $cover;
        $this->title = htmlentities($title);
        $this->seriesId = $seriesId;
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $this->url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    public function render(): string
    {
        $user = unserialize($_SESSION['loggedUser'], ['allowed_classes' => true]);

        $db = ConnectionFactory::getConnection();
        $query = $db->prepare("SELECT * FROM user_serie_Pref WHERE idSerie = :serie_id AND idUser = :user_id");
        $query->execute([
            'serie_id' => $this->seriesId,
            'user_id' => $user->id
        ]);

        $html = <<<END
        <div class="w-max">
            <a href="?action=viewSerie&id={$this->seriesId}">
                <img class="h-58 w-40" src="$this->cover" alt="Couverture du film $this->title">
                <h3 class="dark:text-white text-gray-900 pt-2 leading-tight text-sm">$this->title</h3> 
            </a>
        END;

        if (!$query->fetch()) {
            $html .= <<<END

            <a href="?action=preferences&seriesId=$this->seriesId&url={$this->url}" class="p-2 hover:cursor-pointer fill-white text-white">
                <i class="fa-regular fa-heart"></i>
            </a>
            END;
            
        } else {
            $html .= <<<END

            <a href="?action=removePreferences&seriesId=$this->seriesId&url={$this->url}" class="p-2 hover:cursor-pointer fill-white text-white">
                <i class="fa-solid fa-heart"></i>
            </a>
            END;
        }

        $html .= '</div>';

        return $html;
    }
}