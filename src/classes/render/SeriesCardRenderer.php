<?php

namespace Application\render;

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
        return <<<END
        <div>
            <a href="?action=viewSerie&id={$this->seriesId}">
                <img class="h-58 w-40" src="$this->cover" alt="Couverture du film $this->title">
                <h3 class="dark:text-white text-gray-900 pt-2 leading-tight text-sm">$this->title</h3> 
            </a>
        </div>
        END;
    }
}