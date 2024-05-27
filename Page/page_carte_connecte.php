<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>

<link rel="stylesheet" href="./leaflet/leaflet.css" />
    <link rel="stylesheet" href="./leaflet-draw/src/leaflet.draw.css"/>

    <link rel="stylesheet" type="text/css" href="CSS/style.css">    
<link rel="stylesheet" type="text/css" href="CSS/style89.css">


</head>

<body>
<script src="ajax.js" type="text/javascript"></script>
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

    function envoyerPolygone(geojson, codeINSEE, nomPolygone) {
        var xhr = createXHR();
        xhr.open("POST", "poste1.php", true);

        var data = JSON.stringify(geojson);
        var formData = new FormData();
        formData.append('geojson', data);
        formData.append('codeINSEE', codeINSEE);
        formData.append('nomPolygone', nomPolygone);

        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {
                    alert("Polygone posté en Base");
                    alert("GeoJSON inséré : " + data + " code INSEE : " + codeINSEE + " nom polygone : " + nomPolygone);
                } else {
                    alert("Echec de postage en base");
                }
            }
        };
       
        xhr.send(formData);
    }

    function createXHR() {
        var xhr;
        if (window.XMLHttpRequest) {
            xhr = new XMLHttpRequest();
        } else {
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
        }
        return xhr;
    }

    function formaterCoordonnees(latlng) {
        return [latlng.lng, latlng.lat];
    }

    function envoyerGeoJSON(geojson, codeINSEE, typeValue) {
        var xhr = createXHR();
        xhr.open("POST", "poste.php", true);

        var geoData = JSON.parse(geojson);
       
        var coordinates = geoData.geometry.coordinates;
        var geometryType = geoData.geometry.type;
       
        var newGeoJSON = {
            type: "Feature",
            geometry: {
                coordinates: coordinates,
                type: geometryType
            },
            properties: {
                geo_point_2d: {
                    lon: coordinates[0][0],
                    lat: coordinates[0][1]
                }
            }
        };

        var data = JSON.stringify(newGeoJSON);
        var formData = new FormData();
        formData.append('geojson', data);
        formData.append('codeINSEE', codeINSEE);
        formData.append('type', typeValue);

        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {
                    alert("Donnée postée en Base");
                    alert("GeoJSON inséré : " + data + " code insee " + codeINSEE + " type " + typeValue);
                } else {
                    alert("Echec de postage en base");
                }
            }
        };
       
        xhr.send(formData);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var maCarte = L.map('carte_basique');
        let reseau_hydrolique = L.layerGroup();

        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: '© IUT SD Perpignan - antenne de Carcassonne - OpenStreetMap'
        }).addTo(maCarte);
        maCarte.setView([48.0833, -1.6833], 10);
        var drawnItems = new L.FeatureGroup();
        maCarte.addLayer(drawnItems);

        var options = {
            position: 'topright',
            draw: {
                polyline: false,
                polygon: true,
                circle: false,
                rectangle: false,
                circlemarker: false,
                marker: true
            },
            edit: {
                featureGroup: drawnItems,
                remove: false,
edit : false,
            }
        };


        var drawControl = new L.Control.Draw(options);
        maCarte.addControl(drawControl);
        maCarte.on(L.Draw.Event.CREATED, function (event) {
            var layer = event.layer;

            if (layer instanceof L.Polygon) {
                maCarte.addLayer(layer);

var FormePolygone = document.createElement('form');
FormePolygone.id = 'FormePolygone';
FormePolygone.innerHTML = `
    <h2> Ajouter un Polygone</h2>
    <label for="codeINSEE">Code INSEE :</label><br>
    <input type="text" id="codeINSEE" name="codeINSEE" class="form-input"><br><br>
    <label for="nomPolygone">Nom Polygone :</label><br>
    <input type="text" id="nomPolygone" name="nomPolygone" class="form-input"><br><br>
    <input type="submit" value="Envoyer" id="BtENVOYER" class="submit-button">
`;

               
                var submitButton = FormePolygone.querySelector('#BtENVOYER');
                submitButton.addEventListener('click', function (event) {
                    event.preventDefault();
                    var codeINSEE = FormePolygone.querySelector('#codeINSEE').value;
                    var nomPolygone = FormePolygone.querySelector('#nomPolygone').value;
                    if (codeINSEE.trim() !== '' && nomPolygone.trim() !== '') {
                        var geojson = layer.toGeoJSON();
                        envoyerPolygone(geojson, codeINSEE, nomPolygone);
                    } else {
                        maCarte.removeLayer(layer);
                    }
                });

                layer.bindPopup(FormePolygone).openPopup();
                FormePolygone.style.display = 'block';

                drawControl.setDrawingOptions({ polygon: false });

                drawnItems.addLayer(layer);
            }
        });

        maCarte.on(L.Draw.Event.CREATED, function (event) {
            var layer = event.layer;

            var markerForm = document.getElementById('markerForm');
            var submitButton = document.getElementById('submitButton');

            if (layer instanceof L.Marker) {
                maCarte.addLayer(layer);
                layer.bindPopup(markerForm).openPopup();
                markerForm.style.display = 'block';

                drawControl.setDrawingOptions({ marker: false });

                submitButton.onclick = function (event) {
                    event.preventDefault();
                    var marqueurValue = document.getElementById('marqueur').value;
                    var typeValue = document.getElementById('type').value; 
                    if (marqueurValue.trim() !== '' && typeValue.trim() !== '') {
                        var geojson = {
                            type: 'Feature',
                            geometry: {
                                type: 'MultiPoint',
                                coordinates: [[layer.getLatLng().lng, layer.getLatLng().lat]]
                            },
                            properties: {
                                geo_point_2d: {
                                    lon: layer.getLatLng().lng,
                                    lat: layer.getLatLng().lat
                                }
                            }
                        };
                        var geojsonString = JSON.stringify(geojson);
                        var codeINSEE = document.getElementById('marqueur').value;
                        envoyerGeoJSON(geojsonString, codeINSEE, typeValue); 
                        layer.bindPopup(markerForm).openPopup();
                    } else {
                        maCarte.removeLayer(layer);
                    }
                };

                drawnItems.addLayer(layer);
            }
        });

