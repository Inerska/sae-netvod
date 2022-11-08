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
        <div class="p-5 rounded border">
            <a href="?action=viewSerie&id={$this->seriesId}">
                <img class="h-48 w-48" src="$this->cover" alt="Couverture du film $this->title">
                <h3>$this->title</h3> 
            </a>
        </div>
        END;
    }
}