 <!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	
	<link rel="stylesheet" href="./leaflet/leaflet.css" />
    <link rel="stylesheet" href="./leaflet-draw/src/leaflet.draw.css"/>

    <link rel="stylesheet" type="text/css" href="CSS/style.css">

</head>

<body>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

	
	<script src="./leaflet/leaflet.js"></script>
	
    <script src="./leaflet-draw/src/Leaflet.draw.js"></script>
    <script src="./leaflet-draw/src/Leaflet.Draw.Event.js"></script>
    <script src="./leaflet-draw/src/Toolbar.js"></script>
    <script src="./leaflet-draw/src/Tooltip.js"></script>
	<script src="./leaflet-draw/src/Control.Draw.js"></script>

    <script src="./leaflet-draw/src/ext/GeometryUtil.js"></script>
    <script src="./leaflet-draw/src/ext/LatLngUtil.js"></script>
    <script src="./leaflet-draw/src/ext/LineUtil.Intersect.js"></script>
    <script src="./leaflet-draw/src/ext/Polygon.Intersect.js"></script>
    <script src="./leaflet-draw/src/ext/Polyline.Intersect.js"></script>
    <script src="./leaflet-draw/src/ext/TouchEvents.js"></script>

    <script src="./leaflet-draw/src/draw/DrawToolbar.js"></script>
    <script src="./leaflet-draw/src/draw/handler/Draw.Feature.js"></script>
    <script src="./leaflet-draw/src/draw/handler/Draw.SimpleShape.js"></script>
    <script src="./leaflet-draw/src/draw/handler/Draw.Polyline.js"></script>
    <script src="./leaflet-draw/src/draw/handler/Draw.Marker.js"></script>
    <script src="./leaflet-draw/src/draw/handler/Draw.Circle.js"></script>
    <script src="./leaflet-draw/src/draw/handler/Draw.CircleMarker.js"></script>
    <script src="./leaflet-draw/src/draw/handler/Draw.Polygon.js"></script>
    <script src="./leaflet-draw/src/draw/handler/Draw.Rectangle.js"></script>

    <script src="./leaflet-draw/src/edit/EditToolbar.js"></script>
    <script src="./leaflet-draw/src/edit/handler/EditToolbar.Edit.js"></script>
    <script src="./leaflet-draw/src/edit/handler/EditToolbar.Delete.js"></script>

    <script src="./leaflet-draw/src/edit/handler/Edit.Poly.js"></script>
    <script src="./leaflet-draw/src/edit/handler/Edit.SimpleShape.js"></script>
    <script src="./leaflet-draw/src/edit/handler/Edit.Rectangle.js"></script>
    <script src="./leaflet-draw/src/edit/handler/Edit.Marker.js"></script>
    <script src="./leaflet-draw/src/edit/handler/Edit.CircleMarker.js"></script>
    <script src="./leaflet-draw/src/edit/handler/Edit.Circle.js"></script>

    <?php
    $host = '10.11.159.10';
    $dbname = '2023_SAE5_01_lambert_maimouni1';
    $user = 'admindbetu';
    $password = 'admindbetu';
    $dsn = "pgsql:host=$host;dbname=$dbname;user=$user;password=$password";
    try {
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Erreur de connexion : ' . $e->getMessage());
    }
    ?>

    <script type="text/javascript">
	
	    var geoData_occupation = [<?php
        $query_commune = $pdo->query("SELECT coordonnee FROM occupation_sol");
        while ($row = $query_commune->fetch(PDO::FETCH_ASSOC)) {
            $geometryArray = json_decode($row['coordonnee'], true);
            echo json_encode($geometryArray) . ',';
        }
        ?>
    ];
	
    var geoData_lotissement = [<?php
        $query_commune = $pdo->query("SELECT coordonnee FROM lotissement");
        while ($row = $query_commune->fetch(PDO::FETCH_ASSOC)) {
            $geometryArray = json_decode($row['coordonnee'], true);
            echo json_encode($geometryArray) . ',';
        }
        ?>
    ];
	
	
	
	

    var geoData_commune = [<?php
        $query_commune = $pdo->query("SELECT coordonnee FROM commune");
        while ($row = $query_commune->fetch(PDO::FETCH_ASSOC)) {
            $geometryArray = json_decode($row['coordonnee'], true);
            echo json_encode($geometryArray) . ',';
        }
        ?>
    ];

    var geoData_zone_agricole = [<?php
        $query_zone_agricole = $pdo->query("SELECT coordonnee FROM zone_agricole");
        while ($row = $query_zone_agricole->fetch(PDO::FETCH_ASSOC)) {
            $geometryArray = json_decode($row['coordonnee'], true);
            echo json_encode($geometryArray) . ',';
        }
        ?>
    ];


    var geoData_reseau_hydrolique = [<?php
        $query_reseau_hydrolique = $pdo->query("SELECT coordonnee FROM reseau_hydrolique");
        while ($row = $query_reseau_hydrolique->fetch(PDO::FETCH_ASSOC)) {
            $geometryArray = json_decode($row['coordonnee'], true);
            echo json_encode($geometryArray) . ',';
        }
        ?>
    ];

    <?php $pdo = null; ?>