const couchesDessinees = new L.FeatureGroup().addTo(maCarte);

    var drawnItems = new L.FeatureGroup().addTo(maCarte);
    var options = {
        position: 'topright',
        draw: {
            polyline: false,
            polygon: false,
            circle: false,
            rectangle: false,
            circlemarker: false,
            marker: false
        },
        edit: {
            featureGroup: drawnItems,
            remove: true
        }
    };

    var drawControl = new L.Control.Draw(options);
    maCarte.addControl(drawControl);

    maCarte.on(L.Draw.Event.CREATED, function (event) {
        drawnItems.addLayer(event.layer);
    });

const marqueursSurCarte = [];

function chargerMarqueursDepuisServeur() {
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
                const id = infos[4]; 
                //const id1 = infos[0]; 

                if (!isNaN(latitude) && !isNaN(longitude)) {
                    const marker = L.marker([latitude, longitude]);
                    marker.bindPopup(nom_fr);
                    marker.id = id; 
                    drawnItems.addLayer(marker); 
                    marqueursSurCarte.push(marker);
                } else {
                    console.error('Données de latitude ou longitude invalides pour le marqueur:', infos);
                }
            });
        })
        .catch(error => console.error('Erreur lors de la récupération des marqueurs:', error));
}

chargerMarqueursDepuisServeur();

drawnItems.on('layerremove', function (event) {
    const layer = event.layer;
    const id = layer.id; 
    const nom_fr = layer.getPopup().getContent();

    const data = {
        id: id
    };

    fetch('supprimer_marqueur.php?id=' + id, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur réseau');
        }
        alert("Le marqueur '" + nom_fr + "' (ID: " + id + ") a été supprimé de la carte et de la base de données");
    })
    .catch(error => console.error('Erreur lors de la suppression du marqueur:', error));
});



   var controleDessin = new L.Control.Draw({
        position: 'topright',
        draw: {
            polyline: false,
            polygon: false,
            circle: false,
            rectangle: false,
            circlemarker: false,
            marker: false
        },
        edit: {
            featureGroup: couchesDessinees,drawnItems,
            remove: true,
edit : false,
        }
    }).addTo(maCarte);
   maCarte.on(L.Draw.Event.CREATED, function (event) {
        var layer = event.layer;
        couchesDessinees.addLayer(layer);
    });

    maCarte.on(L.Draw.Event.DELETED, function (event) {
        var layers = event.layers;
        layers.eachLayer(function (layer) {
            var id = layer.options.id;
            var type = layer.options.type;
            supprimerPolygone(id, layer);
            alert("Le réseau hydraulique de type : " + type + " et d'id : " + id + " a été supprimé");
        });
    });
function ajouterPolygoneSurCarte(type, tableauGeometrie, id) {
        L.geoJSON(tableauGeometrie, {
            style: { color: 'black' },
            onEachFeature: function (feature, couche) {
                couche.options.id = id;
                couche.options.type = type; 
                couchesDessinees.addLayer(couche);
            }
        }).addTo(maCarte);
    }


    function supprimerPolygone(id, couche) {
        var requeteHTTP = new XMLHttpRequest();
        requeteHTTP.open("POST", "delete_polygon.php", true);
        requeteHTTP.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        requeteHTTP.onreadystatechange = function () {
            if (requeteHTTP.readyState === XMLHttpRequest.DONE) {
                if (requeteHTTP.status === 200) couchesDessinees.removeLayer(couche);
            }
        };
        requeteHTTP.send("id=" + encodeURIComponent(id));
    }
    <?php
    $host = '10.11.159.10';
    $dbname = '2023_SAE5_01_lambert_maimouni1';
    $user = 'admindbetu';
    $password = 'admindbetu';
    $dsn = "pgsql:host=$host;dbname=$dbname;user=$user;password=$password";
    try {
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query_reseau_hydrolique = $pdo->query("SELECT id, coordonnee, type FROM reseau_hydrolique");
        while ($row = $query_reseau_hydrolique->fetch(PDO::FETCH_ASSOC)) {
            $geometryArray = json_decode($row['coordonnee'], true);
            $type = $row['type'];
            $id = $row['id'];
            echo "ajouterPolygoneSurCarte('$type', " . json_encode($geometryArray) . ", $id);\n";
        }
    } catch (PDOException $e) {
        die('Erreur de connexion : ' . $e->getMessage());
    }
    ?>
 

});





