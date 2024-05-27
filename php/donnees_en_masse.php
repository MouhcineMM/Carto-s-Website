<?php
$db = new PDO('pgsql:host=10.11.159.10;dbname=2023_SAE5_01_lambert_maimouni1', 'admindbetu', 'admindbetu');

function getNextAvailableId($db, $table, $idColumn) {
    $sql = "SELECT $idColumn FROM $table ORDER BY CAST($idColumn AS INTEGER) ASC";
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['lefichier']) && $_FILES['lefichier']['error'] === UPLOAD_ERR_OK && isset($_POST['tableName'])) {
    $csvFile = $_FILES['lefichier']['tmp_name'];
    $tableName = $_POST['tableName'];

    $sql = "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = ? AND column_name != 'id' AND column_name != 'coordonnee'";
    $stmt = $db->prepare($sql);
    $stmt->execute([$tableName]);
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!$columns) {
        echo "Erreur: La table spécifiée n'existe pas ou elle ne contient pas de colonnes autres que l'ID.";
        exit; 
    }

    $handle = fopen($csvFile, 'r');

    fgetcsv($handle);

    $insertSql = "INSERT INTO $tableName (id, coordonnee," . implode(',', $columns) . ") VALUES (?, ?, " . implode(',', array_fill(0, count($columns), '?')) . ")";
    $insertStmt = $db->prepare($insertSql);

    while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
        $nextId = getNextAvailableId($db, $tableName, 'id');

        $coordinates = explode(',', $data[array_search('coord_brut', $columns)]);
        $latitude = floatval($coordinates[1]);
        $longitude = floatval($coordinates[0]);
        $coordinatesJson = json_encode([
            "type" => "Feature",
            "geometry" => [
                "coordinates" => [[$longitude, $latitude]],
                "type" => "Point"
            ]
        ]);

        array_unshift($data, $nextId, $coordinatesJson);
        $insertStmt->execute($data);
    }

    fclose($handle);

    echo "Les données ont été importées avec succès.";
    echo "<script>setTimeout(function(){ location.reload(); }, 2000);</script>";

} else {
    echo "Une erreur s'est produite lors du téléchargement du fichier ou le nom de la table n'a pas été spécifié.";
}
?>
