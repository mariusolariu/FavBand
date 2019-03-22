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
       				$imgDbId = $_POST['imgDbId'];
					$photoName = $_POST['imgSrcName'];
					deleteImage($imgDbId, $photoName);	
					addInsertPhotoButton();
					displayDbContent();
			}elseif ($val == 'Add Disc'){
					$discDbId = -1;
					$title = "";
					$year = -1;
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

	function deleteImage($imgDbId, $photoName){
		GLOBAL $conn;
		$absolutePath = GALLERY_IMGS_PATH . $photoName;
		$successful = unlink($absolutePath);

		if (!$successful) {
				FavouriteBandHelper::displayErrorMessage("Cannot delete photo, please contact website administrator!");
				return;
		}


		if ($conn){
			try{
				$query = 'DELETE FROM ' . TBL_GALLERY . ' WHERE id = :rowId';
			
				$st = $conn -> prepare($query);
				$st->bindValue(":rowId", $imgDbId, PDO :: PARAM_INT);
				$st->execute();



			}catch (PDOException $e){
				FavouriteBandHelper::displayErrorMessage("Failed to execute query: " . $e->getMessage());
			}

		}else{
			FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
		}


	}

	function updateDisc($discDbId, $title, $year){
		GLOBAL $conn;

		if ($conn){
			$query = 'SELECT img_name FROM ' . TBL_DISCOGRAPHY . ' WHERE id = :rI';
			
			$st = $conn -> prepare($query);
			$st->bindValue(":rI", $discDbId, PDO::PARAM_INT);
			$st->execute();

			$result = $st -> fetchAll();

			FavouriteBandHelper::displayErrorMessage(print_r($resulti[0], true));
		}else{
			FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
		}

		$absolutePath = GALLERY_IMGS_PATH . $oldPhotoName;
		$successful = unlink($absolutePath);

		if (!$successful) {
				FavouriteBandHelper::displayErrorMessage("Cannot update photo, please contact website administrator!");
				return;
		}

		/* $newPhotoName = $_FILES['imgI']['name']; */

		/* $storedPhotoSuccessfully = handlePhotoUpload(); */
	
		/* if (!$storedPhotoSuccessfully){ */
		/* 		FavouriteBandHelper::displayErrorMessage("Cannot update photo, please contact website administrator!"); */
		/* 		return; */
		/* } */

		/* try{ */
		/* 	$query = 'UPDATE ' . TBL_GALLERY . ' SET img_name = :in, caption = :c WHERE id = :rowId'; */
		
		/* 	$st = $conn -> prepare($query); */
		/* 	$st->bindValue(":in", $newPhotoName, PDO :: PARAM_STR); */
		/* 	$st->bindValue(":c", $_POST['captionI'], PDO :: PARAM_STR); */
		/* 	$st->bindValue(":rowId", $imgDbId, PDO :: PARAM_INT); */
		/* 	$st->execute(); */

		/* }catch (PDOException $e){ */
		/* 	FavouriteBandHelper::displayErrorMessage("Failed to execute query: " . $e->getMessage()); */
		/* } */

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
									
									echo "\n\t\t\t<table>";
									
										echo "<input type = 'hidden' name = 'discDbIdI' value = '${discDbId}'>";
										echo "<input type = 'hidden' name = 'titleI' value = '${title}'>";
										echo "<input type = 'hidden' name = 'yearI' value = '${year}'>";

										echo "<tr> <td> <img src='${imgAbsolutePath}' name = 'imgI' alt = '${title}' width = '100%' height = '200px' style = 'border-radius: 10px;' > </td> </tr>";
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
