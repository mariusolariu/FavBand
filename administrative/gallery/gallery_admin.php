<?php
	require_once "../../classes/FavouriteBandHelper.class.php";
	require_once "../../classes/DbHelper.class.php";
	require_once "../../../config.php";

	$scripts = array();
	FavouriteBandHelper::displayHeader(GALLERY_ADMIN, $scripts);
	
	$conn = DbHelper::connect();

	if (isSet($_POST['submitI'])){
		$imgDbId = $_POST['imgDbId'];
		$oldPhotoName = $_POST['oldPhotoName'];

		if ($oldPhotoName){
			updatePhoto($imgDbId, $oldPhotoName);
		}else{
			insertNewPhoto();
		}
	}

	if (isset($_POST['action'])){
			$val = $_POST['action'];

			if ($val == 'Update Image'){
					$imgDbId = $_POST['imgDbId'];
					$caption = $_POST['captionI'];
					$oldPhotoName = $_POST['imgSrcName'];
					displayInsertForm($imgDbId, $caption, $oldPhotoName);
			}elseif ($val == 'Delete Image'){
       				$imgDbId = $_POST['imgDbId'];
					$photoName = $_POST['imgSrcName'];
					deleteImage($imgDbId, $photoName);	
					addInsertPhotoButton();
					displayDbContent();
			}elseif ($val == 'Add Photo'){
					$imgDbId = -1;
					$caption = "";
					$oldPhotoName = "";
					displayInsertForm($imgDbId, $caption, $oldPhotoName);
			}			
	}else{
					addInsertPhotoButton();
					displayDbContent();
	}

	function insertNewPhoto(){
		GLOBAL $conn;

		if (!handlePhotoUpload()) {
				FavouriteBandHelper::displayErrorMessage("Cannot upload photo, please contact website administrator!");
				return;
		}	


 		if ($conn){
			try{
				$query = 'INSERT ' . TBL_GALLERY . ' (img_name, caption) VALUES (:in, :c)';
			
				$st = $conn -> prepare($query);
				$st->bindValue(":in", $_FILES['newPhotoI']['name'], PDO :: PARAM_STR);
				$st->bindValue(":c", $_POST['captionI'], PDO :: PARAM_STR);
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

	function updatePhoto($imgDbId, $oldPhotoName){
		GLOBAL $conn;

		$absolutePath = GALLERY_IMGS_PATH . $oldPhotoName;
		$successful = unlink($absolutePath);

		if (!$successful) {
				FavouriteBandHelper::displayErrorMessage("Cannot update photo, please contact website administrator!");
				return;
		}

		$newPhotoName = $_FILES['newPhotoI']['name'];

		$storedPhotoSuccessfully = handlePhotoUpload();
	
		if (!$storedPhotoSuccessfully){
				FavouriteBandHelper::displayErrorMessage("Cannot update photo, please contact website administrator!");
				return;
		}

		if ($conn){
			try{
				$query = 'UPDATE ' . TBL_GALLERY . ' SET img_name = :in, caption = :c WHERE id = :rowId';
			
				$st = $conn -> prepare($query);
				$st->bindValue(":in", $newPhotoName, PDO :: PARAM_STR);
				$st->bindValue(":c", $_POST['captionI'], PDO :: PARAM_STR);
				$st->bindValue(":rowId", $imgDbId, PDO :: PARAM_INT);
				$st->execute();

			}catch (PDOException $e){
				FavouriteBandHelper::displayErrorMessage("Failed to execute query: " . $e->getMessage());
			}

		}else{
			FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
		}
	
	}

	function handlePhotoUpload(){
		$result = false;
		$source = $_FILES['newPhotoI']['tmp_name'];

		$photoName = $_FILES['newPhotoI']['name'];
		$destination =  GALLERY_IMGS_PATH . $photoName;	
		$uploadError = $_FILES['newPhotoI']['error'];
 
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

	function displayInsertForm($imgDbId, $caption, $oldPhotoName){
?>
	 <div class ='bigDivContainer'>
		<div class = 'box2'>
			<form action = 'gallery_admin.php' method = 'post'  enctype ='multipart/form-data'>
				<input type = 'hidden' name = 'MAX_FILE_SIZE' value = '2097152'>
				<input type = 'hidden' name = 'oldPhotoName'  value = '<?php echo $oldPhotoName; ?>'>
				<input type = 'hidden' name = 'imgDbId'  value = '<?php echo $imgDbId; ?>'>

				<table id = 'theTable' >
					<tr> <td> Caption </td> <td> <input type = 'text' name = 'captionI' value = ' <?php echo $caption;?>' style = 'width : 95%;' > </td> </tr>
					<tr> <td> Pick photo </td> <td> <input type = 'file' name = 'newPhotoI' accept = 'image/*'> </td> </tr>
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
				$query = "SELECT * FROM " . TBL_GALLERY;

				$rows = $conn -> query($query);

				$records = array();

				foreach ($rows as $row){

					//img_name and caption are stored on each row of the following array
					$a_record = array($row[0], $row[1], $row[2]);

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
				<form method = "post" action = "gallery_admin.php">
					<input type = "submit" name = 'action' id = 'addPhotoButton' style = "margin: 10px auto; display: block; background-color: #2eb82e; border-radius: 6px; height: 3em; width: 10em;" value = 'Add Photo'>
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

					$entryId = $records[$i][0];
					$imgName = $records[$i][1];
					$imgAbsolutePath = GALLERY_IMGS_PATH . $imgName; 
					$caption =  $records[$i][2];
				
						echo "\n \t <div class='${boxType}'>";

							echo "\n \t \t <form action = 'gallery_admin.php' method = 'post' >";
									
								echo "<input type = 'hidden' name = 'imgDbId' value = '${entryId}'>";
								echo "<input type = 'hidden' name = 'imgSrcName' value = '${imgName}'>";
								echo "<input type = 'hidden' name = 'captionI' value = '${caption}'>";

									echo "\n\t\t\t<table>";
									
										echo "<tr> <td> <img src='${imgAbsolutePath}'  class = 'imgI' alt = '${caption}'> </td> </tr>";
										echo "<tr> <td> <span  style = 'margin-left: 5px;'>${caption} </span> </td> </tr>";
										echo "<tr> <td> <input type = 'submit' name = 'action' value = 'Delete Image' style = 'background-color: red; margin: 0px 45px;'> " . 
												" <input type = 'submit'  name = 'action' value = 'Update Image' style = 'background-color: #2eb82e;'> </td> </tr>";
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
