<?php
	require_once("../../../config.php");
	require_once("../../classes/FavouriteBandHelper.class.php");
	require_once("../../classes/DbHelper.class.php");

	FavouriteBandHelper::displayHeader(GALLERY_PUBLIC, array());

	$conn = DbHelper::connect();

	if ($conn){
		$query = "SELECT * FROM " . TBL_GALLERY;

		$rows = $conn -> query($query);

		$records = array();

		foreach ($rows as $row){

			//img_name and caption are stored on each row of the following array
			$a_record = array( $row[1], $row[2]);

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
			$IMGS_PER_DIV = 2;

			for ($i = 0; $i < $rowsCount; $i++){
				if ($i % $IMGS_PER_DIV  == 0) echo " <div class = 'bigDivContainer'> ";
					
					$boxType = "box" . ($i % $IMGS_PER_DIV);
					$imgAbsolutePath = GALLERY_IMGS_PATH . $records[$i][0];
					$caption =  $records[$i][1];
				
						echo "<div class='${boxType}'>";

							echo "<table>";
								echo "<tr> <td> <img src='${imgAbsolutePath}' alt = '${caption}' width = '100%' height = '200px' style = 'border-radius: 10px;' > </td> </tr>";
								echo "<tr> <td> <span style = 'margin-left: 5px;'>${caption} </span> </td> </tr>";
							echo "</table>";

						echo "</div>";

				if ( (($i+1) % $IMGS_PER_DIV) == 0) echo " </div> </br> ";
			}	

			//add the ending div if it needed 
			if (($i % $IMGS_PER_DIV) != 0) echo " </div>";
	}


	FavouriteBandHelper::displayFooter();
?>
