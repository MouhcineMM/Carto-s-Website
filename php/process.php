<?php
$db = new PDO('pgsql:host=10.11.159.10;dbname=2023_SAE5_01_lambert_maimouni1', 'admindbetu', 'admindbetu');

function getNextAvailableId($db, $table) {
    $sql = "SELECT id FROM $table ORDER BY CAST(id AS INTEGER) ASC";;
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Chercher le premier ID manquant
    $nextId = 1;
    foreach ($ids as $id) {
        if ($id != $nextId) {
            break;
        }
        $nextId++;
    }

    return $nextId;
}

if(isset($_POST['action'])) {
    $action = $_POST['action'];

    // Vérifier si la requête est une requête AJAX
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        // La requête est une requête AJAX

        if(isset($_POST["checkboxValues"]) && is_array($_POST["checkboxValues"])) {
            $checkboxValues = $_POST["checkboxValues"];
        } 
        else {
            $checkboxValues = array();
        }
        $table = $_POST['tableName'];

        if($action == 'supprimer') {
			// Traitement pour l'action "supprimer"
			if(empty($checkboxValues)) {
				echo '<script>
					document.getElementById("ajout_modif_sup").style.display = "none";
					</script>';
				echo '<script>alert("Veuillez choisir une donnée à supprimer.");</script>';
			} else {
				foreach ($checkboxValues as $val) {
					$sql = "DELETE FROM $table WHERE id = :id";
					$stmt = $db->prepare($sql);
					$stmt->bindParam(':id', $val, PDO::PARAM_INT);
					$stmt->execute();

					echo "Ligne n°$val a été supprimée de la table $table.<br/>";
					echo "<script>setTimeout(function(){ location.reload(); }, 2000);</script>";
				}
			}
		}

        elseif ($action == 'modifier') {
            // Traitement pour l'action "modifier"
            if (count($checkboxValues) === 1) {
                $val = reset($checkboxValues); // Récupérer la valeur unique
                // Modifier la requête SQL pour exclure la colonne ID
                $sql = "SELECT * FROM $table WHERE id = :id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id', $val, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // Afficher le formulaire de modification
                $form = '<form name="formModifier" id="formModifier" method="POST">';
                foreach ($row as $column => $value) {
                    // Exclure l'affichage de la colonne ID
                    if ($column !== 'id'&& $column !== 'geom' && $column !== 'coordonnee' && $column !== 'forme') {
                        $form .= '<label for="' . $column . '">' . ucfirst($column) . ':</label>';
                        $form .= '<input type="text" name="' . $column . '" id="' . $column . '" value="' . $value . '" required><br>';

                    }
                }
                $form .= '<button type="button" value="Modifier" id="submitFormModifier">Enregistrer</button>';
				$form .= '<button type="button" value="Annuler" id="cancelFormModifier">Annuler</button>';
                $form .= '</form>';

                echo $form;

                // JavaScript AJAX code
                echo '<script>
                        document.getElementById(\'submitFormModifier\').addEventListener(\'click\', function(event) {
                            event.preventDefault(); // Empêche le formulaire de se soumettre normalement
                            
                            // Collecte des données du formulaire
                            var formData = {};
                            var formElements = document.getElementById(\'formModifier\').elements;
                            for (var i = 0; i < formElements.length; i++) {
                                var element = formElements[i];
                                if (element.tagName === \'INPUT\') {
                                    formData[element.name] = element.value;
                                }
                            }
                            // Ajouter le nom de la table aux données envoyées via AJAX
                            formData[\'tableName\'] = ' . json_encode($table) . ';
                            formData[\'id\'] = ' . $val . ';
                            
                            // Envoi des données via AJAX
                            $.ajax({
                                url: \'modifier.php\',
                                type: \'POST\',
                                data: formData,
                                success: function (response) {
                                    $("#ajout_modif_sup").html(response);
                                },
                                error: function (error) {
                                    console.log(error);
                                }
                            });
                        });
						document.getElementById(\'cancelFormModifier\').addEventListener(\'click\', function(event) {
                            var form = document.getElementById(\'formModifier\');
                            form.parentNode.removeChild(form);
							$("#ajout_modif_sup").hide();
                        });
                    </script>';
            } elseif (count($checkboxValues) > 1) {
				echo '<script>
				document.getElementById("ajout_modif_sup").style.display = "none";
			  </script>';

                echo '<script>alert("Veuillez sélectionner une seule ligne pour la modification.");</script>';
            } else {
				echo '<script>
				document.getElementById("ajout_modif_sup").style.display = "none";
			  </script>';
				echo '<script>alert("Veuillez sélectionner une ligne pour la modification.");</script>';
            }
        }
        elseif($action == 'ajouter') {
            // Traitement pour l'action "ajouter"
            if (!empty($table)) {
                // Obtenir le prochain ID disponible
                $nextId = getNextAvailableId($db, $table);

                $sql = "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table' AND column_name != 'id' AND column_name != 'coordonnee'AND column_name != 'geom'AND column_name != 'forme'";
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

                // Envoyer les champs du formulaire dans la réponse AJAX
                $form = '<form name="formAjouter" id="formAjouter" method="POST" action="ajouter.php">';
                foreach ($columns as $column) {
                    $form .= '<label for="' . $column . '">' . ucfirst($column) . ':</label>';
                    $form .= '<input type="text" name="' . $column . '" id="' . $column . '" required><br>';
                }
                // Ajouter un champ hidden pour l'ID avec la valeur $nextId
                $form .= '<input type="hidden" name="id" value="' . $nextId . '">';
                $form .= '<button type="button" value="Ajouter" id="submitFormAjouter">Enregistrer</button>';
				$form .= '<button type="button" value="Annuler" id="cancelFormAjouter">Annuler</button>';
                $form .= '</form>';

                echo $form;

                // JavaScript AJAX code
                echo '<script>
                        document.getElementById(\'submitFormAjouter\').addEventListener(\'click\', function(event) {
                            event.preventDefault(); 

                            // Collecte des données du formulaire
                            var formData = {};
                            var formElements = document.getElementById(\'formAjouter\').elements;
                            for (var i = 0; i < formElements.length; i++) {
                                var element = formElements[i];
                                if (element.tagName === \'INPUT\') {
                                    formData[element.name] = element.value;
                                }
                            }
                            // Ajouter le nom de la table aux données envoyées via AJAX
                            formData[\'tableName\'] = ' . json_encode($table) . ';
                            // Envoi des données via AJAX
                            $.ajax({
                                url: \'ajouter.php\',
                                type: \'POST\',
                                data: formData,
                                success: function (response) {
                                    $("#ajout_modif_sup").html(response);
                                },
                                error: function (error) {
                                    console.log(error);
                                }
                            });
                        });
						document.getElementById(\'cancelFormAjouter\').addEventListener(\'click\', function(event) {
                            var form = document.getElementById(\'formAjouter\');
                            form.parentNode.removeChild(form);
							$("#ajout_modif_sup").hide();
                        });
                    </script>';
            } 
            else {
				echo '<script>
				document.getElementById("ajout_modif_sup").style.display = "none";
			  </script>';
				echo '<script>alert("Veuillez sélectionner une table avant d\'ajouter des données.");</script>';
            }
        }
       elseif($action == 'ajouter_en_masse') {
			// Affichage du formulaire pour l'ajout en masse
			$form ='<form id="uploadForm" enctype="multipart/form-data">';
			$form .='<input type="file" name="lefichier" />';
			$form .='<br/>';
			$form .='<button type="button" value="Ajouter le fichier" id="submitFormAjouter_en_masse"/>Intégrer le fichier</button>';
			$form .='</form>';
			echo $form;
			echo '<button type="button" value="Annuler" id="cancelFormAjouterEnMasse">Annuler</button>';
			// JavaScript AJAX code pour l'envoi du fichier en masse
			echo '<script>
				document.getElementById(\'submitFormAjouter_en_masse\').addEventListener(\'click\', function(event) {
					var fileInput = document.querySelector(\'input[type=file]\');
					if (fileInput.files.length === 0) {
						alert("Veuillez choisir un fichier avant d\'intégrer.");
						return; // Arrêter l\'exécution si aucun fichier n\'est choisi
					}
					var formData = new FormData();
					formData.append(\'lefichier\', document.querySelector(\'input[type=file]\').files[0]);
					// Ajouter tableName aux données envoyées via AJAX
					formData.append(\'tableName\', ' . json_encode($table) . ');
					$.ajax({
						url: \'donnees_en_masse.php\',
						type: \'POST\',
						data: formData,
						processData: false,
						contentType: false,
						success: function(response) {
							$("#ajout_modif_sup").html(response);
						},
						error: function(error) {
							console.log(error);
						}
					});
				});

				document.getElementById(\'cancelFormAjouterEnMasse\').addEventListener(\'click\', function(event) {
					var form = document.getElementById(\'uploadForm\');
					form.parentNode.removeChild(form);
					var cancelButton = document.getElementById(\'cancelFormAjouterEnMasse\');
					cancelButton.parentNode.removeChild(cancelButton);
					$("#ajout_modif_sup").hide();
				});
			</script>';


		}
        else {
			echo '<script>alert("Action inconnue!");</script>';
        }
    } 
    else {
		echo '<script>alert("Action inconnue!");</script>';
    }
} 
else {
	echo '<script>alert("Aucune action spécifiée!");</script>';
}
?>