</script>
	
	



<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function () {
    let maCarte = L.map('carte_basique');
    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '© IUT SD Perpignan - antenne de Carcassonne - OpenStreetMap'
    }).addTo(maCarte);
    maCarte.setView([48.0833, -1.6833], 10);
    
    let drawnItems = L.featureGroup(); 

var options = {
    position: 'topright',
    draw: {
        polyline: false,
        polygon: false,
        circle: true,
        rectangle: true, 
        circlemarker: false,
        marker: false 
    },
    edit: {
        featureGroup: drawnItems,
        edit: false, 
        remove: false 
    }
};

var drawControl = new L.Control.Draw(options);
maCarte.addControl(drawControl);



    let cercleSelection = null; 
    let rectangleSelection = null; 
let occupation = L.layerGroup();
			let lotissement = L.layerGroup();
            let communeLayer = L.layerGroup();
            let zoneAgricoleLayer = L.layerGroup();
            let espace_vert = L.layerGroup();
			let reseau_hydrolique = L.layerGroup();
			
function afficherCoordonnees_occupation() {
                if ($('#afficherCoordonnees_occupation').prop('checked')) {
                    maCarte.addLayer(occupation);
					console.log(occupation);
                } else {
                    maCarte.removeLayer(occupation);
                }
            }

            $('#afficherCoordonnees_occupation').change(afficherCoordonnees_occupation);




			geoData_occupation.forEach(function (feature) {
				L.geoJSON(feature, {
					style: { color: 'yellow', opacity: 0.8 },
					onEachFeature: function (feature, layer) {
						layer.on('click', function (e) {
							afficherNom(feature.properties.nom_mnie, e.latlng);
						});
					}
				}).addTo(occupation);
			});

			geoData_lotissement.forEach(function (feature) {
                L.geoJSON(feature, { style: { color: '#9683EC	' } }).addTo(lotissement);
            });

			function afficherCoordonnees_lotissement() {
                if ($('#afficherCoordonnees_lotissement').prop('checked')) {
                    maCarte.addLayer(lotissement);
					console.log(lotissement);
                } else {
                    maCarte.removeLayer(lotissement);
                }
            }

            $('#afficherCoordonnees_lotissement').change(afficherCoordonnees_lotissement);
			
			
			
			
			
			
			


            geoData_zone_agricole.forEach(function (feature) {
                L.geoJSON(feature, { style: { color: 'green' } }).addTo(zoneAgricoleLayer);
            });

			geoData_commune.forEach(function (feature) {
				L.geoJSON(feature, {
					style: { color: 'darkgray', fillcolor: '#696969', opacity: 0.4 },
					onEachFeature: function (feature, layer) {
						layer.on('click', function (e) {
							afficherNom(feature.properties.nom, e.latlng);
						});
					}
				}).addTo(communeLayer);
			});

			function afficherNom(nom, latlng) {
				const popupContent = "<p>Nom : " + nom + "</p>";
				
				L.popup()
					.setLatLng(latlng)
					.setContent(popupContent)
					.openOn(maCarte);
			}

			function affichertype(nom, latlng) {
				const popupContent = "<p>Nom : " + type + "</p>";
				
				L.popup()
					.setLatLng(latlng)
					.setContent(popupContent)
					.openOn(maCarte);
			}
			

			
			
			
			
			geoData_reseau_hydrolique.forEach(function (feature) {
					L.geoJSON(feature, { style: { color: 'lightblue	' } }).addTo(reseau_hydrolique);
				});

	       function afficherCoordonnees_reseau_hydrolique() {
                if ($('#afficherCoordonnees_reseau_hydrolique').prop('checked')) {
                    maCarte.addLayer(reseau_hydrolique);
					console.log(reseau_hydrolique);
                } else {
                    maCarte.removeLayer(reseau_hydrolique);
                }
            }

            $('#afficherCoordonnees_reseau_hydrolique').change(afficherCoordonnees_reseau_hydrolique);


            function afficherCoordonnees_zone_agricole() {
                if ($('#afficherCoordonnees_zone_agricole').prop('checked')) {
                    maCarte.addLayer(zoneAgricoleLayer);
                } else {
                    maCarte.removeLayer(zoneAgricoleLayer);
                }
            }

            $('#afficherCoordonnees_zone_agricole').change(afficherCoordonnees_zone_agricole);

            function afficherCoordonnees() {
                if ($('#afficherCoordonnees').prop('checked')) {
                    maCarte.addLayer(communeLayer);
                } else {
                    maCarte.removeLayer(communeLayer);
                }
            }

            $('#afficherCoordonnees').change(afficherCoordonnees);
    function afficherPanneau(contenu) {
        document.getElementById('panneau_info').style.display = 'block';
        document.getElementById('contenu_panneau').innerHTML = contenu;
    }

    const marqueursSurCarte = [];
    const occurrencesMarqueurs = {}; 

    function afficherOccurrencesMarqueurs() {
        const tbody = document.querySelector('#table_marqueurs tbody');
        tbody.innerHTML = ''; 

        for (const nomMarqueur in occurrencesMarqueurs) {
            const row = document.createElement('tr');
            const cellNom = document.createElement('td');
            cellNom.textContent = nomMarqueur;
            const cellOccurrence = document.createElement('td');
            cellOccurrence.textContent = occurrencesMarqueurs[nomMarqueur];
            row.appendChild(cellNom);
            row.appendChild(cellOccurrence);
            tbody.appendChild(row);
        }
    }

	const checkbox = document.getElementById('filtre1');
        checkbox.addEventListener('change', function () {
            if (this.checked) {
                chargerMarqueursDepuisServeur();
            } else {
                marqueursSurCarte.forEach(marqueur => {
                    maCarte.removeLayer(marqueur);
                });
                marqueursSurCarte.length = 0;
            }
        });



    maCarte.on(L.Draw.Event.CREATED, function (e) {
        const type = e.layerType;
        const layer = e.layer;

        if (type === 'circle') {
            if (cercleSelection !== null) {
                maCarte.removeLayer(cercleSelection); 
            }
            cercleSelection = layer; 
            changerCouleurMarqueursDansCercle(layer);
            layer.on('pm:remove', function () {
                resetOccurrences();
            });
        } else if (type === 'rectangle') {
            if (rectangleSelection !== null) {
                maCarte.removeLayer(rectangleSelection); 
            }
            rectangleSelection = layer; 
            changerCouleurMarqueursDansRectangle(layer);
            layer.on('pm:remove', function () {
                resetOccurrences();
            });
        }
    });

    function changerCouleurMarqueursDansCercle(cercle) {
        resetOccurrences();

        const rayon = cercle.getRadius();
        const centre = cercle.getLatLng();

        marqueursSurCarte.forEach(marqueur => {
            const distance = centre.distanceTo(marqueur.getLatLng());
            if (distance <= rayon) {
                marqueur.setIcon(L.icon({ iconUrl: 'nouvelle_icone.png' }));
                const nomMarqueur = marqueur.options.nom_fr;
                occurrencesMarqueurs[nomMarqueur] = (occurrencesMarqueurs[nomMarqueur] || 0) + 1;
            }
        });

        afficherOccurrencesMarqueurs();
    }

    function changerCouleurMarqueursDansRectangle(rectangle) {
        resetOccurrences();

        const bounds = rectangle.getBounds();

        marqueursSurCarte.forEach(marqueur => {
            const latLng = marqueur.getLatLng();
            if (bounds.contains(latLng)) {
                marqueur.setIcon(L.icon({ iconUrl: 'nouvelle_icone.png' }));
                const nomMarqueur = marqueur.options.nom_fr;
                occurrencesMarqueurs[nomMarqueur] = (occurrencesMarqueurs[nomMarqueur] || 0) + 1;
            }
        });

        afficherOccurrencesMarqueurs();
    }

    function resetOccurrences() {
        for (const marqueur in occurrencesMarqueurs) {
            occurrencesMarqueurs[marqueur] = 0;
        }
        afficherOccurrencesMarqueurs();
    }
    
    function resetSelection() {
        if (cercleSelection !== null) {
            maCarte.removeLayer(cercleSelection); 
            cercleSelection = null; 
            chargerMarqueursDepuisServeur(); 
        }
        if (rectangleSelection !== null) {
            maCarte.removeLayer(rectangleSelection); 
            rectangleSelection = null; 
            chargerMarqueursDepuisServeur(); 
        }
    }

    document.getElementById('carte_basique').addEventListener('click', resetSelection);
	
	    function chargerMarqueursDepuisServeur() {
        marqueursSurCarte.forEach(marqueur => {
            maCarte.removeLayer(marqueur);
        });

        marqueursSurCarte.length = 0;

        fetch('donnne_bdd.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                return response.text(); 
            })
            .then(data => {
                const lignes = data.trim().split('\n');
                
                lignes.forEach(ligne => {
                    const infos = ligne.split('|');
                    const latitude = parseFloat(infos[1]);
                    const longitude = parseFloat(infos[2]);
                    const nom_fr = infos[6];
                    const image_url = infos[7];
                    const nom_latin = infos[5];
                    const descriptif = infos[8];
                    const page_wiki = infos[9];

                    if (!isNaN(latitude) && !isNaN(longitude)) {
                        const marker = L.marker([latitude, longitude], { nom_fr: nom_fr });

                        marker.on('click', function () {
                            const contenuPanneau = `
                                <div class="bande_grise">
                                    <p> <center> <span id="nom_fr">${nom_fr} </center></span></p>
                                </div>
                                <img src="${image_url}" alt="${nom_fr}">
                                <div>
                                    <p> Nom Latin : <span id="nom_latin">${nom_latin}</span></p>
                                    <p> Description : <span id="descriptif">${descriptif}</span></p> 
                                    <p>Pour plus de renseignements, allez sur : <a href="${page_wiki}" target="_blank" style="color: #6B8E23;">${page_wiki}</a></p>
                                </div>`;
                            afficherPanneau(contenuPanneau);
                        });

                        maCarte.addLayer(marker);

                        marqueursSurCarte.push(marker);
                    } else {
                        console.error('Données de latitude ou longitude invalides pour le marqueur:', infos);
                    }
                });
            })
            .catch(error => console.error('Erreur lors de la récupération des marqueurs:', error));
    }


        maCarte.on('click', function (e) {
            const latlng = e.latlng;

            const clickedMarker = marqueursSurCarte.find(marqueur => marqueur.getLatLng().equals(latlng));

            if (!clickedMarker) {
                document.getElementById('panneau_info').style.display = 'none';
            }
        });
});
</script>



    <style type="text/css">
