	<?php
		require_once "../../../config.php";
		require_once "../../classes/HomePageHelper.class.php";
		require_once "../../classes/FavouriteBandHelper.class.php";
		require_once "../../classes/DbHelper.class.php";


		FavouriteBandHelper::displayHeader(HOME_PUBLIC, array());

		$conn = DbHelper::connect();

		if ($conn){	
				$query = "SELECT * FROM " . TBL_GEN_INFO;
				
				$rows = $conn -> query($query);
				$bandName = "";

				foreach ($rows as $row){
					/* echo "<p style = 'color : red;' > " . $row['bandName'] . "</p>"; */
					$bandName = $row['bandName'];
					$welcomeText = $row['welcomeText'];
					
					$youtubeLink = $row['youtubeLink'];

					$imgName = $row['imgName'];
				}

		
			displayPageContent();
			DbHelper::disconnect($conn);

		}else{
			FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
		}
				

		
	function displayPageContent(){	
			GLOBAL $bandName, $welcomeText, $youtubeLink, $imgName;

			echo "<div id = 'wrapperDiv'>";
						echo "<h1> Welcome to " . ($bandName != "" ? $bandName : "NoBand") . "'s website! </h1>";

						$imgPath = HomePageHelper::$imgsPath . $imgName;
						echo "<img src = '${imgPath}'alt = \"${bandName}\" align = \"right\" width = \"350\" height = \"450\" > "; 
						echo "<p> ${welcomeText}  </p>"; 
				
				
			echo "</div>";

			echo  "<div id = 'wrapperDiv2' >";

			echo  "<iframe id = 'video' src='${youtubeLink}' frameborder='0' allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>";

			echo "</div>";
	}

	FavouriteBandHelper::displayFooter();

?>


