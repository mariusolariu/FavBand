<?php
	require_once "../../classes/FavouriteBandHelper.class.php";
	require_once "../../classes/DbHelper.class.php";
	require_once "../../../config.php";

	$scripts = array();
	FavouriteBandHelper::displayHeader(DISCOGRAPHY_ADMIN, $scripts);
	
	$conn = DbHelper::connect();

	if (isSet($_POST['submitI'])){
		$discDbId = $_POST['discDbIdI'];
		$title = $_POST['titleI'];
		$year = $_POST['yearI'];

		if ($discDbId != -1){
			updateDisc($discDbId, $title, $year);
		}else{
			insertNewDisc();
		}
	}

	if (isset($_POST['action'])){
			$val = $_POST['action'];

			if ($val == 'Update Disc'){
					$discDbId = $_POST['discDbIdI'];
					$title = $_POST['titleI'];
					$year = $_POST['yearI'];
					displayInsertForm($discDbId, $title, $year);
			}elseif ($val == 'Delete Disc'){
					$discDbId = $_POST['discDbIdI'];
					deleteImage($discDbId);	
					addInsertPhotoButton();
					displayDbContent();
			}elseif ($val == 'Add Disc'){
					$discDbId = -1;
					$title = "";
					$year = "";
					displayInsertForm($discDbId, $title, $year);
			}			
	}else{
					addInsertPhotoButton();
					displayDbContent();
	}

	function insertNewDisc(){
		GLOBAL $conn;

		if (!handlePhotoUpload()) {
				FavouriteBandHelper::displayErrorMessage("Cannot upload photo, please contact website administrator!");
				return;
		}	


 		if ($conn){
			try{
				$query = 'INSERT ' . TBL_DISCOGRAPHY . ' (title, img_name, year) VALUES (:t, :in, :y)';
			
				$st = $conn -> prepare($query);
				$st->bindValue(":t", $_POST['titleI'], PDO :: PARAM_STR);
				$st->bindValue(":in", $_FILES['imgI']['name'], PDO :: PARAM_STR);
				$st->bindValue(":y", $_POST['yearI'], PDO :: PARAM_INT);
				$st->execute();

			}catch (PDOException $e){
				FavouriteBandHelper::displayErrorMessage("Failed to execute query: " . $e->getMessage());
			}

		}else{
			FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
		}


	}

	function deleteImage($discDbId){
		GLOBAL $conn;

		$photoName = getImgName($discDbId);

		if ($photoName  == ""){
				FavouriteBandHelper::displayErrorMessage("Failed to retrieve requested resource, please contact webpage administrator! ");
				return;
		}


		$absolutePath = DISCOGRAPHY_IMGS_PATH . $photoName;
		$successful = unlink($absolutePath);

		if (!$successful) {
				FavouriteBandHelper::displayErrorMessage("Cannot delete disc, please contact website administrator!");
				return;
		}


		if ($conn){
			try{
				$query = 'DELETE FROM ' . TBL_DISCOGRAPHY . ' WHERE id = :rowId';
			
				$st = $conn -> prepare($query);
				$st->bindValue(":rowId", $discDbId, PDO :: PARAM_INT);
				$st->execute();

			}catch (PDOException $e){
				FavouriteBandHelper::displayErrorMessage("Failed to execute query: " . $e->getMessage());
			}

		}else{
			FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
		}


	}

	function getImgName($discDbId){
		$result = "";
		GLOBAL $conn;

		if ($conn){
			try{	
				$query = 'SELECT img_name FROM ' . TBL_DISCOGRAPHY . ' WHERE id = :rI';
				
				$st = $conn -> prepare($query);
				$st->bindValue(":rI", $discDbId, PDO::PARAM_INT);
				$st->execute();

				$result = $st -> fetchAll();

				$result = $result[0]['img_name'];
			
			}catch (PDOException $e){
				FavouriteBandHelper::displayErrorMessage("Failed to execute query: " . $e->getMessage());
				return;
			}


		}else{
			FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
			return;
		}


		return $result;
	}

	function updateDisc($discDbId, $title, $year){
		GLOBAL $conn;

		$oldPhotoName = getImgName($discDbId);

		if ($oldPhotoName  == ""){
				FavouriteBandHelper::displayErrorMessage("Failed to retrieve requested resource, please contact webpage administrator! ");
				return;
		}

		$absolutePath = DISCOGRAPHY_IMGS_PATH . $oldPhotoName;
		$successful = unlink($absolutePath);

		if (!$successful) {
				FavouriteBandHelper::displayErrorMessage("Cannot update disc, please contact website administrator!");
				return;
		}

		$newPhotoName = $_FILES['imgI']['name'];

		$storedPhotoSuccessfully = handlePhotoUpload();
	
		if (!$storedPhotoSuccessfully){
				FavouriteBandHelper::displayErrorMessage("Cannot update photo, please contact website administrator!");
				return;
		}

		try{
			$query = 'UPDATE ' . TBL_DISCOGRAPHY . ' SET img_name = :in, title = :t, year = :y WHERE id = :rowId';
		
			$st = $conn -> prepare($query);
			$st->bindValue(":in", $newPhotoName, PDO :: PARAM_STR);
			$st->bindValue(":t", $title, PDO :: PARAM_STR);
			$st->bindValue(":y", $year, PDO :: PARAM_INT);
			$st->bindValue(":rowId", $discDbId, PDO :: PARAM_INT);
			$st->execute();

		}catch (PDOException $e){
			FavouriteBandHelper::displayErrorMessage("Failed to execute query: " . $e->getMessage());
		}

	}

	function handlePhotoUpload(){
		$result = false;
		$source = $_FILES['imgI']['tmp_name'];

		$photoName = $_FILES['imgI']['name'];
		$destination =  DISCOGRAPHY_IMGS_PATH . $photoName;	
		$uploadError = $_FILES['imgI']['error'];
 
				if ( $uploadError == UPLOAD_ERR_OK){
			
					//move photo to the permanent storage location
					if (!move_uploaded_file($source, $destination)) FavouriteBandHelper::displayErrorMessage("Sorry, there was a problem uploading the photo!");
					else $result = true; //successful upload

				}else{
				
					switch ($uploadError){
						case UPLOAD_ERR_INI_SIZE:
							$message = "The photo is larger than the server allows.";
							break;


						case UPLOAD_ERR_FORM_SIZE:
							$message = "The photo is larger than the websites supports.";
							break;

						case UPLOAD_ERR_NO_FILE:
							$message = "No file was uploaded. Make sure you choose a file to upload.";
							break;
						
						default:
							$message = "Please contact your webpage administrator for help.";
					}

					FavouriteBandHelper::displayErrorMessage($message);
				}


		return $result;
	}

	function displayInsertForm($discDbId, $title, $year){
?>
	 <div class ='bigDivContainer'>
		<div class = 'box2'>
			<form action = 'discography_admin.php' method = 'post'  enctype ='multipart/form-data'>
				<input type = 'hidden' name = 'MAX_FILE_SIZE' value = '2097152'>
				<input type = 'hidden' name = 'titleI'  value = '<?php echo $title; ?>'>
				<input type = 'hidden' name = 'discDbIdI'  value = '<?php echo $discDbId; ?>'>
				<input type = 'hidden' name = 'yearI'  value = '<?php echo $year; ?>'>

				<table id = 'theTable' >
					<tr> <td> Album title </td> <td> <input type = 'text' name = 'titleI' value = ' <?php echo $title;?>' style = 'width : 97%;' > </td> </tr>
					<tr> <td> Release year </td> <td> <input type = 'text' name = 'yearI' value = ' <?php echo $year;?>' style = 'width : 97%;' > </td> </tr>
					<tr> <td> Album cover </td> <td> <input type = 'file' id = 'imgI' name = 'imgI' accept = 'image/*'> </td> </tr>
					<tr> <td> </td> <td> <input type = 'submit' name = 'submitI' value = 'Submit'> </td> </tr>

				</table>

			</form>
		</div>
	</div>

<?php
	}

	function displayDbContent(){
			GLOBAL $conn;

			if ($conn){
				$query = "SELECT * FROM " . TBL_DISCOGRAPHY;

				$rows = $conn -> query($query);

				$records = array();

				foreach ($rows as $row){

					$a_record = array($row[0], $row[1], $row[2], $row[3]);

					array_push($records, $a_record);		
				}

				displayContent($records);

				DbHelper::disconnect($conn);
			}
			else{
				FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
			}
	}

	function updateImage(){
		GLOBAL $conn;

		if ($conn){
			try{
				

			}catch(PDOException $e){

			}

		}else{
				FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
		}

	}

	function addInsertPhotoButton(){
?>
			<div class = "wrapperDiv">	
				<form method = "post" action = "discography_admin.php">
					<input type = "submit" name = 'action'  style = "margin: 10px auto; display: block; background-color: #2eb82e; border-radius: 6px; height: 3em; width: 10em;" value = 'Add Disc'>
				</form>
			</div>

<?php
	}

	function displayContent($records){
		
			$rowsCount = count($records);	
			$IMGS_PER_DIV = 2;

			for ($i = 0; $i < $rowsCount; $i++){
				if ($i % $IMGS_PER_DIV  == 0) echo "\n\n <div class = 'bigDivContainer'> ";
					
					$boxType = "box" . ($i % $IMGS_PER_DIV);

					$discDbId = $records[$i][0];
					$title = $records[$i][1];
					$imgName = $records[$i][2];
					$imgAbsolutePath = DISCOGRAPHY_IMGS_PATH . $imgName; 
					$year =  $records[$i][3];
				
						echo "\n \t <div class='${boxType}'>";

							echo "\n \t \t <form action = 'discography_admin.php' method = 'post' >";
									
								echo "<input type = 'hidden' name = 'discDbIdI' value = '${discDbId}'>";
								echo "<input type = 'hidden' name = 'titleI' value = '${title}'>";
								echo "<input type = 'hidden' name = 'yearI' value = '${year}'>";

									echo "\n\t\t\t<table>";
									

										echo "<tr> <td> <img src='${imgAbsolutePath}'  class = 'discImg' alt = '${title}' > </td> </tr>";
										echo "<tr> <td> <span  style = 'margin-left: 5px;'>${title} </span> </td> </tr>";
										echo "<tr> <td> <span  style = 'margin-left: 5px;'>${year} </span> </td> </tr>";
										echo "<tr> <td> <input type = 'submit' name = 'action' value = 'Delete Disc' style = 'background-color: red; margin: 0px 45px;'> " . 
												" <input type = 'submit'  name = 'action' value = 'Update Disc' style = 'background-color: #2eb82e;'> </td> </tr>";
									echo "</table>";
							echo "</form>";

						echo "\n\t </div>";

				if ( (($i+1) % $IMGS_PER_DIV) == 0) echo "\n </div> \n";
			}	

			//add the ending div if it needed 
			if (($i % $IMGS_PER_DIV) != 0) echo "\n </div>";
	}

	

	FavouriteBandHelper::displayFooter();

?>
