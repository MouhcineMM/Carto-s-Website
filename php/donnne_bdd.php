<?php
$host = "10.11.159.10";
$dbname = "2023_SAE5_01_lambert_maimouni1";
$user = "admindbetu";
$password = "admindbetu";

try {
    $dbh = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = 'SELECT t1.id, t1.coordonnee, t1.code_insee, t1.id AS amphibien_id, t2.nom_latin, t2.nom_fr, t2.image, t2.descriptif, t2.page_wiki 
FROM amphibien t1 
JOIN description_amphibien t2 ON t1.clef_describ = t2.id;
';
    $stmt = $dbh->query($query);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $decodedCoords = json_decode($row['coordonnee'], true);

        if ($decodedCoords === null) {
            $latitude = 0;
            $longitude = 0;

            error_log("Erreur de décodage JSON dans la colonne coordonnee : " . $row['coordonnee']);
        } else {
            if (!empty($decodedCoords)) {
                $coordonnee = $decodedCoords['geometry']['coordinates'][0];
                $latitude = $coordonnee[1];
                $longitude = $coordonnee[0];
            } else {
                $latitude = 0;
                $longitude = 0;
            }
        }

        $image_url = 'http://10.11.159.10/sd3a/llambert/sae_6sig/propre/Image_des_grenouilles/' . urlencode($row['image']);

        echo $row['id'] . '|' . $latitude . '|' . $longitude . '|' . $row['code_insee'] . '|' . $row['amphibien_id'] . '|' . $row['nom_latin'] . '|' . $row['nom_fr'] . '|' . $image_url . '|' . $row['descriptif'] . '|' . $row['page_wiki'] . "\n";
        echo $row['id'] . '|' . $latitude . '|' . $longitude . '|' . $row['code_insee'] . '|' . $row['amphibien_id'] . '|' . $row['nom_latin'] . '|' . $row['nom_fr'] . '|' . $image_url . '|' . $row['descriptif'] . '|' . $row['page_wiki'] . "\n";
    }

} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>
