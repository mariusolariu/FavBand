<?php
	require_once "../../classes/FavouriteBandHelper.class.php";
	require_once "../../../config.php";

	$scripts = array();
	FavouriteBandHelper::displayHeader(GALLERY_ADMIN, $scripts);
	
   // add content


	FavouriteBandHelper::displayFooter();

?>
