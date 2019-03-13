<?php
	require_once "../../classes/DbHelper.class.php";
	require_once "../../../config.php";
	require_once "../../classes/FavouriteBandHelper.class.php";
	require_once "../../classes/HomePageHelper.class.php";

	
	$conn = DbHelper::connect();


	//avoid chaching by ther browser of the script
	$scriptName = "swapContent.js?Marius=" . rand(1, getrandmax());
	$scripts = array($scriptName);

	FavouriteBandHelper::displayHeaderAdmin(HOME, $scripts);

	if ($conn){
			$myQuery = "SELECT * FROM " . TBL_GEN_INFO;
			$st = $conn -> prepare($myQuery);
			$st -> execute();
			

			$rows = $st -> fetchAll();

			if ( count($rows) === 1){
				displayCurrentData($rows);
			}else{
				$data = array();
				displayForm($data);
			}
		
	}



	function displayCurrentData($rows){
		echo  '<div class = "wrapperDiv" id = "wrapperDivId">';
		
		foreach ($rows as $row){
			/* echo "<p> " . $row['bandName'] . " " . $row['youtubeLink'] . "</p> </br>"; */

			$bandName = $row['bandName'];
			$welcomeText = $row['welcomeText'];
			$youtubeLink = $row['youtubeLink'];
			$imgName = $row['imgName'];
		}

		

			$imgPath = HomePageHelper :: $imgsPath . $imgName; 
				echo "<table>";
						echo "<tr> <th> Band Name </th> <th> Welcome Text </th> <th> Youtube Link </th> <th> Image </th> </tr>";
						echo "<tr> <td> <span id='bandName'> ${bandName} </span> </td> " .
								  "<td> <textarea id = 'wTA' rows = '15' cols = '40' disabled> ${welcomeText} </textarea> </td>" .
								  "<td> <iframe id = 'video' src = '${youtubeLink}' frameborder = '0' allow ='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen> </iframe> </td>" .
								  "<td> <img id = 'imgId' src = '${imgPath}' alt = '${imgName}' width = '100px' height = '150px'> </td> </tr>";	
				

				echo "</table>";
	

			echo "<input type = 'button' class = 'updateButton'  name = 'updateButton' id = 'updateButton' value = 'update'>";

			echo "</br>";
		echo '</div>';
	}

?>		

	</body>

</html>

