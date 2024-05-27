<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon projet SAE</title>
    <link rel="stylesheet" type="text/css" href="CSS/Page_Donnee.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        section {
            scroll-margin-top: 10px;
        }
    </style>
    <script>
	$(document).ready(function () {
		var selectedTableName = "";

		var tableTextMapping = {
			"amphibien": "Amphibiens",
			"description_amphibien": "Description",
			"commune": "Communes",
			"reseau_hydrolique": "Reseaux Hydroliques",
			"entreprise": "Entreprises",
			"lotissement": "Lotissements",
			"espace_vert": "Espaces verts",
			"zone_agricole": "Zones Agricoles",
			"occupation_sol": "Occupation du Sol"
		};

		function showError(message) {
			alert("Une erreur s'est produite : " + message);
		}

		$('#sommaire').on('click', 'a', function (e) {
			e.preventDefault();

			var nomSection = $(this).data('nom');
			selectedTableName = nomSection;  
			
			var tableText = tableTextMapping[nomSection] || nomSection;

			$('#tablePresentation').text("Présentation de la table : " + tableText);

			$.ajax({
				url: 'traitement_connecte.php',
				type: 'GET',
				data: { nom_section: nomSection },
				success: function (data) {
					$('#resultat', window.parent.document).html(data);
				},
				error: function () {
					showError("Une erreur s'est produite lors de la requête AJAX.");
				}
			});
		});

		$("form[name='formActions'] button").click(function (e) {
			e.preventDefault(); 

			var action = $(this).attr("id");

			switch (action) {
				case "supprimer":
					sendAjaxRequest(selectedTableName, "supprimer");
					break;
				case "modifier":
					sendAjaxRequest(selectedTableName, "modifier");
					break;
				case "ajouter":
					sendAjaxRequest(selectedTableName, "ajouter");
					break;
				case "ajouter_en_masse":
					sendAjaxRequest(selectedTableName, "ajouter_en_masse");
					break;
				default:
					console.log("Action non reconnue");
			}

			$("#ajout_modif_sup").show();
		});

		function sendAjaxRequest(tableName, action) {
			if (tableName !== "") {
				var checkboxValues = $('input[name="checkbox[]"]:checked').map(function () {
					return this.value;
				}).get();
				$.ajax({
					url: 'process.php',
					type: 'POST',
					data: { action: action, tableName: tableName, checkboxValues: checkboxValues },
					success: function (response, status, jqXHR) {
						if (jqXHR.readyState === 4 && jqXHR.status === 200){
							$("#ajout_modif_sup").html(response);
						}
					},
					error: function (error) {
						showError("Une erreur s'est produite lors de l'envoi de la requête AJAX.");
					}
				});
			} else {
				alert("Veuillez sélectionner une table avant d'effectuer une action.");
			}
		}
	});
    </script>
</head>
<body>
    <form name="formActions" method="post">
        <header>
            <nav>
                <ul>
                    <li><a href="page_accueil_connecte.php">Accueil</a></li>
                    <li><a href="Page_carte_connecte.php">Cartographie</a></li>
                    <li><a href="#">Données brutes </a></li>
					<a href="Page_accueil_1.php">
					<img src="Image_des_grenouilles/image_deconnexion.jfif" width="30" height="30">
					</a>
                </ul>
				
            </nav>
			
        </header>

        <div id="menu">
            <ul id="sommaire">
                <li><b>Selectionner ici :</b></li>
                <li><a href="#" data-nom="amphibien">Amphibiens</a></li>
                <li><a href="#" data-nom="description_amphibien">Description</a></li>
                <li><a href="#" data-nom="commune">Communes</a></li>
                <li><a href="#" data-nom="reseau_hydrolique">Reseaux Hydroliques</a></li>
                <li><a href="#" data-nom="entreprise">Entreprises</a></li>
                <li><a href="#" data-nom="lotissement">Lotissements</a></li>
                <li><a href="#" data-nom="espace_vert">Espaces verts</a></li>
                <li><a href="#" data-nom="zone_agricole">Zones Agricoles</a></li>
                <li><a href="#" data-nom="occupation_sol">Occupation du Sol</a></li>
            </ul>
        </div>

		
        <div id="cache"></div>
        <div class="main-content">
            <h2 id="tablePresentation">Veuillez choisir une table souhaitée à afficher dans le menu à gauche</h2>
            <button type="button" name="action" id="supprimer" class="btn btn-danger">Supprimer</button>
            <button type="button" name="action" id="modifier" class="btn btn-primary">Modifier</button>
			<button type="button" name="action" id="ajouter" class="btn btn-success">Ajouter</button>
			<button type="button" name="action" id="ajouter_en_masse" class="btn btn-info">Ajouter en masse</button>

			<br>
			<br>
			<div>
			<div id="ajout_modif_sup"></div>
            <div id="resultat"></div>
			</div>
        </div>
		
    </form>
</body>
</html>