#table_marqueurs {
    position: absolute;
    top: 79px;
    left: 74%;
    background-color: #F0FFF0;
    border: 1px solid #cccccc;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 10px;
    max-height: 300px;
    overflow-y: auto;
    z-index: 1000;
}

#table_marqueurs table {
    width: 100%;
    border-collapse: collapse;
}

#table_marqueurs th,
#table_marqueurs td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #dddddd;
}

#table_marqueurs th {
    background-color: #f2f2f2;
}

#table_marqueurs tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

#table_marqueurs tbody tr:hover {
    background-color: #f1f1f1;
}

.carre {
    display: inline-block;
    width: 15px;
    height: 15px;
    margin-right: 5px;
    border: 1px solid #ccc;
    vertical-align: middle;
}

#carte_basique {
    position: fixed;
    right: 27%; 
    width: calc(65% - 220px); 
    top: 7%; 
    bottom: 5%; 
}

#panneau_info {
    position: absolute;
    top: 5%;
    left: 58%;
    width: 30%;
    height: 730px;
    border-style: groove;
    display: none;
    overflow: hidden; 
    margin-top: 20px ;
    z-index: 1001; /* Valeur plus élevée pour que le panneau soit au-dessus du menu */
}

footer {
    position: fixed;
    bottom: 0;
    width: 100%;
    height: 2%;
    background-color: #f1f1f1;
    text-align: center;
    padding: 10px;
    z-index: 2; 
}

