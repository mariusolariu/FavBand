<?php
	require_once ("../../../config.php");
	
	class FavouriteBandHelper{
		private static $stylesheets = array('homeStyleAdmin.css', 'newsStyleAdmin.css', 'toursStyleAdmin.css', 'galleryStyleAdmin.css', 'discographyStyleAdmin.css', 'aboutUsStyleAdmin.css', 'homeStylePublic.css', 'newsStylePublic.css', 'toursStylePublic.css', 'galleryStylePublic.css','discographyStylePublic.css','aboutUsStylePublic.css');	

		private static $pageTitles = array('Home Admin', 'News Admin', 'Tours Admin', 'Gallery Admin', 'Discography Admin', 'About Us Admin','Home', 'News', 'Tours', 'Gallery', 'Discography', 'About Us' );


		//FIXME make this method to be more general e.g: can handle the public part as well (though it might be challenging);
		public static function  displayHeader($option, $pageScripts){
				$stylesheet = FavouriteBandHelper::$stylesheets[$option];
				$pageTitle = FavouriteBandHelper::$pageTitles[$option];
	

				$javascriptScripts = "";
				foreach ($pageScripts as $script){
					$javascriptScripts .= "<script src='${script}'> </script>  ";
				}

				echo	"<!DOCTYPE html>

						<html>
							<head>
								<meta charset = 'utf-8'>
								<title> ${pageTitle} </title>
								<link rel = 'stylesheet' href = '${stylesheet}'> 
								${javascriptScripts}
							</head>

							<body>

								<ul>
									<li> <a id = 'homeAnchor' " .   ((($option == HOME_ADMIN) || ($option == HOME_PUBLIC)) ?  "class = 'active'" : "")  . " href = " . ($option <= 5 ? '../home/home_page_admin.php' : '../home/home_page_public.php') . "> Home </a> </li> 
									<li> <a id = 'newsAnchor' " .   ((($option == NEWS_ADMIN) || ($option == NEWS_PUBLIC))?  "class = 'active'" : "") . " href = "  . ($option <= 5 ? '../news/news_admin.php' : "../news/news_public.php") . "> News</a> </li>
									<li> <a id = 'toursAnchor' " .   (($option == TOURS_ADMIN) || ($option == TOURS_PUBLIC)?  "class = 'active'" : "")  . " href = " . ($option <= 5?  '../tours/tours_admin.php' : "../tours/tours_public.php") . " > Tours </a> </li>

									<li> <a id = 'galleryAnchor' " .   (($option == GALLERY_ADMIN) || ($option == GALLERY_PUBLIC)?  "class = 'active'" : "")  . " href = " . ($option <= 5? '../gallery/gallery_admin.php' : '../gallery/gallery_public.php') . "> Gallery </a> </li>
									<li> <a id = 'discographyAnchor' " .   (($option == DISCOGRAPHY_ADMIN) || ($option == DISCOGRAPHY_PUBLIC)?  "class = 'active'" : "")  . " href = " . ($option <= 5? '../discography/discography_admin.php' : '../discography/discography_public.php') ." > Discography </a> </li>
									<li> <a id = 'aboutAnchor' " .   (($option == ABOUT_US_ADMIN) || ($option == ABOUT_US_PUBLIC)?  "class = 'active'" : "")  . " href = " . ($option <= 5 ? '../about_us/about_us_admin.php' : '../about_us/about_us_public.php') . "> About us </a> </li>

								</ul>";
		}


		public static function displayFooter(){
			echo "</body> </html>";

		}
		
		public static function displayErrorMessage($message){
			echo "<p style = 'color:red'> ${message} </p> </br>";
		}

	}

?>
