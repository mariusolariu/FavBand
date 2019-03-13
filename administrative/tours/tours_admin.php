<?php
	require_once "../../classes/FavouriteBandHelper.class.php";
	require_once "../../../config.php";

	$scripts = array();
	FavouriteBandHelper::displayHeaderAdmin(TOURS, $scripts);
	
   // add content


	FavouriteBandHelper::displayFooter();

?>
