<?php
/******************************************************************************/
/*
/*      File: positiions.php
/*      Author: Samuel Fink
/*
/*      Description:
/*      This file conatins processing functions for requests relating to
/*      the positions databases
/*
/*      TODO in this file:
/*	  Add question editing support
/*	  Add ability to delete a response independent of question
/*	  Add unified search across Q and A simultaneously
/*        implment better error handling
/*        sanitize inputs
/*        improve documentation
/*        add more robust handling of incomplete/overfilled requests
/*
/******************************************************************************/

function positionByID($link, $id){
	$sth = mysql_query("SELECT * FROM csc342.positions WHERE `id` = " . $id, $link);
	if($sth == false){
                http_response_code(404);
       		die();
	}
	$rows = mysql_fetch_assoc($sth); 
        print json_encode($rows);
	die();
}

function positionsListAll($link){
        $sth1 = mysql_query("SELECT * FROM csc342.positions WHERE 1;", $link);
        if($sth1 == false){
		http_response_code(500);
		die();
	}
	$rows = array();
        while($q = mysql_fetch_assoc($sth1)) {
		array_push($rows, $q);
        }

        print json_encode($rows);
	die();
}

function positionSearch($link, $params){
	$sql = "SELECT * FROM csc342.positions WHERE";
	if(is_array($params)){
		foreach ($params as $key => $value) {
 			 $sql = $sql . " (`" . $key . "` LIKE '%" . $value . "%') AND";
		}
		$sql = $sql . " 1";
	} else {
		http_response_code(400);
		die();
	}
	$sth = mysql_query($sql, $link);
	 if($sth == false){
                http_response_code(404);
        	die();
	}
	$rows = array();
        while($r = mysql_fetch_assoc($sth)) {
            $rows[] = $r;
        }
        print json_encode($rows);
	die();
}

function addPosition($link, $data){
	if(!is_array($data)){
		http_response_code(400); //NOT ACCEPTABLE
		die();
	}
	if(!$data["name"]){
		http_response_code(400);
		die();
	}
	if(!$data["level"]){
		http_response_code(400);
		die();
	}
	if(!$data["termLength"]){
		http_response_code(400);
		die();
	}
	if(!$data["description"]){
		http_response_code(400);
		die();
	}
$sql = "INSERT INTO csc342.positions (`name`, `level`, `termLength`, `description`) VALUES ('" . $data["name"] . "', '" .  $data["level"] . "', '" . $data["termLength"] . "', '" . $data["description"] . "');";

	$sth = mysql_query($sql, $link);
        if($sth == false){
               http_response_code(400);
               die();
        }
	http_response_code(201);
	die();
}

function updatePosition($link, $data){
	$sql = "UPDATE csc342.positions SET ";

	if(!is_array($data)){
		http_response_code(400); //NOT ACCEPTABLE
		die();
	}
	if(!$data["id"]){
		http_response_code(400);
		die();
	}
	if($data["name"]){
		$sql = $sql . "`name` = '" . $data["name"] . "', ";
	}
	if($data["level"]){
		$sql = $sql . "`level` = '" . $data["level"] . "', ";
	}
	if($data["termLength"]){
		$sql = $sql . "`termLength` = '" . $data["termLength"] . "', ";
	}
	if($data["description"]){
		$sql = $sql . "`description` = '" . $data["description"] . "', ";
	}

	$sql = substr($sql, 0 , -2) . " WHERE `id` = " . $data["id"] . ";";

	$sth = mysql_query($sql, $link);
        if($sth == false){
               http_response_code(400);
               die();
        }
	http_response_code(200);
	die();
}

function deletePosition($link, $data){
	$sql = "DELETE from csc342.positions WHERE `id` = ";

         if(is_array($data)){
        	if(!$data["id"]){
                	http_response_code(400);
              		die();
       		}
		$sql = $sql . $data["id"];
	}
	else {
		$sql = $sql . $data . ";";
	}
	$sth = mysql_query($sql, $link);
        if($sth == false){
	       http_response_code(400);
               die();
        }
	http_response_code(204);
	die();
}

?>
