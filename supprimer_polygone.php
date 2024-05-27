<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["id"])) {
        $id = $_POST["id"];
        
        $host = '10.11.159.10';
        $dbname = '2023_SAE5_01_lambert_maimouni1';
        $user = 'admindbetu';
        $password = 'admindbetu';

        try {
            $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("DELETE FROM reseau_hydrolique99 WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            echo json_encode(["success" => true]);
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "L'ID du polygone n'a pas été fourni."]);
    }
} else {
    echo json_encode(["success" => false, "error" => "La méthode de requête n'est pas prise en charge."]);
}
?>
