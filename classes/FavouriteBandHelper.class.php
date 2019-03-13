<?php
	require_once ("../../../config.php");
	
	class FavouriteBandHelper{
		private static $stylesheets = array('homeStyleAdmin.css', 'newsStyleAdmin.css', 'toursStyleAdmin.css', 'galleryStyleAdmin.css', 'discographyStyleAdmin.css', 'aboutUsStyleAdmin.css');	

		private static $pageTitles = array('Home Admin', 'News Admin', 'Tours Admin', 'Gallery Admin', 'Discography Admin', 'About Us Admin');


		//FIXME make this method to be more general e.g: can handle the public part as well (though it might be challenging);
		public static function  displayHeaderAdmin($option, $pageScripts){
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
									<li> <a id = 'homeAnchor'" .   ($option == HOME?  "class = 'active'" : "")  . " href = '../home/home_page_admin.php'> Home </a> </li> 
									<li> <a id = 'newsAnchor' " .   ($option == NEWS?  "class = 'active'" : "")  . " href = '../news/news_admin.php'> News</a> </li>
									<li> <a id = 'toursAnchor' " .   ($option == TOURS?  "class = 'active'" : "")  . "href = '../tours/tours_admin.php' > Tours </a> </li>
									<li> <a id = 'galleryAnchor' " .   ($option == GALLERY?  "class = 'active'" : "")  . "href = '../gallery/gallery_admin.php'> Gallery </a> </li>
									<li> <a id = 'discographyAnchor' " .   ($option == DISCOGRAPHY?  "class = 'active'" : "")  . "href = '../discography/discography_admin.php' > Discography </a> </li>
									<li> <a id = 'aboutAnchor' " .   ($option == ABOUT_US?  "class = 'active'" : "")  . "href = '../about_us/about_us_admin.php'> About us </a> </li>

								</ul>";
		}


		public static function displayFooter(){
			echo "</body> </html>";

		}

	}

?>
