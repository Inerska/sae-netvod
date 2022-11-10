<?php

namespace Application\render;

use Application\datalayer\factory\ConnectionFactory;

class SeriesCardRenderer implements Renderer
{
    private int $seriesId;
    private ?string $cover;
    private ?string $title;
    private ?string $url;
    private int $annee;

    public function __construct(?string $cover, ?string $title, int $seriesId, int $annee)
    {
        $this->cover = $cover;
        $this->title = htmlentities($title);
        $this->seriesId = $seriesId;
        $this->annee = $annee;
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $this->url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
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
        <div class="w-max hover:bg-gray-50 p-2 dark:hover:bg-gray-700" id="seriesCard">
            <a href="?action=viewSerie&id={$this->seriesId}"> 
                <img class="h-58 w-40" src="$this->cover" alt="Couverture du film $this->title">
                <h3 class="dark:text-white text-gray-900 pt-2 leading-tight text-sm font-sans">$this->title</h3> 
                <span class="text-emerald-500 pt-2 leading-tight text-sm font-sans">$this->annee - </span>
            </a>
        END;

        $url = urlencode($this->url);



        if (!$query->fetch()) {
            $html .= <<<END

            <a href="?action=preferences&seriesId=$this->seriesId&url={$url}" class="p-2 hover:cursor-pointer fill-white text-white">
                <i class="fa-regular fa-heart text-rose-600"></i>
            </a>
            END;
            
        } else {
            $html .= <<<END

            <a href="?action=removePreferences&seriesId=$this->seriesId&url={$url}" class="p-2 hover:cursor-pointer fill-white text-white">
            <div class="flex h-3 w-3">
                <i class="absolute inline-flex fa-solid fa-heart text-rose-600 hover:scale-125 animate-ping"></i>
                <i class="relative inline-flex fa-solid fa-heart text-rose-600 hover:scale-125"></i>
            </div>
                
            </a>
            END;
        }

        $html .= '</div>';

        return $html;
    }
}