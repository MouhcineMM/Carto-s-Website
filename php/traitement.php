<?php
if (isset($_GET['nom_section'])) {
    $nomSection = $_GET['nom_section'];
    $host = '10.11.159.10';
    $dbname = '2023_SAE5_01_lambert_maimouni1';
    $user = 'admindbetu';
    $password = 'admindbetu';

	//modification des noms de colonnes
    $columnMapping = array(
        'id' => 'Id', 
        'code_insee' =>'Adresse',
        'clef_describ'=>'Nom',
        'coord_brut'=>'Coordonnée',
        'nom'=>'Nom',
        'coordonnee'=>'Coordonnée',
        'geom'=>'Représentation',
        'air'=>'Superficie en x²',
        'nom_latin'=>'Nom latin',
        'nom_fr'=>'Nom français',
        'page_wiki'=>'Lien Wikipédia',
        'descriptif'=>'Descriptif',
        'ville'=>'Ville',
        'insee'=>'Adresse',
        'activite'=>'Activité',
        'commune'=>'Ville',
        'mnie'=>'Milieu naturel',
        'type'=>'Type'
    );

    try {
        $dsn = "pgsql:host=$host;dbname=$dbname;user=$user;password=$password";
        $dbh = new PDO($dsn);

        // Récupérer les colonnes de la table
        $columns = $dbh->query("SELECT column_name FROM information_schema.columns WHERE table_name = '$nomSection'")->fetchAll(PDO::FETCH_COLUMN);

        // Supprimer les colonnes indésirables
        $columns = array_diff($columns, ['coordonnee', 'list_coord', 'coord', 'id_amphibien_describ','image','geom']);

        // Liste des colonnes pour la requête SQL
        $columnList = implode(', ', $columns);

        // Préparer et exécuter la requête SQL
        $requete = $dbh->prepare("SELECT $columnList FROM $nomSection ORDER BY CAST(id AS INTEGER) ASC");
        $requete->execute();
        $resultats = $requete->fetchAll(PDO::FETCH_ASSOC);

        if ($resultats) {
			echo '<table class="table table-striped table-dark>';
            echo '<tr>';
			echo '<th scope="col"></th>';
            foreach ($resultats[0] as $colonne => $valeur) {

                $titreColonne = isset($columnMapping[$colonne]) ? $columnMapping[$colonne] : $colonne;
                echo '<th scope="col">' . $titreColonne . '</th>';
            }
            echo '</tr>';

            // Tableau pour stocker les valeurs des cases à cocher
            $checkboxValues = array();

            // Récupérer les données de correspondance depuis la table description_amphibien
            $correspondanceQuery = $dbh->query("SELECT id, nom_fr FROM description_amphibien");
            $correspondanceData = $correspondanceQuery->fetchAll(PDO::FETCH_ASSOC);
            $correspondanceAmphibiens = array_column($correspondanceData, 'nom_fr', 'id');

            foreach ($resultats as $resultat) {
                $requete_extent = $dbh->prepare("SELECT ST_XMin(geom) as xmin, ST_YMin(geom) as ymin, ST_XMax(geom) as xmax, ST_YMax(geom) as ymax FROM $nomSection WHERE id = ?");
                $requete_extent->execute([$resultat['id']]);
                $extent = $requete_extent->fetch(PDO::FETCH_ASSOC);

                echo '<tr>';


                $checkboxValues[] = $resultat['id'];

                foreach ($resultat as $colonne => $valeur) {
                    if ($colonne == 'clef_describ') {
                        $correspondanceId = $valeur;
                        $correspondanceNom = isset($correspondanceAmphibiens[$correspondanceId]) ? $correspondanceAmphibiens[$correspondanceId] : 'Inconnu';
                        echo '<td>' . $correspondanceNom . '</td>';
                    } elseif ($colonne == 'forme') {
                        echo '<td><svg viewBox="'. $extent['xmin'] . ' ' . $extent['ymin'] . ' ' . ($extent['xmax'] - $extent['xmin']) . ' ' . ($extent['ymax'] - $extent['ymin']) .'" width="500" height="500"><path d="' . $valeur . '"fill="none" stroke="black"/>
						</svg></td>';
                    } else {
                        echo '<td>' . $valeur . '</td>';
                    }
                }

                echo '</tr>';
            }

            echo '</table>';

        } else {
            echo 'Aucun résultat trouvé pour la table : ' . $nomSection;
        }
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données: " . $e->getMessage());
    }
} else {
    echo "Veuillez choisir un nom de table pour l'afficher.";
}
?>
