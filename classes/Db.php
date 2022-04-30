<?php

class Db
{
    protected function connect()
    {
        try{
            $user = "root";
            $password = "";
            $host = "localhost";
            $db = "ticketing";
            $conn = new PDO('mysql:host='. $host .';dbname=' . $db, $user, $password);
            return $conn;
        }
        catch (PDOException $e)
        {
            print 'Error: '.$e->getMessage(). "<br/>";
            die();
        }
    }

    // Sanitize Inputs
	  public function test_input($data) {
	    $data = strip_tags($data);
	    $data = htmlspecialchars($data);
	    $data = stripslashes($data);
	    $data = trim($data);
	    return $data;
	  }

	  // JSON Format Converter Function
	  public function message($content, $status) {
	    return json_encode(['message' => $content, 'error' => $status]);
	  }
}