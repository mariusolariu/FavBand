<?php
	require_once "../../classes/FavouriteBandHelper.class.php";
	require_once "../../classes/DbHelper.class.php";
	require_once "../../../config.php";

	$scripts = array();
	FavouriteBandHelper::displayHeader(ABOUT_US_ADMIN, $scripts);
	
	$conn = DbHelper::connect();

	if (isset($_POST['action'])){

		if ($_POST['action'] == 'Update Info') updateMainContent();
		elseif ($_POST['action'] == 'Insert Info') insertMainContent();
		else deleteMainContent();
	}

	if (isset($_POST['action2'])){


		if ($_POST['action2'] == 'Update Info') updateContactUs();
		elseif ($_POST['action2'] == 'Insert Info') insertContactUs();
		else deleteContactUs();


	}

	displayMainSection();
	displayContactUs();

	function insertMainContent(){
		GLOBAL $conn;
		
		$newPhotoName = $_FILES['newPhotoI']['name'];

		$success = handlePhotoUpload();

		if (!$success) {
			return;
		}


		if ($conn){
			try{
				$query = "INSERT " . TBL_ABOUT . "  (text, img_name, title) values (:te, :in, :ti)";
				$text = $_POST['descTI'];

				$text = str_replace("\n", "</br>", $text, $count);
				
				$st = $conn -> prepare ($query);
				$st->bindValue(":te", $text, PDO::PARAM_STR);
				$st->bindValue(":in", $newPhotoName, PDO::PARAM_STR);
				$st->bindValue(":ti", $_POST['titleI'], PDO::PARAM_STR);
				$st->execute();

			}catch (PDOException $e){
				FavouriteBandHelper::displayErrorMessage("Failed to execute query: " . $e->getMessage());
			}

		}else{
			FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
			return;
		}


	}

	function insertContactUs(){
		GLOBAL $conn;		
		
		if ($conn){
			try{
				$query = "INSERT " . TBL_CONTACT . "  (phone_no, email, city, country, street_add, postcode) values (:pn, :e, :c, :co, :sa, :pc)";
				
				$st = $conn -> prepare ($query);

				$st->bindValue(":pn", $_POST['phoneI'], PDO::PARAM_STR);
				$st->bindValue(":e", $_POST['emailI'], PDO::PARAM_STR);
				$st->bindValue(":c", $_POST['cityI'], PDO::PARAM_STR);
				$st->bindValue(":co", $_POST['countryI'], PDO::PARAM_STR);
				$st->bindValue(":sa", $_POST['streetI'], PDO::PARAM_STR);
				$st->bindValue(":pc", $_POST['postcodeI'], PDO::PARAM_STR);

				$st->execute();

			}catch (PDOException $e){
				FavouriteBandHelper::displayErrorMessage("Failed to execute query: " . $e->getMessage());
			}

		}else{
			FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
			return;
		}

	}


	function updateMainContent(){
		GLOBAL $conn;

		if ($conn){
			try{

				//the tables used in this webpage store only one row at at time
				$query = "UPDATE " . TBL_ABOUT . " set text = :te, title = :ti ";
			
				$text = $_POST['descTI'];
				$text = str_replace("\n", "</br>", $text, $count);

				$st = $conn -> prepare ($query);
				$st->bindValue(":te", $text, PDO::PARAM_STR);
				$st->bindValue(":ti", $_POST['titleI'], PDO::PARAM_STR);
				$st->execute();

			}catch (PDOException $e){
				FavouriteBandHelper::displayErrorMessage("Failed to execute query: " . $e->getMessage());
			}

		}else{
			FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
			return;
		}

		//update photo only if a new one was submited
		$oldPhotoName = $_POST['oldPhotoName'];
		$newPhotoName = "";
		

		if (isset($_FILES['newPhotoI']['name'])){
			$newPhotoName = $_FILES['newPhotoI']['name'];
		}	


		if (($newPhotoName != "") && ($newPhotoName != $oldPhotoName)){
			$fullPath = ABOUT_IMGS_PATH . $oldPhotoName;
			$success = handlePhotoUpload();
			

			if (!$success) {
				FavouriteBandHelper::displayErrorMessage("Couldn't store the new photo, please contact webpage administrator!");
				return;
			}

			$success = unlink($fullPath);
			FavouriteBandHelper::displayErrorMessage($success);

			if (!$success) {
				FavouriteBandHelper::displayErrorMessage("Couldn't delete the old photo, please contact webpage administrator!");
				return;
			}

			try{
				$query = "UPDATE " . TBL_ABOUT . " SET img_name = :in";
					
				$st = $conn -> prepare ($query);
				$st->bindValue(":in", $newPhotoName, PDO::PARAM_STR);
				$st->execute();
	
			}catch (PDOException $e){
				FavouriteBandHelperLLdisplayErrorMessage("Failed to execute query: " . $e->getMessage());
			}
		
		}

	}

	function updateContactUs(){
		GLOBAL $conn;

		if ($conn){

			try{
				$query = "UPDATE " . TBL_CONTACT . " SET phone_no = :pn, email = :e, city = :c, country = :co, street_add = :sa, postcode = :pc"; 

				$st = $conn -> prepare($query);

				$st->bindValue(":pn", $_POST['phoneI'], PDO::PARAM_STR);
				$st->bindValue(":e", $_POST['emailI'], PDO::PARAM_STR);
				$st->bindValue(":c", $_POST['cityI'], PDO::PARAM_STR);
				$st->bindValue(":co", $_POST['countryI'], PDO::PARAM_STR);
				$st->bindValue(":sa", $_POST['streetI'], PDO::PARAM_STR);
				$st->bindValue(":pc", $_POST['postcodeI'], PDO::PARAM_STR);

				$st->execute();

			}catch (PDOException $e){
				FavouriteBandHelper::displayErrorMessage("Failed to execute query: " . $e->getMessage());
			}

		}else{
			FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
			return;
		}



	}

	function handlePhotoUpload(){
		$result = false;
		$source = $_FILES['newPhotoI']['tmp_name'];

		$photoName = $_FILES['newPhotoI']['name'];
		$destination =  ABOUT_IMGS_PATH . $photoName;	

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

	function deleteMainContent(){
		GLOBAL $conn;

		$fullPath = ABOUT_IMGS_PATH . $_POST['oldPhotoName'];
		
		$success = unlink($fullPath);

		if (!$success){
			FavouriteBandHelper::displayErrorMessage("Failed to delete old photo, please contact webpage administrator");
			return;
		}

		if ($conn){
			try{
				$query = "DELETE FROM " . TBL_ABOUT;
				
				$st = $conn -> prepare ($query);
				$st->execute();

			}catch (PDOException $e){
				FavouriteBandHelper::displayErrorMessage("Failed to execute query: " . $e->getMessage());
			}

		}else{
			FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
			return;
		}

	}

	function deleteContactUs(){
		GLOBAL $conn;

		if ($conn){
			try{
				$query = "DELETE FROM " . TBL_CONTACT;
				
				$st = $conn -> prepare ($query);
				$st->execute();

			}catch (PDOException $e){
				FavouriteBandHelper::displayErrorMessage("Failed to execute query: " . $e->getMessage());
			}

		}else{
			FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
			return;
		}

	}

	function displayMainSection(){
		GLOBAL $conn;

		if ($conn){	
				$query = "SELECT * FROM " . TBL_ABOUT;
				
				$statement = $conn -> query($query);

				$row = $statement -> fetch(PDO::FETCH_ORI_FIRST);

				$text = $row['text'];
				$text = str_replace("</br>", "\n", $text);
				$imgSrcName = $row['img_name'];
				$title = $row['title'];

				$imgAbsolutePath = ABOUT_IMGS_PATH . $imgSrcName;
?>

				<div class = 'wrapperDiv'>
					<form action = 'about_us_admin.php' method = 'post' enctype = 'multipart/form-data'>
						<input type = hidden name = 'oldPhotoName' value = '<?php echo $imgSrcName ?>'>

						<table id = 'mainTable' >

								<tr> <td> Current photo : '<?php echo $imgSrcName ?>' </td> <td> <input type = "file" name ='newPhotoI' accept = 'image/*'> </td> </tr>
								<tr> <td> Title </td> <td> <input type = 'text' name = 'titleI' style = "width:98%;" value = ' <?php echo $title; ?>' > </td> </tr> 
								<tr> <td> Description text </td> <td> <textarea name = 'descTI' rows = '15' cols = '59'>  <?php echo $text; ?> </textarea> </td> </tr> 
								<tr> <td>
<?php

									if ($imgSrcName != ""){
									  echo 	"<input type = 'submit' name = 'action' value = 'Delete Info' style = 'background-color: red;margin: 0 auto; display: block;' >"; 
								 	   echo  "</td> <td>";
										echo "<input type = 'submit' name = 'action' value = 'Update Info' style = 'background-color: #2eb82e; margin: 0 auto; display: block;'>";
									}else{
										echo " </td> <td> <input type = 'submit' name = 'action' value = 'Insert Info'  style = 'background-color: #2eb82e; margin: 0 auto; display:block;'> </td> </tr>";	
									}

?>										
								</td> </tr>
						</table>
					</form>

				</div>


				
<?php
		

		}else{
			FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
		}
	}
			
	function displayContactUs(){
		GLOBAL $conn;

		if ($conn){	
				$query = "SELECT * FROM " . TBL_CONTACT;
				
				$statement = $conn -> query($query);

				$row = null;
				$row = $statement -> fetch(PDO::FETCH_ORI_FIRST);

				$phone_no = $row['phone_no'];
				$email = $row['email'];
				$city = $row['city'];
				$country = $row['country'];
				$street_add = $row['street_add'];
				$postcode = $row['postcode'];

				echo "\n<div class = 'wrapperDiv'>";
					echo "\n<form method = 'post' action = 'about_us_admin.php'>";
						echo "\n\t <table style = 'margin-left: 5%;'>";
							echo "\n\t\t<tr> <td> Phone number </td> <td> <input type = 'text' name = 'phoneI' style = 'width: 97%;' value = '${phone_no}' > </td> </tr>";
							echo "\n\t\t<tr> <td> E-mail </td> <td> <input type = 'text' name = 'emailI' style = 'width: 97%;' value = '${email}'> </td> </tr>";
							echo "\n\t\t<tr> <td> Street address </td> <td> <input type = 'text' name = 'streetI' style = 'width: 97%;' value = '${street_add}'> </td> </tr>";
							echo "\n\t\t<tr> <td> Postcode </td><td> <input type = 'text' name = 'postcodeI' style = 'width: 97%;' value = '${postcode}' > </td> </tr>";
							echo "\n\t\t<tr> <td> City </td><td> <input type = 'text' name = 'cityI' style = 'width: 97%;' value = '${city}'> </td> </tr>";
							echo "\n\t\t<tr> <td> Country </td> <td> <input type = 'text' name = 'countryI' style = 'width: 97%;' value = '${country}' > </td> </tr>";

							if ($row != null){
									echo "\n\t\t<tr> <td> <input type = 'submit' name = 'action2' value = 'Delete Info' style = 'background-color: red; margin: 0 auto; display: block;'>
	 </td> <td> <input type = 'submit' name = 'action2' value = 'Update Info' style = 'background-color: #2eb82e; margin: 0 auto; display: block;'>
	</td> </tr>";
							}else{
									echo "<tr> <td> </td> <td> 	 <input type = 'submit' name = 'action2' value = 'Insert Info' style = 'background-color: #2eb82e; margin: 0 auto; display: block;'>";
							}

						echo "\n\t </table>";
					echo "\n</form>";

				echo "\n</div>";


				
		

		}else{
			FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
		}
	}
	


	DbHelper::disconnect($conn);



	FavouriteBandHelper::displayFooter();

?>
