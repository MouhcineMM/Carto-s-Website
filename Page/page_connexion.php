<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Mon projet SAE</title>
    <link rel="stylesheet" type="text/css" href="CSS/connexion.css">
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
	
	
    <form id="loginForm" action="login.php" method="post">
		<h2>Connexion</h2>
        <label for="username">Nom d'utilisateur :</label><br>
        <input type="text" id="username" name="username" autocomplete="off"><br>
        <label for="password">Mot de passe :</label><br>
        <input type="password" id="password" name="password" autocomplete="off"><br><br>
        <button type="button" value="connexion" id="submitFormConnexion">Connexion</button>
    </form>

    <script>
		document.getElementById('submitFormConnexion').addEventListener('click', function(event) {
			event.preventDefault(); 


			var formData = {
				username: document.getElementById('username').value,
				password: document.getElementById('password').value
			};

			$.ajax({
				url: 'login.php',
				type: 'POST',
				data: formData,
				success: function (response) {

                if (response.trim() === 'success') {
                    window.location.href = 'Page_accueil_connecte.php';
                } else {
					
					
                    alert("Nom d'utilisateur ou mot de passe invalide");
                }
            },
				error: function (error) {
					console.log(error);
				}
			});
		});
	</script>
    <footer>

    </footer>
  </body>
</html>