#contenu_panneau {
    height: 100%;
    background-color: white;
    display: flex;
    flex-direction: column; 
    color: #6B8E23;
}

.bande_grise {
    background-color: #90EE90; 
    padding: 10px; 
    color: white;
    font-size: 23px;
    font-weight: bold;  
}

#contenu_panneau img {
    width: 100%; 
    max-height: 300px; 
    margin-top: 10px; 
}

#menu-filtres {
    position: fixed;
    top: 50px; 
    left: 0;
    bottom: 1%; 
    width: 300px;
    background-color: #333;
    color: white;
    padding: 10px;
    margin-right: 10px; 
}

#menu-filtres h3 {
    margin-top: 0;
}

#menu-filtres label {
    display: block;
    margin-bottom: 10px;
}

#menu-filtres input[type="checkbox"] {
    margin-right: 5px;
}

#menu-filtres .carre {
    display: inline-block;
    width: 15px;
    height: 15px;
    margin-right: 5px;
    border: 1px solid #ccc;
    vertical-align: middle;
}

#menu-filtres a {
    color: white;
    text-decoration: none;
}

#menu-filtres a:hover {
    text-decoration: underline;
}


    </style>
</head>
<body>
<div id="menu-filtres">
    <h3>Filtres</h3>
    <label><input type="checkbox" id="filtre1"> Amphibiens (Interactive) </label><br><br>
