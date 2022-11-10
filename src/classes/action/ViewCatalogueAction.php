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


        $searchQuery = "SELECT distinct id, titre, img, annee FROM serie";

        $genre = $_GET['genre'] ?? null;

        $nbPossibleGenre = 0;
        $nbPossibleType = 0;

        if ($genre !== null) {
            $genres = explode(',', $genre);

            foreach ($genres as $value) {
                if (in_array($value, $this->listeGenre)) {
                    $nbPossibleGenre++;
                }
            }

            if ($nbPossibleGenre > 0) {
                $searchQuery .= " INNER JOIN serie_genre ON serie.id = serie_genre.idSerie INNER JOIN genre ON serie_genre.idGenre = genre.idGenre";
            }
        }

        $type = $_GET['type'] ?? null;

        if ($type !== null) {
            $types = explode(',', $type);

            foreach ($types as $value) {
                if (in_array($value, $this->listeType)) {
                    $nbPossibleType++;
                }
            }

            if ($nbPossibleType > 0) {
                $searchQuery .= " INNER JOIN serie_type ON serie.id = serie_type.idSerie INNER JOIN type ON serie_type.idType = type.idType";
            }
        }

        if ($nbPossibleGenre > 0 || $nbPossibleType > 0) {
            $searchQuery .= " WHERE";
        }

        $filtreActif = [];

        if ($nbPossibleGenre > 0) {
            $searchQuery .= " (";
            $realise = 0;
            foreach ($genres as $key => $value) {
                if (in_array($value, $this->listeGenre)) {
                    unset($this->listeGenre[array_search($value, $this->listeGenre)]);
                    $searchQuery .= " genre.libelle = '$value'";

                    $filtreActif [] = $value;

                    $realise++;

                    if ($realise < $nbPossibleGenre) {
                        $searchQuery .= " OR";
                    }
                } else {
                    unset($genres[$key]);
                }
            }
            $searchQuery .= ")";
        }

        if ($nbPossibleType > 0) {
            if ($genre !== null) {
                $searchQuery .= " AND";
            }
            $searchQuery .= " (";
            $realise = 0;
            foreach ($types as $key => $value) {
                if (in_array($value, $this->listeType)) {
                    unset($this->listeType[array_search($value, $this->listeType)]);
                    $searchQuery .= " type.libelle = '$value'";

                    $filtreActif[] = $value;

                    $realise++;

                    if ($realise < $nbPossibleType) {
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

        $triQuerry = '';

        if (array_key_exists($tri, $this->listeTri)) {
            $triQuerry .= " ORDER BY $tri";
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


        if (count($this->listeGenre) > 0) {
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
            END;

            foreach ($this->listeGenre as $value) {
                if ($genre !== null) {
                    $html .= "<option value=\"{$genre},{$value}\">{$value}</option>";
                } else {
                    $html .= "<option value=\"{$value}\">{$value}</option>";
                }
            }

            $html .= <<<END
            </select>
            <button class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">Filtrer</button>
            </form>
            END;
        }


        if (count($this->listeType) > 0) {

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
            END;

            foreach ($this->listeType as $value) {
                if ($type !== null) {
                    $html .= "<option value=\"{$type},{$value}\">{$value}</option>";
                } else {
                    $html .= "<option value=\"{$value}\">{$value}</option>";
                }
            }

            $html .= <<<END
                </select>
                <button class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">Filtrer</button>
                </form>
                END;
        }

        if (count($filtreActif) > 0) {
            $html .= <<<END
            <div>
                <label class="block text-sm font-medium text-gray-900 dark:text-gray-300" for="genrePrefere">Filtres actifs</label>
                <ul class="flex flex-col flex-wrap h-20 gap-3 mt-2" >
            END;

            $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $url = urldecode($url);

            foreach ($filtreActif as $value) {
                $lien = str_replace($value . ',', "", $url);
                $lien = str_replace(',' . $value, "", $lien);
                $lien = str_replace('&type=' . $value, "", $lien);
                $lien = str_replace('&genre=' . $value, "", $lien);
                $html .= <<<END
                <li class="block bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded" xmlns="http://www.w3.org/1999/html">
                    <a class="flex flex-row justify-between" href="$lien">
                        <label class="mr-2">$value</label>
                        <i class="fa-solid fa-xmark text-black align-middle dark:text-white"></i>
                    </a>
                </li>
                END;
            }

            $html .= <<<END
                </ul>
            </div>
            END;
        }

        $query = $db->prepare($searchQuery . $triQuerry);
        $query->execute();

        $catalogue = "";

        while ($result = $query->fetch()) {
            $seriesCard = new SeriesCardRenderer($result["img"], $result["titre"], $result["id"] , (int)$result["annee"]);

            $catalogue .= $seriesCard->render();
        }

        if ($catalogue === "") {
            $catalogue = "<label class=\"block text-sm font-medium text-gray-900 dark:text-gray-300\">Aucune série ne correspond à la recherche</label>";
        }

        return <<<END
                <div class="flex flex-wrap flex-row gap-10 mb-8">
                    {$html}
                </div>
                <div class="flex flex-wrap flex-row gap-10" id="articles">
                    {$catalogue}
                </div>
                END;

    }
}