<?php
/******************************************************************************/
/*
/*	File: rest.php
/*	Author: Samuel Fink
/*	
/*	Description:
/*	Incoming HTTP requests to the Apache2 server with a tld of /rest/
/*	are redirected to this file using a mod_rewrite rule in the /vae/www
/*	.htaccess file
/*
/*	This file extracts information from the incoming requests, performs
/*	minimal error checking, and routs the request to the correct handler 
/*	function. If no handler function exists, a 404 status is set and no 
/* 	content is returned.
/*
/*	TODO in this file:
/*	  implment better error handling 
/*	  sanitize inputs
/*	  improve documentation
/*
/******************************************************************************/

$uri = $_SERVER['REQUEST_URI'];
$request_body = @file_get_contents('php://input');
$method = $_SERVER['REQUEST_METHOD'];
//echo "request_uri: " . $uri;
//echo "\n<br>\nmethod: " . $method;
//echo "\n<br>\n request body: " . $request_body;;
//echo "\n<br>\n response:\n<br>\n";
require "candidates.php";
require "QandA.php";
require "positions.php";
$link = connectDB();
if($method == "GET"){
	if($uri == "/rest/candidates" || $uri == "/rest/candidates/"){
		candidateListAll($link);
	}
	$matcher = "@^\\/rest\\/candidates\\/([0-9]+)$@";
	if(preg_match($matcher, $uri , $matches)){
		candidateByID($link, $matches[1]);
	}
        if($uri == "/rest/questions" || $uri == "/rest/questions/" ){
                questionsListAll($link);
        }
        $matcher = "@^\\/rest\\/questions\\/([0-9]+)$@";
        if(preg_match($matcher, $uri , $matches)){
                questionByID($link, $matches[1]);
        }
        if($uri == "/rest/positions" || $uri == "/rest/positions/" ){
                 positionsListAll($link);
        }
        $matcher = "@^\\/rest\\/positions\\/([0-9]+)$@";
        if(preg_match($matcher, $uri , $matches)){
                positionByID($link, $matches[1]);
        }
}
else if ($method == "POST"){
	if($uri == "/rest/candidates"){
		if(!$request_body){
                        http_response_code(400);
                        die();
                }
		$parameters = json_decode($request_body, TRUE);
		updateCandidate($link, $parameters);
	}
	if($uri == "/rest/positions"){
		if(!$request_body){
                        http_response_code(400);
                        die();
                }
		$parameters = json_decode($request_body, TRUE);
		updatePosition($link, $parameters);
	}
	if($uri == "/rest/answers"){
		if(!$request_body){
                        http_response_code(400);
                        die();
                }
		$parameters = json_decode($request_body, TRUE);
		updateAnswer($link, $parameters);
	}
}
else if ($method == "SEARCH"){
	if($uri == "/rest/candidates"){
		if(!$request_body){
			http_response_code(400);
			die();
		}
		$parameters = json_decode($request_body, TRUE);
		if(array_key_exists("str",$parameters)){
			candidateSearchAny($link, trim($parameters["str"]));
		}else {
			candidateSearch($link, $parameters);
		}
	}
	if($uri == "/rest/positions"){
		if(!$request_body){
			http_response_code(400);
			die();
		}
		$parameters = json_decode($request_body, TRUE);
		positionSearch($link, $parameters);
	}
	if($uri == "/rest/questions"){
		if(!$request_body){
			http_response_code(400);
			die();
		}
		$parameters = json_decode($request_body, TRUE);
		questionSearch($link, $parameters);
	}
	if($uri == "/rest/answers"){
		if(!$request_body){
			http_response_code(400);
			die();
		}
		$parameters = json_decode($request_body, TRUE);
		answerSearch($link, $parameters);
	}
}
else if ($method == "PUT"){
	if($uri == "/rest/candidates"){
		if(!$request_body){
			http_response_code(400);
			die();
		}
		$parameters = json_decode($request_body, TRUE);
		addCandidate($link, $parameters);
	}
	if($uri == "/rest/questions"){
		if(!$request_body){
			http_response_code(400);
			die();
		}
		$parameters = json_decode($request_body, TRUE);
		addQuestion($link, $parameters);
	}
	if($uri == "/rest/positions"){
		if(!$request_body){
			http_response_code(400);
			die();
		}
		$parameters = json_decode($request_body, TRUE);
		addPosition($link, $parameters);
	}
	if($uri == "/rest/answers"){
		if(!$request_body){
			http_response_code(400);
			die();
		}
		$parameters = json_decode($request_body, TRUE);
		addAnswer($link, $parameters);
	}
	if($uri == "/rest/articles"){
		if(!$request_body){
                        http_response_code(400);
                        die();
                }
                $parameters = json_decode($request_body, TRUE);
                addArticle($link, $parameters);
	}
}
else if ($method == "DELETE"){
	if($uri == "/rest/candidates"){
                if(!$request_body){
                        http_response_code(400);
                        die();
                }
                if(ctype_digit($request_body)){
			deleteCandidate($link, intval($request_body));
		} else {
			$parameters = json_decode($request_body, TRUE);
        	        deleteCandidate($link, $parameters);
		}
	}
	if($uri == "/rest/positions"){
                if(!$request_body){
                        http_response_code(400);
                        die();
                }
                if(ctype_digit($request_body)){
			deletePosition($link, intval($request_body));
		} else {
			$parameters = json_decode($request_body, TRUE);
        	        deletePosition($link, $parameters);
		}
	}
	if($uri == "/rest/questions"){
                if(!$request_body){
                        http_response_code(400);
                        die();
                }
                if(ctype_digit($request_body)){
			deleteQuestion($link, intval($request_body));
		} else {
			$parameters = json_decode($request_body, TRUE);
        	        deleteQuestion($link, $parameters);
		}
	}
}
http_response_code(404);
?>

