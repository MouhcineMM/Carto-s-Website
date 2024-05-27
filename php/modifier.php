<?php

try {
    $db = new PDO('pgsql:host=10.11.159.10;dbname=2023_SAE5_01_lambert_maimouni1', 'admindbetu', 'admindbetu');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['tableName'])&& $_POST['tableName'] == "amphibien"|| ($_POST['tableName'] == "entreprise") && isset($_POST['id'])) {
            //if (!isset($_POST['coord_brut']) || !preg_match('/^-?\d+(\.\d+)?,-?\d+(\.\d+)?$/', $_POST['coord_brut'])) {
            //    echo "<script>alert('Le format des coordonnées doit être latitude,longitude');</script>";
            //    echo '<script>document.getElementById("ajout_modif_sup").style.display = "none";</script>'; // Correction ici
            //    exit; // Arrêter l'exécution si le format n'est pas valide
            //}
            
            $table = $_POST['tableName'];
            $id = $_POST['id'];

            $coordinates = explode(',', $_POST['coord_brut']);
            $latitude = floatval($coordinates[1]);
            $longitude = floatval($coordinates[0]);

            $coordinatesJson = array(
                "type" => "Feature",
                "geometry" => array(
                    "coordinates" => array(array($longitude, $latitude)), 
                    "type" => "Point"
                )
            );

            $updates = array();
            foreach ($_POST as $key => $value) {
                if ($key !== 'tableName' && $key !== 'id' && $key !== 'coord_brut') { // Ignorer les clés 'tableName', 'id' et 'coord_brut'
                    $updates[] = "$key = :$key";
                }
            }

            $updateList = implode(", ", $updates);

            $sql = "UPDATE $table SET $updateList, coordonnee = :coordonnee, coord_brut = :coord_brut WHERE id = :id";
            $stmt = $db->prepare($sql);

            try {
                foreach ($_POST as $key => $value) {
                    if ($key !== 'tableName' && $key !== 'id' && $key !== 'coord_brut') { // Ignorer les clés 'tableName', 'id' et 'coord_brut'
                        $stmt->bindValue(":$key", $value);
                    }
                }
                $stmt->bindValue(":coordonnee", json_encode($coordinatesJson), PDO::PARAM_STR);
                $stmt->bindValue(":coord_brut", $_POST['coord_brut'], PDO::PARAM_STR); // Ajout de la liaison pour coord_brut
                $stmt->bindValue(":id", $id, PDO::PARAM_INT);
                $stmt->execute();
                echo "Données mises à jour avec succès.";
				
                echo "<script>setTimeout(function(){ location.reload(); }, 2000);</script>";

            } catch (PDOException $e) {
                echo "Erreur lors de la mise à jour des données: " . $e->getMessage();
            }
		}
		elseif (isset($_POST['tableName']) && (($_POST['tableName'] == "reseau_hydrolique") || ($_POST['tableName'] == "commune") || ($_POST['tableName'] == "espace_vert") || ($_POST['tableName'] == "occupation_sol") || ($_POST['tableName'] == "zone_agricole")) && isset($_POST['id'])) {
            
            
            $table = $_POST['tableName'];
            $id = $_POST['id'];

            $coordinates = explode(',', $_POST['list_coord']);
            

            $coordinatesJson = array(
                "type" => "Feature",
                "geometry" => array(
                    "coordinates" => array(array($coordinates)), 
                    "type" => "polygon"
                )
            );

            $updates = array();
            foreach ($_POST as $key => $value) {
                if ($key !== 'tableName' && $key !== 'id' && $key !== 'list_coord') { 
                    $updates[] = "$key = :$key";
                }
            }

            $updateList = implode(", ", $updates);

            $sql = "UPDATE $table SET $updateList, coordonnee = :coordonnee, list_coord = :list_coord WHERE id = :id";
            $stmt = $db->prepare($sql);

            try {
                foreach ($_POST as $key => $value) {
                    if ($key !== 'tableName' && $key !== 'id' && $key !== 'list_coord') { 
                        $stmt->bindValue(":$key", $value);
                    }
                }
                $stmt->bindValue(":coordonnee", json_encode($coordinatesJson), PDO::PARAM_STR);
                $stmt->bindValue(":list_coord", $_POST['list_coord'], PDO::PARAM_STR); 
                $stmt->bindValue(":id", $id, PDO::PARAM_INT);
                $stmt->execute();
                echo "Données mises à jour avec succès.";
				
                echo "<script>setTimeout(function(){ location.reload(); }, 2000);</script>";

            } catch (PDOException $e) {
                echo "Erreur lors de la mise à jour des données: " . $e->getMessage();
            }
		}
         else {
            echo "Nom de la table ou ID non spécifié.";
        }
    }
} catch (PDOException $ex) {
    echo "Erreur de connexion à la base de données: " . $ex->getMessage();
}

?>