</script>






    <style type="text/css">

/* Styles pour les éléments du formulaire */
.form-input {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border: 1px solid #ccc;
  border-radius: 5px;
  box-sizing: border-box;
}

.submit-button {
  background-color: #4CAF50; /* Couleur de fond */
  color: white; /* Couleur du texte */
  padding: 12px 20px; /* Espacement intérieur */
  text-align: center; /* Alignement du texte */
  text-decoration: none; /* Suppression du soulignement par défaut */
  display: inline-block; /* Affichage en ligne avec une largeur automatique */
  font-size: 16px; /* Taille de la police */
  border-radius: 5px; /* Bord arrondi */
  border: none; /* Suppression de la bordure */
  cursor: pointer; /* Curseur de la souris */
  transition: background-color 0.3s; /* Animation de transition pour le changement de couleur de fond */
}

.submit-button:hover {
  background-color: #45a049; /* Couleur de fond au survol */
}

#markerForm {
    position: absolute;
    top: 50%; /* Centre le formulaire verticalement */
    left: 50%; /* Centre le formulaire horizontalement */
    transform: translate(-50%, -50%); /* Centre le formulaire par rapport à son propre centre */
    background-color: #F0FFF0;
    padding: 20px;
    border: 1px solid #ccc;
    z-index: 1000; /* Assurez-vous que le formulaire est au-dessus de la carte */
    display: none;
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
            right:27%;
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
            margin-top : 20px ;
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
color:#6B8E23;

        }

        .bande_grise {
            background-color: #90EE90;
            padding: 10px;
color:white;
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
            z-index: 1;
            margin-right: 10px;
        }

        .image_marqueur {
            position: fixed;
            margin-top: 15px;
            height : 100%;
        }

#submitButton {
  background-color: #4CAF50; /* Couleur de fond */
  color: white; /* Couleur du texte */
  padding: 12px 20px; /* Espacement intérieur */
  text-align: center; /* Alignement du texte */
  text-decoration: none; /* Suppression du soulignement par défaut */
  display: inline-block; /* Affichage en ligne avec une largeur automatique */
  font-size: 16px; /* Taille de la police */
  border-radius: 5px; /* Bord arrondi */
  border: none; /* Suppression de la bordure */
  cursor: pointer; /* Curseur de la souris */
  transition: background-color 0.3s; /* Animation de transition pour le changement de couleur de fond */
}

#submitButton:hover {
  background-color: #45a049; /* Couleur de fond au survol */
}

    </style>
</head>
<body>

<div id="menu-filtres">
    <h3>Données présente </h3>
    <label> Amphibiens </label><br><br>

<label>
    <div class="carre" style="background-color: lightblue;"></div> Reseau Hydraulique
</label> <br>


</div>

<header>
    <nav>
        <ul>
            <li><a href="page_accueil_connecte.php">Accueil</a></li>
                    <li><a href="Page_carte_connecte.php">Cartographie</a></li>
                    <li><a href="Page_Donnees_connecte.php">Données brutes </a></li>
					<a href="Page_accueil_1.php">
					<img src="Image_des_grenouilles/image_deconnexion.jfif" width="30" height="30">
					</a>
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

<form id="markerForm" method="post" action="">
    <h2>Ajouter un Amphibien</h2>
    <label for="marqueur">Code Insee :</label><br>
    <input type="text" id="marqueur" name="marqueur"><br><br>
    <label for="type">Type :</label><br>
    <select id="type" name="type">
        <?php
        try {
            $db = new PDO('pgsql:host=10.11.159.10;dbname=2023_SAE5_01_lambert_maimouni1', 'admindbetu', 'admindbetu');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $query = "SELECT id, nom_fr FROM description_amphibien";
            $statement = $db->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($result as $row) {
                    echo '<option value="' . $row['id'] . '">' . $row['nom_fr'] . '</option>';
            }
        } catch(PDOException $e) {
            echo '<option value="">Erreur lors de la récupération des données</option>';
        }
        ?>
    </select><br><br>
    <input type="submit" name="submit" value="Envoyer" id="submitButton">
</form>
</body>
</html>