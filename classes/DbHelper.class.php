<?php
	require_once "../../../config.php";

	 class DbHelper{
		/* public static $test = "TEST"; */

        public static function connect(){ 

            try {
                $conn = new PDO( DB_DSN, USERNAME, PASSWORD );
                $conn->setAttribute( PDO::ATTR_PERSISTENT, true );
                $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            } catch ( PDOException $e ) {
                die( "Connection failed: " . $e->getMessage() );
            }

            return $conn;
        }

        public static function disconnect( $conn ) {
            $conn = "";
        }

	}
 
?>
