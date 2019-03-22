<?php
	require_once("../../../config.php");
	require_once("../../classes/FavouriteBandHelper.class.php");
	require_once("../../classes/DbHelper.class.php");

	FavouriteBandHelper::displayHeader(DISCOGRAPHY_PUBLIC, array());

	$conn = DbHelper::connect();

	if ($conn){
		$query = "SELECT * FROM " . TBL_DISCOGRAPHY;

		$rows = $conn -> query($query);

		$records = array();

		foreach ($rows as $row){

			//img_name and caption are stored on each row of the following array
			$a_record = array( $row[1], $row[2], $row[3]);

			array_push($records, $a_record);		
		}

		displayDbContent($records);

		DbHelper::disconnect($conn);
	}
	else{
		FavouriteBandHelper:displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
	}


	function displayDbContent($records){
		
			$rowsCount = count($records);	
			$IMGS_PER_DIV = 3;

			for ($i = 0; $i < $rowsCount; $i++){
				if ($i % $IMGS_PER_DIV  == 0) echo " \n <div class = 'bigDivContainer'> ";
					
					$boxType = "box" . ($i % $IMGS_PER_DIV);

					$title =  $records[$i][0];
					$imgAbsolutePath = DISCOGRAPHY_IMGS_PATH . $records[$i][1];
					$year =  $records[$i][2];

						echo "\n \t <div class='${boxType}'>";

							echo "\n \t\t <table>";
								echo "\n\t\t\t <tr> <td> <img src='${imgAbsolutePath}' alt = '${title}' width = '100%' height = '200px' style = 'border-radius: 10px;' > </td> </tr>";
								echo "\n\t\t\t<tr> <td> <span style = 'margin-left: 5px;'>${title} </span> </td> </tr>";
								echo "\n\t\t\t<tr> <td> <span style = 'margin-left: 5px;'>${year} </span> </td> </tr>";
							echo "\n\t\t</table>";

						echo "\n\t</div>";

				if ( (($i+1) % $IMGS_PER_DIV) == 0) echo "\n </div> </br> ";
			}	

			//add the ending div if it needed 
			if (($i % $IMGS_PER_DIV) != 0) echo "\n </div>";
	}


	FavouriteBandHelper::displayFooter();
?>
