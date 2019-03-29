<?php
	require_once "../../classes/DbHelper.class.php";
	require_once "../../../config.php";
	require_once "../../classes/FavouriteBandHelper.class.php";
	require_once "../../classes/HomePageHelper.class.php";

	

	//avoid chaching by ther browser of the script
	$scriptName = "swapContent.js?Marius=" . rand(1, getrandmax());
	$scripts = array($scriptName);

	FavouriteBandHelper::displayHeader(HOME_ADMIN, $scripts);

	$conn = DbHelper::connect();

	if (isset($_POST['submitBtn'])){
		updateDataInDb($conn);
	}

	displayDataFromDb($conn);
	

	function updateDataInDb($conn){

		if ($conn){
			//get data from submited form	
			$newBandName = $_POST['bandNameText'];
			$newWlcmTxt = $_POST['wlcmTxtArea'];	
			$newWlcmTxt = str_replace("\n", "</br>", $newWlcmTxt);	
			$newEmbeddedYTL = HomePageHelper :: getEmbedLink($_POST['youtubeLnkText']);
			
			$query = "UPDATE " . TBL_GEN_INFO . " SET bandName = :bN, welcomeText = :wT, youtubeLink = :ytL WHERE id = 1";
			
			try{
				$st = $conn -> prepare($query);
				$st -> bindValue(":bN", $newBandName, PDO::PARAM_STR);
				$st -> bindValue(":wT", $newWlcmTxt, PDO::PARAM_STR);
				$st -> bindValue(":ytL", $newEmbeddedYTL, PDO::PARAM_STR);
				$st -> execute();

			}catch(PDOException $e){
				FavouriteBandHelper::displayErrorMessage("Query failed: " . $e -> getMessage());
			}

				//only if the user actually uploaded a new photo then we'll modify the db record
				$photoName = "";

				if (isset($_FILES['imgInput']) && ($_FILES['imgInput']['name'] != "")){
					$photoName = $_FILES['imgInput']['name'];
					$photoStoredSuccessfully = handlePhotoUpload($photoName);
				}
				
				//get upload name and modify, delete the old one and replace it with the new one and store the name of the new one 
				if ($photoName && $photoStoredSuccessfully){
					
					
					try{
						$query1 = "UPDATE " . TBL_GEN_INFO . " SET imgName = :iN WHERE id = 1";

						$st = $conn-> prepare($query1);
						$st -> bindValue(":iN", $photoName, PDO::PARAM_STR);
						$st -> execute();

					}catch(PDOException $e){
						FavouriteBandHelper::displayErrorMessage("Query failed: " . $e -> getMessage());
					}

				}

		}else{
			FavouriteBandHelper::displayErrorMessage("Connection to Db couldn't be established");	
		}


	}


	function handlePhotoUpload($photoName){
		$result = false;
		$source = $_FILES['imgInput']['tmp_name'];
		
		$imgsPath = "../../resources/home/imgs/";
		$destination =  $imgsPath . $photoName;	
		$uploadError = $_FILES['imgInput']['error'];
 
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

				if ($result){ //delete the previous photo stored in destination
					$handle = opendir("../../resources/home/imgs");
			
					while ($file = readdir($handle)){
						if (($file != ".") && ($file != "..") && ($file != $photoName)) unlink($imgsPath . $file);
					}

					closedir($handle);	
				}

		return $result;
	}

	function displayDataFromDb($conn){

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
				
			}else{
				FavouriteBandHelper::displayErrorMessage("Connection to Db couldn't be established");	
			}
	}



	function displayCurrentData($rows){
		echo  '<div class = "wrapperDiv" id = "wrapperDivId">';
		
		foreach ($rows as $row){

			$bandName = $row['bandName'];
			$welcomeText = $row['welcomeText'];
			$welcomeText = str_replace("</br>", "\n", $welcomeText);

			$youtubeLink = $row['youtubeLink'];
			$imgName = $row['imgName'];
		}

		

			$imgPath = HomePageHelper :: $imgsPath . $imgName; 
				echo "<table>";
						echo "<tr> <th> Band Name </th> <th> Welcome Text </th> <th> Youtube Link </th> <th> Image </th> </tr>";
						echo "<tr> <td> <span id='bandName'> ${bandName} </span> </td> " .
								  "<td> <textarea id = 'wTA' rows = '15' cols = '40' disabled> ${welcomeText} </textarea> </td>" .
								  "<td> <iframe id = 'video' src = '${youtubeLink}'  allow ='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen> </iframe> </td>" .
								  "<td> <img id = 'imgId' src = '${imgPath}' alt = '${imgName}' width = '100' height = '150'> </td> </tr>";	
				

				echo "</table>";
	

			echo "<input type = 'button' class = 'updateButton'  name = 'updateButton' id = 'updateButton' value = 'update'>";

		echo '</div>';
	}

	FavouriteBandHelper::displayFooter();

?>		


