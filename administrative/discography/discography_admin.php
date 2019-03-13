<?php
	require_once "../../classes/FavouriteBandHelper.class.php";
	require_once "../../../config.php";

	$scripts = array();
	FavouriteBandHelper::displayHeaderAdmin(DISCOGRAPHY, $scripts);
	
   // add content


	FavouriteBandHelper::displayFooter();

?>
