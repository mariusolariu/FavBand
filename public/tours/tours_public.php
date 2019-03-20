<?php
	require_once("../../../config.php");
	require_once("../../classes/FavouriteBandHelper.class.php");
	require_once("../../classes/DbHelper.class.php");

	FavouriteBandHelper::displayHeader(TOURS_PUBLIC, array());

	$conn = DbHelper::connect();

	if ($conn){
		$query = "SELECT * FROM " . TBL_TOURS;

		$rows = $conn -> query($query);

		$records = array();

		foreach ($rows as $row){
			$city = $row['city'];
			$location = $row['location'];
			$start_time = $row['start_time'];
			$tour_date = $row['tour_date'];
			$ticket_price = $row['ticket_price'];

			$a_record = array($city, $location, $start_time, $tour_date, $ticket_price);

			array_push($records, $a_record);		
		}

		displayDbContent($records);

		DbHelper::disconnect($conn);
	}
	else{
		FavouriteBandHelper:displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
	}


	function displayDbContent($records){
		
			$noOfTours = count($records);	

			for ($i = 0; $i < $noOfTours; $i++){
				if ($i % 4 == 0) echo " <div class = 'bigDivContainer'> ";
					
				$city = $records[$i][0];
				$location = $records[$i][1];
				$start_time = $records[$i][2];
				$tour_date = $records[$i][3];
				$ticket_price = $records[$i][4];

					$boxType = "box" . ($i % 4);

						echo "<div class='${boxType}'>";

							echo "<table>";
								echo "<tr> <td> City </td> <td> ${city} </td> </tr>";
								echo "<tr> <td> Location </td> <td> ${location} </td> </tr>";
								echo "<tr> <td> Start time </td> <td> ${start_time} </td> </tr>";
								echo "<tr> <td> Tour date </td> <td> ${tour_date} </td> </tr>";
								echo "<tr> <td> Ticket price </td> <td> £ ${ticket_price} </td> </tr>";
							echo "</table>";

						echo "</div>";

				if ( (($i+1) % 4) == 0) echo " </div> </br> ";
			}	

			//add the ending div if it needed 
			if (($i % 4) != 0) echo " </div>";
	}

	FavouriteBandHelper::displayFooter();
?>