<label>
    <input type="checkbox" id="afficherCoordonnees" />
    <div class="carre" style="background-color: gray;"></div> Commune (Interactive)
</label> <br>

<br>
<label>
    <input type="checkbox" id="afficherCoordonnees_zone_agricole" />
    <div class="carre" style="background-color: green;"></div> Zone agricole
</label> <br>
<br>

<label>
    <input type="checkbox" id="afficherCoordonnees_espace_vert" />
    <div class="carre" style="background-color: darkgreen;"></div> Espace vert 
</label> <br>
<br>

<label>
    <input type="checkbox" id="afficherCoordonnees_reseau_hydrolique" />
    <div class="carre" style="background-color: lightblue;"></div> Reseau Hydraulique
</label> <br>
<br>
<label>
    <input type="checkbox" id="afficherCoordonnees_lotissement" />
    <div class="carre" style="background-color: #9683EC;"></div> Lotissement
</label> <br> <br>

<label>
    <input type="checkbox" id="afficherCoordonnees_occupation" />
    <div class="carre" style="background-color: yellow;"></div> Occupation sol (Interactive)
</label> <br>


</div>

<header>
    <nav>
        <ul>
            <li><a href="Page_accueil_1.php">Accueil</a></li>
			<li><a href="Page_carte.php">Cartographie</a></li>
			<li><a href="Page_Donnees.php">Données brutes</a></li>
			<li><a href="page_connexion.php">Connexion</a></li>
        </ul>
    </nav>
</header>
<div id="carte_basique"></div>
<div id="panneau_info">
    <img id="image_marqueur" class="image-marqueur" src="">
    <div id="contenu_panneau">
        <div class="bande_grise">
            <p>Nom FR : <span id="nom_fr"></span></p>
        </div>
        <div>
            <p> Nom Latin : <span id="nom_latin"></span></p><br>
			<p> Description : <span id="descriptif"></span></p><br>


        </div>
    </div>
</div>

<footer>
    <div class="contenu-footer">
        <div class="bloc footer-contact">
            <p></p>
        </div>
    </div>
</footer>
<table id="table_marqueurs">
    <thead>
        <tr>
<th>Nom français</th><th>Occurrence par type</th>

        </tr>
		
    </thead>
    <tbody>
    </tbody>
</table>

</body>
</html>

