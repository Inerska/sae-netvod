<?php

namespace Application\action;

use Application\datalayer\repository\SeriesRepository;
use Application\render\SeriesCardRenderer;

class SearchSeriesAction extends Action
{
    public function execute(): string
    {
        return match ($this->httpMethod) {
            'POST' => $this->post(),
            default => $this->get(),
        };
    }

    private function post(): string
    {
        $search = filter_var($_POST['search'], FILTER_SANITIZE_SPECIAL_CHARS);
        $repository = new SeriesRepository();
        $series = $repository->getSeriesWith($search);

        $html = <<<END
        <form method="post">
            <input type="text" name="search" placeholder="Rechercher une série">
        </form>
        <script async>
            $('input[name="search"]').focus(); 
            $('input[name="search"]').val('{$search}');
            
            $(document).ready(function() {
                $('input[name="search"]').on('keyup', function() {
                    const value = $('input[name="search"]').val();
                    
                    if (value.length > 0) {
                        $.ajax({
                            url: '?action=search',
                            type: 'POST',
                            data: {
                                search: value
                            },
                            success: (html) => {
                                $('body').html(html);
                            }
                        });
                    }
                })
            })
        </script>
        <p>Vous avez recherché : {$search}</p>
        END;

        foreach ($series as $serie) {
            $renderer = new SeriesCardRenderer($serie['img'], $serie['titre'], $serie['id']);
            $html .= $renderer->render();
        }

        return $html;
    }

    private function get(): string
    {
        return <<<END
        <form method="post">
            <input type="text" name="search" placeholder="Rechercher une série">
        </form>
        <script async>
            const search = document.querySelector('input[name="search"]');
            
            search.focus();
            
            $(document).ready(function() {
                $('input[name="search"]').on('keyup', function() {
                    const value = $('input[name="search"]').val();
                    
                    if (value.length > 0) {
                        $.ajax({
                            url: '?action=search',
                            type: 'POST',
                            data: {
                                search: value
                            },
                            success: (html) => {
                                $('body').html(html);
                            }
                        });
                    }
                })
            })
        </script>
        END;
    }
}