<?php
/******************************************************************************/
/*
/*      File: QandA.php
/*      Author: Samuel Fink
/*
/*      Description:
/*      This file conatins processing functions for requests relating to
/*      the question and answer databases
/*
/*      TODO in this file:
/*	  Add question editing support
/*	  Add ability to delete a response independent of question
/*	  Add unified search across Q and A simultaneously
/*        implment better error handling
/*        sanitize inputs
/*        improve documentation
/*        add more robust handling of incomplete/overfilled requests
/*	  add more appopriate response codes on insert/update/delete/etc...
/*
/******************************************************************************/

function questionByID($link, $id){
	$sth = mysql_query("SELECT * FROM csc342.questions WHERE `id` = " . $id, $link);
	if($sth == false){
                http_response_code(404);
       		die();
	}
	$results = mysql_fetch_assoc($sth); 

	$sth = mysql_query("SELECT `response`, `candidateID` FROM csc342.answers WHERE `questionID` = " . $id . ";");
	if($sth != false){
		$results["answers"] = array();
		while($r = mysql_fetch_assoc($sth)){
			array_push($results["answers"], $r);
		}
	}	
	print json_encode($results);
	die();
}

function questionsListAll($link){
        $sth1 = mysql_query("SELECT * FROM csc342.questions WHERE 1;", $link);
        if($sth1 == false){
		http_response_code(404);
		die();
	}
	$rows = array();
        while($q = mysql_fetch_assoc($sth1)) {
		$sth = mysql_query("SELECT `response`, `candidateID` FROM csc342.answers WHERE `questionID` = " . $q["id"] . ";");
		if($sth != false){
                	$q["answers"] = array();
                	while($r = mysql_fetch_assoc($sth)){
                	        array_push($q["answers"], $r);
               		}
        	}
		array_push($rows, $q);
        }

        print json_encode($rows);
	die();
}

function questionSearch($link, $params){
	$sql = "SELECT * FROM csc342.questions WHERE";
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

function answerSearch($link, $params){
	$sql = "SELECT * FROM csc342.answers WHERE";
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

function addQuestion($link, $data){
	if(!is_array($data)){
		http_response_code(400); //NOT ACCEPTABLE
		die();
	}
	if(!$data["question"]){
		http_response_code(400);
		die();
	}

$sql = "INSERT INTO csc342.questions (`question`) VALUES ('" . $data["question"] . "');";

	$sth = mysql_query($sql, $link);
        if($sth == false){
               http_response_code(400);
               die();
        }
	http_response_code(201);
	die();
}

function addAnswer($link, $data){
	if(!is_array($data)){
		http_response_code(400); //NOT ACCEPTABLE
		die();
	}
	if(!$data["questionID"]){
		http_response_code(400);
		die();
	}
	if(!$data["candidateID"]){
		http_response_code(400);
		die();
	}
	if(!$data["response"]){
		http_response_code(400);
		die();
	}

$sql = "INSERT INTO csc342.answers (`questionID`, `candidateID`, `response`) VALUES ('" . $data["questionID"] . "', '" . $data["candidateID"] . "', '" . $data["response"] . "');";

	$sth = mysql_query($sql, $link);
        if($sth == false){
               http_response_code(400);
               die();
        }
	http_response_code(201);
	die();
}

function updateAnswer($link, $data){
	if(!is_array($data)){
		http_response_code(400); //NOT ACCEPTABLE
		die();
	}
	if(!$data["questionID"]){
		http_response_code(400);
		die();
	}
	if(!$data["candidateID"]){
		http_response_code(400);
		die();
	}
	if(!$data["response"]){
		http_response_code(400);
		die();
	}

$sql = "UPDATE csc342.answers SET `response` = '" . $data["response"] . "' WHERE `questionID` = " . $data["questionID"] . " AND `candidateID` = " . $data["candidateID"] . ");";

	$sth = mysql_query($sql, $link);
        if($sth == false){
               http_response_code(400);
               die();
        }
	die();
}

function deleteQuestion($link, $data){
	$sql = "DELETE from csc342.questions WHERE `id` = ";

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
	
        $sql = "DELETE from csc342.answers WHERE `questionID` = ";

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
