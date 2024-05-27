<?php
function getNextAvailableId($db, $table) {
    $sql = "SELECT id FROM $table WHERE id ~ '^[0-9]+$' ORDER BY CAST(id AS INTEGER) ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $nextId = 1;
    foreach ($ids as $id) {
        if ($id != $nextId) {
            break;
        }
        $nextId++;
    }

    return $nextId;
}

$geojson = $_POST['geojson'];
$codeINSEE = $_POST['codeINSEE']; 
$type = $_POST['type']; 

try {
    $db = new PDO('pgsql:host=10.11.159.10;dbname=2023_SAE5_01_lambert_maimouni1', 'admindbetu', 'admindbetu');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $uniqueId = getNextAvailableId($db, 'amphibien');

    $sql = "INSERT INTO amphibien (coordonnee, clef_describ, code_insee, id) VALUES (:geojson, :type, :codeINSEE, :uniqueId)";
    $sth = $db->prepare($sql);
    $sth->bindParam(':geojson', $geojson, PDO::PARAM_STR);
    $sth->bindParam(':codeINSEE', $codeINSEE, PDO::PARAM_STR); // Lier le code INSEE avec le paramètre
    $sth->bindParam(':type', $type, PDO::PARAM_STR); // Lier le type avec le paramètre
    $sth->bindParam(':uniqueId', $uniqueId, PDO::PARAM_INT); // Lier l'identifiant unique avec le paramètre (comme entier)
    $sth->execute();

    echo "Données insérées avec succès!";
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>