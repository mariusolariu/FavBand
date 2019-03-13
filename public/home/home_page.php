	<?php
		require_once "../../../config.php";
		require_once "../../classes/HomePageHelper.class.php";
		require_once "../../classes/DbHelper.class.php";


		/* echo DbHelper::$test; */
		/* $test = new DbHelper(); */
		$conn = DbHelper::connect();

		if ($conn){	
				$query = "SELECT * FROM " . TBL_GEN_INFO;
				
				$rows = $conn -> query($query);
				$bandName = "";

				foreach ($rows as $row){
					/* echo "<p style = 'color : red;' > " . $row['bandName'] . "</p>"; */
					$bandName = $row['bandName'];
					$welcomeText = $row['welcomeText'];
					
					//FIXME needs formating in order to be able to embed it. E.g. valid link: https://www.youtube.com/embed/5AVOpNR2PIs
					$youtubeLink = $row['youtubeLink'];

					//FIXME: administrator should be allowed to upload this photo 
					$imgName = $row['imgName'];
				}

			DbHelper::disconnect($conn);
		}
				

	?>

		
<!DOCTYPE html>

<html>
	<head>
		<meta charset = “utf-8”>
		<title><?php echo ($bandName != "" ? $bandName : "Set title") ?> </title>
		<link rel = "stylesheet" href = "homeStyle.css">

	</head>

	<body>

		<ul>
			<li> <a id = "homeAnchor" class = "active" href = ""> Home </a> </li>
			<li> <a href = ""> News</a> </li>
			<li> <a href = ""> Tours </a> </li>
			<li> <a href = ""> Gallery </a> </li>
			<li> <a href = ""> Discography </a> </li>
			<li> <a href = ""> About us </a> </li>

		</ul>
		
	
		<div id = "wrapperDiv">
			<?php
				echo "<h1> Welcome to " . ($bandName != "" ? $bandName : "NoBand") . "'s website! </h1>";

				$imgPath = HomePageHelper::$imgsPath . $imgName;
				echo "<img src = '${imgPath}'alt = \"${bandName}\" align = \"right\" width = \"350\" height = \"450\" > "; 
				echo "<p> ${welcomeText}  </p>"; 
			?>
		
		</div>

		<div id = "wrapperDiv2" >
				<!-- how can I center this? -->

				<iframe id = "video" src="<?php echo $youtubeLink; ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		
		</div>
	</body>

</html>

