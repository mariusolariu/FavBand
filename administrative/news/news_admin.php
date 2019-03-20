<?php
	require_once "../../classes/FavouriteBandHelper.class.php";
	require_once "../../classes/DbHelper.class.php";
	require_once "../../../config.php";

	$scripts = array();
	FavouriteBandHelper::displayHeader(NEWS_ADMIN, $scripts);
	
		$conn = DbHelper::connect();

		if (isset($_POST['action']) && $_POST['action'] == 'Update Article'){
			updateDb();
			addInsertArticleButton();
			displayDbContent();
		}elseif (isset($_POST['action']) && $_POST['action'] == 'Delete Article'){
			deleteArticle();
			addInsertArticleButton();
			displayDbContent();
		}elseif (isset($_POST['action']) && $_POST['action'] == 'Add Article'){
			displayNews("", "", "" , "", true);
		}elseif (isset($_POST['action']) && $_POST['action'] == 'Submit Article'){
			insertArticleDB();
			addInsertArticleButton();
			displayDbContent();
		}else{
			addInsertArticleButton();
			displayDbContent();
		}

		

		function addInsertArticleButton(){
?>
			<div class = "wrapperDiv">	
				<form method = "post" action = "news_admin.php">
					<input type = "submit" name = 'action' id = 'addArticleButton' style = "margin: 10px auto; display: block; background-color: #2eb82e; border-radius: 6px; height: 3em; width: 10em;" value = 'Add Article'>
				</form>
			</div>

<?php
		}

		function insertArticleDB(){	
			GLOBAL $conn;
			$title = $_POST['newsTitle'];
			$description = $_POST['taDesc'];
			$newsDate = $_POST['newsDate'];
		
			if ($conn){
				try{
						$query = "INSERT INTO " . TBL_NEWS . " (title, description, news_date) VALUES (:t, :d, :nd)";
						$st = $conn -> prepare($query);
						$st->bindValue(":t",  $title, PDO :: PARAM_STR);
						$st->bindValue(":d", $description, PDO :: PARAM_STR);
						$st->bindValue(":nd", $newsDate, PDO :: PARAM_STR);
						$st->execute();
				}catch (PDOException $e){
					FavouriteBandHelper::displayErrorMessage("Failed to execute query: " . $e->getMessage());
				}
			}else{
				FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");

			}

		}

		function updateDb(){
			GLOBAL $conn;
			$title = $_POST['newsTitle'];
			$description = $_POST['taDesc'];
			$newsDate = $_POST['newsDate'];
			$newsId = (int) $_POST['newsId'];
		
			if ($conn){
				try{
						$query = "UPDATE " . TBL_NEWS . " SET title = :t, description = :d, news_date = :nd WHERE id = :rowToUpdate";
						$st = $conn -> prepare($query);
						$st->bindValue(":t",  $title, PDO :: PARAM_STR);
						$st->bindValue(":d", $description, PDO :: PARAM_STR);
						$st->bindValue(":nd", $newsDate, PDO :: PARAM_STR);
						$st->bindValue(":rowToUpdate", $newsId, PDO :: PARAM_INT);
						$st->execute();
				}catch (PDOException $e){
					FavouriteBandHelper::displayErrorMessage("Failed to execute query: " . $e->getMessage());
				}
			}else{
				FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");

			}
		}


		function deleteArticle(){
			GLOBAL $conn;

			$newsId = (int) $_POST['newsId'];

			if ($conn){
				try{
					$query = "DELETE FROM " . TBL_NEWS . " WHERE id = :rowToDel";
					$st = $conn -> prepare($query);
					$st->bindValue(":rowToDel",$newsId, PDO::PARAM_INT);
					$st->execute(); 					
				}catch (PDOException $e){
					FavouriteBandHelper::displayErrorMessage("Failed to execute query:" . $e->getMessage());
				}

			}else{
				FavouriteBandHelper::displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");

			}

		}

		function displayDbContent(){
			GLOBAL $conn;

				if ($conn){
					try{ 
							$query = "SELECT * FROM " . TBL_NEWS;

							$rows = $conn -> query($query);

							foreach ($rows as $row){
								$newsId = $row['id'];
								$title = $row['title'];
								$description = $row['description'];
								$news_date = $row['news_date'];

								displayNews($newsId, $title, $description, $news_date, false);
								
								echo "<p> &nbsp </p>";
							}
					}catch (PDOException $e){
						FavouriteBandHelper::displayErrorMessage("Failed to execute query: " . $e->getMessage());
					}

					/* DbHelper::disconnect($conn); */
				}
				else{
					FavouriteBandHelper:displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
				}
		}		


		/*
			This function is used to display an existent News Article or to allow the user to create a new one
		*/
		function displayNews($newsId, $title, $description, $news_date, $insertNewArticle){
			echo " <div class = 'wrapperDiv'>";
				echo "<form method = 'post' action = 'news_admin.php'>";
					echo "<table>";

							echo " <td> <input type = 'hidden' name = 'newsId' value = '${newsId}'>"; //I won't do this in real-world, I promise!

						echo "<tr>";
						echo "<td> <label> Title </label> </td>";
							echo "<td> <input type = 'text' name = 'newsTitle' style = 'width:87%; box-sizing:border-box' value = '${title}'>";

						echo "</td></tr>" ;

						echo "<tr>";
						echo "<td> Description </td>";
							echo "<td> <textarea name = 'taDesc' rows = '15' cols = '56'> ${description} </textarea>";

						echo "</td> </tr>" ;

						echo "<tr>"; 
						echo "<td> Date of publication </td>";
							echo "<td><input type = 'date' name = 'newsDate'  style = 'width:87%; box-sizing:border-box' value = '${news_date}'>";
						echo "</td> </tr>" ;

						
						echo "<tr> <td> </td> <td>";
		
							if ($insertNewArticle){
									echo "<input type = 'submit' name = 'action' value = 'Submit Article'>";
							}else{
								echo "<input type = 'submit'  style = 'background-color: red; margin: 10px'  name = 'action' value = 'Delete Article'>";	
								echo "<input type = 'submit'  style = 'background-color: #2eb82e;' name = 'action' value = 'Update Article'>";	
							}
				
						echo "</td> </tr>";

					echo "</table>";
				echo "</form>";
			echo "</div> ";
			
		}

	FavouriteBandHelper::displayFooter();

?>
