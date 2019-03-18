<?php

	class HomePageHelper{
		public static $imgsPath = "../../resources/home/imgs/"; 

		// a youtube link in order to be embedded needs to be formated like this:
		// https://www.youtube.com/embed/VIDEO_ID
		function getEmbedLink($youtubeLink){
			$idLength = 11;
			$videoId = substr($youtubeLink, - $idLength);
			
			return "https://www.youtube.com/embed/" . $videoId;
		}

		function getNormalLink($embededLink){
			$idLength = 11;
			$videoId = substr($youtubeLink, - $idLength);
			
			return "https://www.youtube.com/watch?v=" . $videoId;
		}

	}

?>
