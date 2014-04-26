<?php
/******************************************************************************/
/*
/*      File: candidates.php
/*      Author: Samuel Fink
/*
/*      Description:
/*      This file conatins processing functions for requests relating to
/*      the candidates table of database, basic connectivity to the DB, 
/*	and colation of candidate information for bulk request of data.
/*
/*      TODO in this file:
/*        implment better error handling
/*        sanitize inputs
/*        improve documentation
/*        add more robust handling of incomplete/overfilled requests
/*	  add more information to the http headers(content-type, etc...)
/*	  add article text search
/*	  add article delete
/*	  add more appropriate response code on update,insert,etc...
/*	
/******************************************************************************/

function http_response_code($statusCode){
	header(':', true, $statusCode);
}

function connectDB(){
	$link = mysql_connect('localhost', 'csc342', 'csc342sql');
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	return $link;
}

function closeDB($link){
	mysql_close($link);
} 

function candidateByID($link, $id){
	$sth = mysql_query("SELECT * FROM csc342.candidates WHERE `id` = " . $id, $link);
	if($sth == false){
                http_response_code(404);
       		die();
	}
	$results = mysql_fetch_assoc($sth); 

	//Do add articles to result
	$sth = mysql_query("SELECT * FROM csc342._c" . $results["id"] . "articles WHERE 1;");
	if($sth != false){
		$results["articles"] = array();
		while($r = mysql_fetch_assoc($sth)){
			array_push($results["articles"], $r["link"]);
		}
	}

	//Do add questions
	$sth = mysql_query("SELECT * FROM csc342.answers WHERE `candidateID` = " . $id . ";");
	if($sth != false){
		$results["QandA"] = array();
		while($r = mysql_fetch_assoc($sth)){
			$QnA["answer"] = $r["response"];
			$ans = mysql_query("SELECT * FROM csc342.questions WHERE `id` = " . $r["questionID"] . ";");
			
			$tmp = mysql_fetch_assoc($ans);
			$QnA["question"] = $tmp["question"];
			array_push($results["QandA"], $QnA);
		}
	}	

	print json_encode($results);
	die();
}

function candidateListAll($link){
        $sth = mysql_query("SELECT `firstName`, `lastName`, `party`, `position`, `shortDesc`, `img` FROM csc342.candidates WHERE 1", $link);
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

function candidateSearch($link, $params){
	$sql = "SELECT `id`, `firstName`, `lastName`, `party`, `position`, `shortDesc`, `img` FROM csc342.candidates WHERE";
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

function candidateSearchAny($link, $str){
	$sql = "SELECT `id`, `firstName`, `lastName`, `party`, `position`, `shortDesc`, `img` FROM csc342.candidates WHERE";
	if($str){
		$params = explode(' ', $str);
		foreach ($params as $value) {
			$sql = $sql . " (";
			foreach (explode(' ', 'firstName lastName party shortDesc longDesc') as $key){
				$sql = $sql . " (`" . $key . "` LIKE '%" . $value . "%') OR";
			}
			$sql = substr($sql, 0 , -2);
			$sql = $sql  . ") AND";
		}
		$sql = substr($sql, 0 , -3) . ";";
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

function addCandidate($link, $data){
	if(!is_array($data)){
		http_response_code(400); //NOT ACCEPTABLE
		die();
	}
	if(!$data["firstName"]){
		http_response_code(400);
		die();
	}
	if(!$data["lastName"]){
		http_response_code(400);
		die();
	}
	if(!$data["party"]){
		http_response_code(400);
		die();
	}
	if(!$data["position"]){
		http_response_code(400);
		die();
	}
	if(!$data["shortDesc"]){
		http_response_code(400);
		die();
	}

$sql = "INSERT INTO `csc342`.`candidates` (`id`, `firstName`, `lastName`, `party`, `position`, `shortDesc`) VALUES (NULL, '" . $data["firstName"] . "', '" . $data["lastName"] . "', '" . $data["party"] . "', '" . $data["position"] . "', '" . $data["shortDesc"] . "');";

	$sth = mysql_query($sql, $link);
        if($sth == false){
               http_response_code(400);
               die();
        }
	http_response_code(201);
	die();
}

function updateCandidate($link, $data){
	 if(!is_array($data)){
                http_response_code(400); //NOT ACCEPTABLE
                die();
        }
	 if(!$data["id"]){
                http_response_code(400);
                die();
        }
	$sql = "UPDATE `candidates` SET ";
        foreach ($data as $key => $value) {
		if($key != "id")
                	$sql = $sql . " `" . $key . "` = '" . $value . "',";
        }
	$sql = substr($sql, 0, -1); //strip trailing comma
	$sql = $sql . " WHERE `candidates`.`id` = " . $data["id"] . ";";        
	
	$sth = mysql_query($sql, $link);
        if($sth == false){
               http_response_code(400);
               die();
        }
	http_response_code(200);
	die();
}

function deleteCandidate($link, $data){
	$sql = "DELETE from `csc342`.`candidates` WHERE `candidates`.`id` = ";

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
	http_response_code(200);
	die();
}

function addArticle($link, $data ){
	if(!$data["id"]){
                        http_response_code(400);
                        die();
        }
	if(!$data["link"]){
                        http_response_code(400);
                        die();
        }
	$sql = "CREATE TABLE IF NOT EXISTS csc342._c" . $data["id"] . "articles ( link varchar(512) UNIQUE ) CHARACTER SET ASCII COLLATE ascii_bin;";
	$sth = mysql_query($sql, $link);
        if($sth == false){
               http_response_code(400);
               die();
        }

	$sql = "INSERT INTO csc342._c" . $data["id"] . "articles VALUES ('" . $data["link"] . "');";
	$sth = mysql_query($sql, $link);
        if($sth == false){
               http_response_code(400);
               die();
        }
	http_response_code(201);
	die();
}
?>
