<?php
$host = '10.11.159.10';
$dbname = '2023_SAE5_01_lambert_maimouni1';
$user = 'admindbetu';
$password = 'admindbetu';
$dsn = "pgsql:host=$host;dbname=$dbname;user=$user;password=$password";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    try {
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $id = $_POST['id'];

        $stmt = $pdo->prepare("DELETE FROM reseau_hydrolique WHERE id = ?");
        $stmt->execute([$id]);

        http_response_code(200);
        echo "Suppression réussie pour l'ID : $id";
    } catch (PDOException $e) {
        http_response_code(500);
        echo 'Erreur de connexion : ' . $e->getMessage();
    }
} else {
    http_response_code(400);
    echo 'Requête invalide';
}
?>
