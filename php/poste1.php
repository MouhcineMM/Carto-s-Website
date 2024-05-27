<?php
$geojson = $_POST['geojson'];
$codeINSEE = $_POST['codeINSEE'];
$nomPolygone = $_POST['nomPolygone'];

try {
    $db = new PDO('pgsql:host=10.11.159.10;dbname=2023_SAE5_01_lambert_maimouni1', 'admindbetu', 'admindbetu');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $uniqueId = generateUniqueId($db);

    $sql = "INSERT INTO reseau_hydrolique (coordonnee, type, nom, id) VALUES (:geojson, :codeINSEE, :nomPolygone, :id)";
    $stmt = $db->prepare($sql);

    $stmt->bindParam(':geojson', $geojson, PDO::PARAM_STR);
    $stmt->bindParam(':codeINSEE', $codeINSEE, PDO::PARAM_STR);
    $stmt->bindParam(':nomPolygone', $nomPolygone, PDO::PARAM_STR);
    $stmt->bindParam(':id', $uniqueId, PDO::PARAM_STR);

    $stmt->execute();

    echo "Polygone inséré avec succès dans la base de données!";
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

function generateUniqueId($db) {
    $stmt = $db->prepare("SELECT COUNT(*) AS count FROM reseau_hydrolique WHERE id = :id");
    $stmt->bindParam(':id', $uniqueId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result['count'] > 0) {
        return generateUniqueId($db);
    }
    return $uniqueId;
}
?>
