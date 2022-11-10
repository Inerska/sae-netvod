<?php

namespace Application\action;

use Application\datalayer\factory\ConnectionFactory;
use Application\exception\datalayer\DatabaseConnectionException;
use Application\render\SeriesCardRenderer;
use PDO;

class ViewCatalogueAction extends Action
{

    private array $listeTri = [
        'id' => 'Aucun tri',
        'titre' => 'Titre',
        'annee' => 'Année de sortie',
        'date_ajout' => "Date d'ajout",
        'note_moyenne' => 'Note Moyenne'
    ];

    private array $listeGenre;
    private array $listeType;

    /**
     * @throws DatabaseConnectionException
     */
    public function execute(): string
    {

        $html = '';

        $tri = $_GET['tri'] ?? 'id';

        $db = ConnectionFactory::getConnection();

        $statement = $db->prepare("SELECT libelle FROM genre");
        $statement->execute();
        $this->listeGenre = $statement->fetchAll(PDO::FETCH_COLUMN);

        $statement = $db->prepare("SELECT libelle FROM type");
        $statement->execute();
        $this->listeType = $statement->fetchAll(PDO::FETCH_COLUMN);


        $searchQuery = "SELECT * FROM serie";

        $genre = $_GET['genre'] ?? null;

        if ($genre !== null) {
            $genres = explode(',', $genre);
            $searchQuery .= " INNER JOIN serie_genre ON serie.id = serie_genre.idSerie INNER JOIN genre ON serie_genre.idGenre = genre.idGenre";
        }

        $type = $_GET['type'] ?? null;

        if ($type !== null) {
            $types = explode(',', $type);
            $searchQuery .= " INNER JOIN serie_type ON serie.id = serie_type.idSerie INNER JOIN type ON serie_type.idType = type.idType";
        }

        if ($genre !== null || $type !== null) {
            $searchQuery .= " WHERE";
        }

        $filtreActif = '';

        if ($genre !== null) {
            $searchQuery .= " (";
            foreach ($genres as $key => $value) {
                if (in_array($value, $this->listeGenre)) {
                    unset($this->listeGenre[array_search($value, $this->listeGenre)]);
                    $searchQuery .= " genre.libelle = '$value'";

                    $filtreActif .= "<li>$value</li>";

                    if ($key !== count($genres) - 1) {
                        $searchQuery .= " OR";
                    }
                } else {
                    unset($genres[$key]);
                }
            }
            $searchQuery .= ")";
        }

        if ($type !== null) {
            if ($genre !== null) {
                $searchQuery .= " AND";
            }
            $searchQuery .= " (";
            foreach ($types as $key => $value) {
                if (in_array($value, $this->listeType)) {
                    unset($this->listeType[array_search($value, $this->listeType)]);
                    $searchQuery .= " type.libelle = '$value'";

                    $filtreActif .= "<li>$value</li>";

                    if ($key !== count($types) - 1) {
                        $searchQuery .= " OR";
                    }
                } else {
                    unset($types[$key]);
                }
            }
            $searchQuery .= ")";
        }


        $html .= <<<END
        <form method="get" action="index.php">
            <input type="hidden" name="action" value="viewCatalogue">
        END;

        if (isset($_GET['tri'])) {
            $html .= <<<END
            <input type="hidden" name="tri" value="{$_GET['tri']}">
            END;
        }

        if ($type !== null) {
            $html .= <<<END
            <input type="hidden" name="type" value="$type">
            END;
        }

        $html .= <<<END
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300" for="genrePrefere">Ajouter un filtre de genre</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="text" name="genre" id="genre"">
            <option value=""></option>
        END;

        foreach ($this->listeGenre as $value) {
            $html .= "<option value=\"{$value}\">{$value}</option>";
        }

        $html .= <<<END
            </select>
            <button class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">Filtrer</button>
            </form>
            END;

        $html .= <<<END
        <form method="get" action="index.php">
            <input type="hidden" name="action" value="viewCatalogue">
        END;

        if (isset($_GET['tri'])) {
            $html .= <<<END
            <input type="hidden" name="tri" value="{$_GET['tri']}">
            END;
        }

        if ($genre !== null) {
            $html .= <<<END
            <input type="hidden" name="genre" value="$genre">
            END;
        }

        $html .= <<<END
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300" for="genrePrefere">Ajouter un filtre de public</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="text" name="type" id="type"">
            <option value=""></option>
        END;

        foreach ($this->listeType as $value) {
            $html .= "<option value=\"{$value}\">{$value}</option>";
        }

        $html .= <<<END
            </select>
            <button class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">Filtrer</button>
            </form>
            END;



        $html .= <<<END
        <form method="get" action="index.php">
            <input type="hidden" name="action" value="viewCatalogue">
        END;

        if ($genre !== null) {
            $html .= <<<END
            <input type="hidden" name="genre" value="$genre">
            END;
        }

        if ($type !== null) {
            $html .= <<<END
            <input type="hidden" name="type" value="$type">
            END;
        }

        $html .= <<<END
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300" for="genrePrefere">Trier le catalogue</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="text" name="tri" id="tri"">
        END;

        if (array_key_exists($tri, $this->listeTri)) {
            $searchQuery .= " ORDER BY $tri";
            $html .= "<option value=\"{$tri}\">{$this->listeTri[$tri]}</option>";
            unset($this->listeTri[$tri]);
        } else {
            $html .= "<option value=\"id\">{$this->listeTri['id']}</option>";
            unset($this->listeTri['id']);
        }

        foreach ($this->listeTri as $key => $value) {
            $html .= "<option value=\"{$key}\">{$value}</option>";
        }

        $html .= <<<END
            </select>
            <button class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">Trier</button>
            </form>
            END;

        $query = $db->prepare($searchQuery);
        $query->execute();

        while ($result = $query->fetch()) {
            $seriesCard = new SeriesCardRenderer($result["img"], $result["titre"], $result["id"]);

            $html .= $seriesCard->render();
        }

        if ($html === "") {
            $html = "<p>Aucune série ne correspond à la recherche</p>";
        } else {
            $html = <<<END
            <div class="flex flex-wrap flex-row gap-10">
                {$html}
            </div>
            END;
        }

        return $html;
    }
}