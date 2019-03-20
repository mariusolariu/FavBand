<?php
	require_once "../../classes/FavouriteBandHelper.class.php";
	require_once "../../classes/DbHelper.class.php";
	require_once "../../../config.php";

	$scripts = array();
	FavouriteBandHelper::displayHeader(TOURS_ADMIN, $scripts);
	
	$conn = DbHelper::connect();

	if (isset($_POST['action'])){


			if ($_POST['action'] == 'Update Tour') {
				updateTour();
				addInsertArticleButton();
				displayDbContent();
			} elseif ($_POST['action'] == 'Delete Tour'){
			    deleteTour();
				addInsertArticleButton();
				displayDbContent();
			}elseif ($_POST['action'] == 'Add Tour'){
				 $records = array();
			  	 $empty_rec = array("","","","","",-1);
				 $records[] = $empty_rec;

				
				 $newArticle = true;

				 displayTours($records, $newArticle);
			}elseif ($_POST['action'] == 'Submit Tour'){
				insertTourDB();
				addInsertArticleButton();
				displayDbContent();
			}


	}else{
		addInsertArticleButton();
		displayDbContent();
	}

	function deleteTour(){
			GLOBAL $conn;

			$tourId = (int) $_POST['tourId'];

			if ($conn){
				try{
					$query = "DELETE FROM " . TBL_TOURS . " WHERE id = :rowToDel";
					$st = $conn -> prepare($query);
					$st->bindValue(":rowToDel",$tourId, PDO::PARAM_INT);
					$st->execute(); 					

				}catch (PDOException $e){
					FavouriteBandHelper::displayErrorMessage("Failed to execute query:" . $e->getMessage());
				}

			}else{
				FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");

			}


	}

	function insertTourDB(){	
			GLOBAL $conn;
		
			if ($conn){
				try{
						$query = "INSERT INTO " . TBL_TOURS . " (city, location, start_time, tour_date, ticket_price) VALUES (:c, :l, :st, :td, :tp)";

						$theDate = str_replace('-', '/', $_POST['dateI']);

						$st = $conn -> prepare($query);
						$st->bindValue(":c", $_POST['cityI'], PDO :: PARAM_STR);
						$st->bindValue(":l", $_POST['locationI'], PDO :: PARAM_STR);
						$st->bindValue(":st", $_POST['timeI'], PDO :: PARAM_STR);
						$st->bindValue(":td", $theDate, PDO :: PARAM_STR);
						$st->bindValue(":tp", $_POST['priceI'], PDO :: PARAM_STR);
						$st->execute();
	
				}catch (PDOException $e){
					FavouriteBandHelper::displayErrorMessage("Failed to execute query: " . $e->getMessage());
				}
			}else{
				FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");

			}

	}
	


	function updateTour(){
		GLOBAL $conn;

			if ($conn){

				try{
						$query = "UPDATE " . TBL_TOURS . " SET city = :theCity, location = :l , start_time = :s , tour_date = :t , ticket_price = :tp WHERE id = :ti";

						$theDate = str_replace('-', '/', $_POST['dateI']);

						$st = $conn -> prepare($query);
						$st->bindValue(":theCity", $_POST['cityI'], PDO :: PARAM_STR);
						$st->bindValue(":l", $_POST['locationI'], PDO :: PARAM_STR);
						$st->bindValue(":s", $_POST['timeI'], PDO :: PARAM_STR);
						$st->bindValue(":t", $theDate, PDO :: PARAM_STR);
						$st->bindValue(":tp", $_POST['priceI'], PDO :: PARAM_STR);
						$st->bindValue(":ti", (int) $_POST['tourId'], PDO :: PARAM_INT);
						$st->execute();
				
				}catch (PDOException $e){
						FavouriteBandHelper::displayErrorMessage("Failed query: " . $e -> getMessage());
				} 

				DbHelper::disconnect($conn);
			}
			else{
				FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
			}
	}


	function displayDbContent(){
			GLOBAL $conn;

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
					$tourId = $row['id'];

					$a_record = array($city, $location, $start_time, $tour_date, $ticket_price, $tourId);

					array_push($records, $a_record);		
				}

				$newArticle = false;
				displayTours($records, $newArticle);

				DbHelper::disconnect($conn);
			}
			else{
				FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
			}
	}

	function addInsertArticleButton(){
?>
			<div class = "wrapperDiv">	
				<form method = "post" action = "tours_admin.php">
					<input type = "submit" name = 'action' id = 'addTourButton' style = "margin: 10px auto; display: block; background-color: #2eb82e; border-radius: 6px; height: 3em; width: 10em;" value = 'Add Tour'>
				</form>
			</div>

<?php
	}

	
	function displayTours($records, $newArticle){
		
			$noOfTours = count($records);	

			// the number of small divs (each containing an event) that are going to be wrapped in a big div
			$NO_SMALL_DIVS = 3;


			for ($i = 0; $i < $noOfTours; $i++){
				if ($i % $NO_SMALL_DIVS == 0) echo " <div class = 'bigDivContainer'> ";
					
				$city = $records[$i][0];
				$location = $records[$i][1];
				$start_time = $records[$i][2];
				$tour_date = $records[$i][3];
				$ticket_price = $records[$i][4];
				$tourId = $records[$i][5];
			
					$boxType = "box" . ($i % $NO_SMALL_DIVS );

						echo "<div class='${boxType}'>";

						 echo "<form action = 'tours_admin.php' method = 'post'>";

							echo "<input type = 'hidden' name = 'tourId' value = '${tourId}'>";

							echo "<table>";
								echo "<tr> <td> City </td> <td> <input type = 'text' name = 'cityI' value = '${city}'  style = 'width : 97%;'> </td> </tr>";
								echo "<tr> <td> Location </td> <td> <input type = 'text' name = 'locationI' value = '${location}'  style = 'width : 97%;'> </td> </tr>";
								
								//FIXME: the way the admin provides the start time for concert to be changed later eventually with a library solution since the <time>
								//       tag is not suported by major the browsers
								echo "<tr> <td> Start time </td> <td>	<input type = 'text' name = 'timeI' value = '${start_time}' style = 'width : 97%;'> </td> </tr>";

								echo "<tr> <td> Tour date </td> <td> <input type = 'date' name = 'dateI' value = '${tour_date}'  style = 'width : 97%;'> </td> </tr>";
								echo "<tr> <td> Ticket price </td> <td> £ <input type = 'text' name = 'priceI' value = '${ticket_price}' style = 'width : 90%;'> </td> </tr>";
						
								if ($newArticle){
									echo "<tr> <td> </td> <td> <input type = 'submit' name = 'action' value = 'Submit Tour' style = 'background-color: #2eb82e; margin: 5px 25%;'>";
								}else{
									echo "<tr> <td> <input type = 'submit' name = 'action' value = 'Delete Tour' style = 'background-color: red; margin: 5px 5%;' ></td>  <td> <input type = 'submit' name = 'action' value = 'Update Tour'  style = 'background-color: #2eb82e; margin: 5px 25%;'> </td></tr>";
								}

							echo "</table>";

						 echo "</form>";

						echo "</div>";

				if ( (($i+1) % $NO_SMALL_DIVS ) == 0) echo " </div> </br> ";
			}	

			//add the ending div if it needed 
			if (($i % $NO_SMALL_DIVS ) != 0) echo " </div>";

	}



	FavouriteBandHelper::displayFooter();

?>
