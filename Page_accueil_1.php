<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Mon projet SAE</title>
    <link rel="stylesheet" type="text/css" href="CSS/test.css">
    <link rel="stylesheet" type="text/js" href="test.js">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
      section {
        scroll-margin-top: 10px;
      }
    </style>
  </head>
  <body>
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

    <section id="photo_intro">
      <center>
        <span style="display: inline-block;">
          <h1> SigWeb les grenouilles à Rennes </h1>
        </span>
      </center>
      <span id="logo" style="display: inline-block;">
        <img src='Image_des_grenouilles/logo_upvd.PNG' id="logo_upvd_acc">
      </span>
    </section>

    <div id="menu">
      <ul>
        <li><a onclick="scrollToSection('Intro')">Introduction</a></li>
        <li><a onclick="scrollToSection('Remerciements')">Remerciements</a></li>
        <li><a onclick="scrollToSection('Donnees')">Données</a></li>
        <li><a onclick="scrollToSection('Presentation')">Présentation du site</a></li>

      </ul>
    </div>
    <div id="cache"></div>

    <div class="main-content">
      <h2 id="Intro">Introduction</h2>
      <div id="paragraphe1">
        <p>
          Au cours de notre 3ème année en BUT SD, nous avons réalisé un projet pour compléter la SAE 5.01.
          Pour ce faire, nous avons créé un site web qui se focalise sur les amphibiens dans la périphérie de Rennes.
          Ce site va donc permettre à n’importe qui de pouvoir visualiser des données de plusieurs manières.
        </p>
      </div>

      <h2 id="Remerciements">Remerciements</h2>
      <div class="container">
        <div id="paragraphe2">
          Ce projet a été fait en collaboration avec des élèves en licence d'arts plastiques à l'université de Rennes 2. Ces élèves ont été influencés par notre projet pour faire le leur, et ainsi réaliser un projet de photographies dans le cadre de la validation de leur 3ème année.</br>
          Malheureusement, ces élèves ont constaté que les différents endroits où vivaient les amphibiens ont pour la plupart presque disparu.
          <p style="text-align: center;">
            Nous avons donc utilisé de superbes photos des amphibiens sur place !
          </p>
        </div>
        <div class="image">
          <img src="Image_des_grenouilles/photo_grenouille_bidon.jpg" width="500" height="300">
        </div>
      </div>

      <h2 >Données</h2>
      <div id="Donnees"> 
        <div id="paragraphe3">
          <p>Nous avons choisi différentes couches d'analyses à utiliser pour comprendre comment sont réparties les populations des amphibiens autour de Rennes.</p>
          <p>Nous avons donc pris en compte :</p>
          <ul>
		  <li> <a href='https://data.rennesmetropole.fr/explore/dataset/amphibien_mnie_pays_de_rennes_2016/information/'>Les amphibiens </a></li>
            <li> <a href='https://data.rennesmetropole.fr/explore/dataset/limites-communales-referentielles-de-rennes-metropole-polygones/information/'>Les communes de la périphérie de Rennes</a></li>
            <li> <a href='https://data.rennesmetropole.fr/explore/dataset/synthese_plui_2021/information/'>L'agriculture </a></li>
            <li><a href='https://data.rennesmetropole.fr/explore/dataset/lotissements/information/'>Les lotissements</a></li>
            <li><a href='https://data.rennesmetropole.fr/explore/dataset/reseau_hydrographique/information/'>Les zones hydrauliques</a></li>
            <li><a href='https://data.rennesmetropole.fr/explore/dataset/surface_espace_vert_hors_rennes/information/'>Les espaces verts</a></li>
			<li><a href= 'https://data.rennesmetropole.fr/explore/dataset/occupation_sol_mnie_pays_de_rennes_2016/information/'> Occupation du sol </a>
          </ul>
          <p>L'ensemble des données utilisées ont été récoltées entre 2015 et 2017.</p>
        </div>
      </div>
      </br>

      <h2>Présentation du site</h2>

      <h3>
        <a href="Page_carte.php">
          Deuxième page: La carte !
        </a>
      </h3>
      <div id="paragraphe4">
        Nous avons construit une carte interactive qui permet d'avoir une représentation de toutes les données récoltées.
        Les différentes couches de données sont filtrables!
        Vous pouvez cliquer sur les différents points positionnés, on pourra alors vous donner plus de détails sur l'amphibien choisi.
      </div>
      <div class="image_carte" >
        <img src="Image_des_grenouilles/photo_carte.PNG" width="400" height="300" style="margin-left: 100px;" >
      </div>  

      <br>
      <div>
        <div class="image_bdd">
          <img src="Image_des_grenouilles/bdd.PNG" width="300" height="220" style="margin-left: 100px; margin-top:-50px">
        </div>
        <div  id="paragraphe_bdd">

            <h3>
              <a href="Page_Donnees.php">
              Troisième page: Les données brutes !
              </a>
            </h3>
              <div id="paragraphe5">
                    <p>Nous vous donnons accès à nos données pour les utiliser de votre côté.</p>
                    <p>Vous pouvez ainsi entièrement analyser ces données grâce aux différents filtres qu'on vous laisse à disposition</p>
              </div>

        </div>
<br><br>
<div id="login"></div>



      <script>
        function scrollToSection(sectionId) {
          const section = document.getElementById(sectionId);
          const offset = 10; // Décalage de 10px
          if (sectionId === 'Donnees') {
            const offset = 50;
          }
          const headerHeight = document.querySelector('header').offsetHeight;
          const sectionTop = section.offsetTop - offset - headerHeight;

          window.scrollTo({
            top: sectionTop,
            behavior: 'smooth' 
          });
        }
      </script>
    </footer>
  </body>
</html>
