<?php

try {
    $db = new PDO('pgsql:host=10.11.159.10;dbname=2023_SAE5_01_lambert_maimouni1', 'admindbetu', 'admindbetu');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['tableName'])&& $_POST['tableName'] == "amphibien") {
            $table = $_POST['tableName'];

            $values = array();
            $columns = array();
            $placeholders = array();

            foreach ($_POST as $key => $value) {
                if ($key !== 'tableName') {
                    $columns[] = $key;
                    $placeholders[] = ":$key";
                    if ($key === 'id') {
                        $values[':' . $key] = intval($value); 
                    } else {
                        $values[':' . $key] = $value;
                    }
                }
            }

			//if (!isset($_POST['coord_brut']) || !preg_match('/^-?\d+(\.\d+)?,-?\d+(\.\d+)?$/', $_POST['coord_brut'])) {
            //    echo "<script>alert('Le format des coordonnées doit être latitude,longitude');</script>";
            //    exit; // Arrêter l'exécution si le format n'est pas valide
            //}

            $coordinates = explode(',', $_POST['coord_brut']);
            $latitude = floatval($coordinates[1]);
            $longitude = floatval($coordinates[0]);

            $coordinatesJson = array(
                "type" => "Feature",
                "geometry" => array(
                    "coordinates" => array(array($longitude, $latitude)),
                    "type" => "MultiPoint"
                )
            );
            $values[':coordonnee'] = json_encode($coordinatesJson);
            $columns[] = 'coordonnee';
            $placeholders[] = ':coordonnee';

            $values[':coord_brut'] = $_POST['coord_brut'];

            $placeholdersList = implode(", ", $placeholders);
            $columnsList = implode(", ", $columns);

            $sql = "INSERT INTO $table ($columnsList) VALUES ($placeholdersList)";
            $stmt = $db->prepare($sql);

            try {
                $stmt->execute($values);
                echo "Données ajoutées avec succès.";
                echo "<script>setTimeout(function(){ location.reload(); }, 2000);</script>";

            } catch (PDOException $e) {
                echo "Erreur lors de l'ajout des données: " . $e->getMessage();
            }
        }
		elseif (isset($_POST['tableName']) && (($_POST['tableName'] == "reseau_hydrolique") || ($_POST['tableName'] == "commune") || ($_POST['tableName'] == "espace_vert") || ($_POST['tableName'] == "occupation_sol") || ($_POST['tableName'] == "zone_agricole")) && isset($_POST['id'])) {
            $table = $_POST['tableName'];

            $values = array();
            $columns = array();
            $placeholders = array();

            foreach ($_POST as $key => $value) {
                if ($key !== 'tableName') {
                    $columns[] = $key;
                    $placeholders[] = ":$key";
                    if ($key === 'id') {
                        $values[':' . $key] = intval($value); 
                    } else {
                        $values[':' . $key] = $value;
                    }
                }
            }

			//if (!isset($_POST['coord_brut']) || !preg_match('/^-?\d+(\.\d+)?,-?\d+(\.\d+)?$/', $_POST['coord_brut'])) {
            //    echo "<script>alert('Le format des coordonnées doit être latitude,longitude');</script>";
            //    exit; // Arrêter l'exécution si le format n'est pas valide
            //}

			$coordinates = explode(',', $_POST['list_coord']);

			$coordinates = array_map(function($coord) {
				$coord = str_replace(array('[', ']', ' '), '', $coord);
				return floatval($coord);
			}, $coordinates);

			$coordinatesJson = array(
				"type" => "Feature",
				"geometry" => array(
					"coordinates" => array([$coordinates]), 
					"type" => "Polygon"
				)
			);
			$values[':coordonnee'] = json_encode($coordinatesJson);


            $columns[] = 'coordonnee';
            $placeholders[] = ':coordonnee';

            $values[':list_coord'] = $_POST['list_coord'];

            $placeholdersList = implode(", ", $placeholders);
            $columnsList = implode(", ", $columns);

            $sql = "INSERT INTO $table ($columnsList) VALUES ($placeholdersList)";
            $stmt = $db->prepare($sql);

            try {
                $stmt->execute($values);
                echo "Données ajoutées avec succès.";
                echo "<script>setTimeout(function(){ location.reload(); }, 2000);</script>";

            } catch (PDOException $e) {
                echo "Erreur lors de l'ajout des données: " . $e->getMessage();
            }
        }
		else {
            echo "Nom de la table non spécifié.";
        }
    }
} catch (PDOException $ex) {
    echo "Erreur de connexion à la base de données: " . $ex->getMessage();
}

?>
