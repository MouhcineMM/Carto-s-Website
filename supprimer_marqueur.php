<?php

// Supprimer le commentaire ci-dessous pour autoriser l'accès même sans utiliser la méthode POST
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
// Récupérer l'ID du marqueur depuis le corps de la requête
$id_marqueur = isset($_REQUEST['id']) ? $_REQUEST['id'] : null; 

if ($id_marqueur !== null) {
    $host = '10.11.159.10';
    $dbname = '2023_SAE5_01_lambert_maimouni1';
    $user = 'admindbetu';
    $password = 'admindbetu';
    $dsn = "pgsql:host=$host;dbname=$dbname;user=$user;password=$password";

    try {
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "DELETE FROM amphibien WHERE id = :id";
        $statement = $pdo->prepare($query);

        $statement->bindParam(':id', $id_marqueur, PDO::PARAM_INT);

        $statement->execute();

        echo "Le marqueur avec l'ID $id_marqueur a été supprimé de la base de données";
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Erreur lors de la suppression du marqueur: " . $e->getMessage();
    }
} else {
    http_response_code(400);
    echo "L'ID du marqueur n'a pas été fourni";
}
?>
