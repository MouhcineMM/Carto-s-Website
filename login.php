<?php
$bdd = new PDO('pgsql:host=10.11.159.10;dbname=2023_SAE5_01_lambert_maimouni1', 'admindbetu', 'admindbetu');

if(isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = $bdd->prepare('SELECT * FROM login WHERE username = ?');
    $query->execute([$username]);
    $user = $query->fetch();

    if ($user) {
        if ($password === $user['password']) {
            echo 'success';
        } else {
            echo 'wrong_password';
        }
    } else {
        echo 'wrong_username';
    }
} else {
    echo 'Veuillez remplir tous les champs du formulaire.';
}
?>
