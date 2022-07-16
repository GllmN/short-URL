<?php
	
	$baseUrl = 'https://guillaumeneau.com/?q=';
	
	// #1 SHORCUT does it already exist
	if(isset($_GET['q'])){

		require('src/connect.php');

		// VARIABLE (get array of shortcut)
		$shortcut = htmlspecialchars($_GET['q']);

		// REQUEST count number of shorcut with this characters
		//$db = new PDO('mysql:host=localhost;dbname=bitly;', 'root', '');
		$req =$db->prepare('SELECT COUNT(*) AS x FROM shorturl WHERE shortcut = ?');
		$req->execute(array($shortcut));

		
			// IF NOTHING
			while($result = $req->fetch()){

				if($result['x'] != 1){
					header('location: index.php?error=1');
					exit();
				}
			}

			// REDIRECTION
			$req = $db->prepare('SELECT * FROM shorturl WHERE shortcut = ?');
			$req->execute(array($shortcut));

			while($result = $req->fetch()){
				echo('test');
				header('location: '.$result['url']);
				echo('test');
				exit();
			}

	}

	// #2 Validate format and submit form
	if(isset($_POST['url'])) {

		require('src/connect.php');

		// VARIABLE (post array of URL)
		$url = $_POST['url'];

		// Check format url 
		if(!filter_var($url, FILTER_VALIDATE_URL)) {
			// PAS DE LIEN (/!\ au index.php équiv a ../)
			header('location: index.php?invalid=1');
			exit();
		}

		// VARIABLE (build shortcut)
		$shortcut = crypt($url, rand());

		// REQUEST count number of url identical
		//$db = new PDO('mysql:host=localhost;dbname=bitly;charset=utf8', 'root', '');
		$req= $db->prepare('SELECT COUNT(*) AS checkURL FROM shorturl WHERE url = ?');
		$req->execute(array($url));

		while($result = $req->fetch()){

			// IF ALREADY EXIST
			if($result['checkURL'] != 0){
				$req = $db->prepare("SELECT shortcut FROM shorturl WHERE url='$url'");
				$req->execute(array($url));
				$result = $req->fetch();
			
				// header('location: index.php/?already&shorturl='.$baseUrl.$result['shortcut']);
				header('location: index.php?already=1&shorturl='.$baseUrl.$result['shortcut']);
				exit();
			}
		}

		// SENDING REQUEST (CREATE IN DATABASE A SHORCUT URL)
		$req = $db->prepare('INSERT INTO shorturl(url, shortcut) VALUES(?, ?)');
		$req->execute(array($url, $shortcut));

			header('location: index.php?success&shorturl='.$baseUrl.$shortcut);
			exit();
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Raccourcisseur d'url express</title>
		<link rel="stylesheet" type="text/css" href="design/default.css">
		<link rel="icon" type="image/png" href="pictures/favicon.ico">
		<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200&display=swap" rel="stylesheet">
		<meta name="description" content="Raccourcisseur d'URL">
		<meta name="viewport" content="width=device-width">
	</head>
	<body>
		<!-- PRESENTATION -->
		<!-- HEADER -->
		<header> 
			<div id="entete_left" >
			</div>
			<!-- LOGO -->
			<h1>Short URL</h1>
			<div id="entete_right">
				<a href="../index.php#projets">
					<img src="pictures/navigation.png" title="Retour">
				</a>
        	</div>
		</header>
		<section id="first">
			<div id="description">
				<h2>Shortcut your URL.</h2>
			</div>
		</section>

		<section id="second">
			<div>
				<form method="post" action="index.php">
					<div id="formulaire">
						<input id="link" type="url" name="url" placeholder="Coller une adresse URL à raccourcir">
						<!-- <input id="short" type="submit" value="Raccourcir"> -->
						<button type="submit">Raccourcir</button>
					</div>
				</form>


				<?php
						if(isset($_GET['invalid'])){
							echo'<div class="alerterror">Format de l\'adresse URL non valide.';
						}
 
						else if(isset($_GET['error'])){
							?>
							<div class="alerterror">
								<b><?php echo'Raccourci URL non valide.';?>
								<?php echo '<script>window.setTimeout(function () {
									location = "index.php";
									}, 3000);</script>';					
						}

						else if(isset($_GET['already'])){?>
							<div class="alertalready">
								<?php echo'Adresse déja raccourcie :';?>
									<br>
									<b>
									<a id="alertalready-href" href="<?php echo htmlspecialchars($_GET['shorturl']); ?>" target="_blank">
										<?php echo htmlspecialchars($_GET['shorturl']); ?>
									</a>
									</b>
								<?php echo '<script>window.setTimeout(function () {
									location = "index.php";
									}, 30000);</script>';
						}
								
						else if (isset($_GET['success'])){?>
							<div class="alertsuccess">
								<?php echo'Adresse raccourcie :';?>
									<br>
									<b>
									<a id="alertsuccess-href" href="<?php echo htmlspecialchars($_GET['shorturl']); ?>" target="_blank">
										<?php echo htmlspecialchars($_GET['shorturl']); ?>
									</a>
									</b>
								<?php echo '<script>window.setTimeout(function () {
									location = "index.php";
									}, 30000);</script>';
						
						}?>


							</div>

		
				<!-- <?php if(isset($_GET['error']) && isset($_GET['message']) && isset($_GET['shorturl'])) { ?>
					<div class="center">
						<div id="result">
							<b><?php echo htmlspecialchars($_GET['message']); ?>
								<a href="<?php echo htmlspecialchars($_GET['shorturl']); ?>">
									<?php echo htmlspecialchars($_GET['shorturl']); ?>
								</a>
							</b>
						</div>
					</div>
				<?php } else if(isset($_GET['message']) && isset($_GET['shorturl'])) { ?>
					<div class="center">
						<div id="result">
							<b><?php echo htmlspecialchars($_GET['message']); ?>
								<a href="<?php echo htmlspecialchars($_GET['shorturl']); ?>">
									<?php echo htmlspecialchars($_GET['shorturl']); ?>
								</a>
							</b>
						</div>
					</div>
				<?php } ?> -->

			</div>
		</section>
		<section id="third">
	

		</section>


		<section id="brands">

		</section>

		<!-- FOOTER -->
		<footer>
			<div>
	
		</footer>

	</body>
</html>