<?php
	require_once "../../classes/FavouriteBandHelper.class.php";
	require_once "../../../config.php";

	$scripts = array();
	FavouriteBandHelper::displayHeaderAdmin(NEWS, $scripts);
	
   // add content


	FavouriteBandHelper::displayFooter();

?>
