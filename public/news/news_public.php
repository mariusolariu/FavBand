<?php
	require_once("../../../config.php");
	require_once("../../classes/FavouriteBandHelper.class.php");
	require_once("../../classes/DbHelper.class.php");

	FavouriteBandHelper::displayHeader(NEWS_PUBLIC, array());

	$conn = DbHelper::connect();

	if ($conn){
		$query = "SELECT * FROM " . TBL_NEWS;

		$rows = $conn -> query($query);

		foreach ($rows as $row){
			$title = $row['title'];
			$description = $row['description'];
			$news_date = $row['news_date'];

			displayNews($title, $description, $news_date);
		}

		DbHelper::disconnect($conn);
	}
	else{
		FavouriteBandHelper:displayErrorMessage("Database connection could not be established, please contact the webpage administrator!");
	}
	

	function displayNews($title, $description, $news_date){
		/* echo "${news_date} ${title} ${description}"; */

		echo "<div class = 'wrapperDiv'>";
			echo "<h2> ${title} </h2>";
			echo "<p>     ${description} </p>";
			echo "<p style = 'text-indent: 0em'> Date of publication: ${news_date} </p>";
		echo "</div>";
			
	}
	
	FavouriteBandHelper::displayFooter();
?>
