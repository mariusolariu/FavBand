<?php
	require_once("../../../config.php");
	require_once("../../classes/FavouriteBandHelper.class.php");
	require_once("../../classes/DbHelper.class.php");

	FavouriteBandHelper::displayHeader(ABOUT_US_PUBLIC, array());

	$conn = DbHelper::connect();

	displayMainSection();

	displayContactUs();

	function displayMainSection(){
		GLOBAL $conn;

		if ($conn){	
				$query = "SELECT * FROM " . TBL_ABOUT ;
				
				$statement = $conn -> query($query);

				$row = null;
				$row = $statement -> fetch(PDO::FETCH_ASSOC);
				

				if ($row != null){

					$text = $row['text'];
					$imgSrcName = $row['img_name'];
					$title = $row['title'];

					$imgAbsolutePath = ABOUT_IMGS_PATH . $imgSrcName;
?>

					<div class = 'wrapperDiv'>
						<img src = '<?php echo $imgAbsolutePath; ?>' alt = 'Image showing the band' align = 'top' id = 'topImgId'>

						<h2> <?php echo $title; ?> </h2>

						<p> <?php echo $text; ?> </p>


					</div>
				
<?php
		
				}

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

				if ($row != null){
					$phone_no = $row['phone_no'];
					$email = $row['email'];
					$city = $row['city'];
					$country = $row['country'];
					$street_add = $row['street_add'];
					$postcode = $row['postcode'];

					echo "\n<div class = 'wrapperDiv'>";
						echo "<h3 style = 'margin-left: 1%' > Contact us: </h3>";
						echo "\n\t <table style = 'margin-left: 5%;'>";
							echo "<tr> <td> Phone number </td> <td> ${phone_no} </td> </tr>";
							echo "<tr> <td> E-mail </td> <td> ${email} </td> </tr>";
							echo "<tr> <td> Street address </td> <td> ${street_add} </td> </tr>";
							echo "<tr> <td> Postcode </td><td> ${postcode} </td> </tr>";
							echo "<tr> <td> City </td><td> ${city} </td> </tr>";
							echo "<tr> <td> Country </td> <td> ${country} </td> </tr>";
						echo "\n\t </table>";

					echo "</div>";
				}

		}else{
			FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
		}
	}
	


	DbHelper::disconnect($conn);

	//join newsletter
	FavouriteBandHelper::displayFooter();
?>
