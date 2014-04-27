/** Callback for each item in result array
*   Inserts new div containing the candidate button 
*   at the end of the candidate_btns item.
*   New divs have ID of candidate_btn_c(id) and class candidate_btn
*/
function addButton(candidateObj) {
	var newdiv = document.createElement("div");
	newdiv.setAttribute("id", "candidate_btn_c" + candidateObj.id);
	newdiv.setAttribute("class", "candidate_btn");
	
	//TODO: make the output look nicer
	var content = "<h2>Name: " + candidateObj.firstName + " " + candidateObj.lastName + "</h2>\n"; //add the name line
	content = content + "<h2>Candidate for: " + candidateObj.position.name + "</h2>\n"; // add position text
	content = content + "<p>" + candidateObj.shortDesc + "</p>\n"; //add description
	content = content + '<img src="' + candidateObj.img + '" alt="candidateimage>\n'; 
	
	$("#candidate_btns").append(newdiv,$("#candidate_btns")); //push the button div
	
	var tst = $("#candidate_btn_c" + candidateObj.id);
	tst.html(content);
	tst.click(candidateObj, showCandidate);
}

function putCandidateInfo(candidateObj){
	//TODO: make the output look nicer
	var content = "<h2>Name: " + candidateObj.firstName + " " + candidateObj.lastName + "</h2>\n"; //add the name line
	content = content + "<h3>Candidate for: " + candidateObj.position.name + "</h3>\n"; // add position text
	content = content + '<img src="' + candidateObj.img + '" alt="candidateimage">\n'; 
	content = content + '<p>Description: ' + candidateObj.longDesc + '</p>\n'; 
	content = content + '<p>Articles:</p>\n<ul>\n';
	
	for(var i = 0; i < candidateObj.articles.length; i++) {
		content = content + '<li><a href="' + candidateObj.articles[i] + '">' + candidateObj.articles[i] + '</a></li>\n';
	}	
	content = content + '</ul>\n';
	
	content = content + '<h3>Q and A</h3>';
	for(var i = 0; i < candidateObj.QandA.length; i++) {
		content = content + '<p>Question: ' + candidateObj.QandA[i].question + '</p><p>Answer: ' + candidateObj.articles[i] + '</p>\n';
	}	
	
	var tst = $("#candidateData");
	tst.html(content);
}

function showCandidate(obj) {
	$("#candidateResults").html('<div id="candidate_btns"></div>');
	$("#positionResults").html('<div id="position_btns"></div>');
	$("#candidateInfo").html('<div id="candidateData"></div>');
	var newurl = '/rest/candidates/' + obj.data.id;
	$.ajax({
		contentType: 'text/plain',
		data: '',
		dataType: 'text/plain',
		success: function(data){
			//not called
		},
		error: function(data){
			if(data.status >= 400){
				alert("Error " + data.status);
			}
			putCandidateInfo(jQuery.parseJSON(data.responseText));
		},
		processData: false,
		type: 'GET',
		url: newurl
	});
}


/** Renders a candidate array to a series of buttons
*   Overwrites the contents of the DOM object location and replaces it
*   with the buttons created
*/
function renderCandidateResults(resultArray) {
	$("#candidateResults").html('<div id="candidate_btns"></div>');
	$("#positionResults").html('<div id="position_btns"></div>');
	$("#candidateInfo").html('<div id="candidateData"></div>');
	resultArray.forEach(addButton);
}

/** Renders a candidate array to a series of buttons
*   adds results to current button display
*/
function renderCandidateResultsAppend(resultArray) {
	resultArray.forEach(addButton);
}

function posClicked(param) {
	$("#candidateResults").html('<div id="candidate_btns"></div>');
	$("#positionResults").html('<div id="position_btns"></div>');
	$("#candidateInfo").html('<div id="candidateData"></div>');
	//addPosition(param.data);
	$.ajax({
		contentType: 'text/plain',
		data: '{"position":"' + param.data.id + '"}',
		dataType: 'text/plain',
		success: function(data){
			//not used
		},
		error: function(data){
			if(data.status >= 400){
				alert("Error " + data.status);
			}
			renderCandidateResultsAppend(jQuery.parseJSON(data.responseText));
		},
		processData: false,
		type: 'SEARCH',
		url: '/rest/candidates'
	});
}

/** Callback for each item in result array
*   Inserts new div containing the candidate button 
*   at the end of the candidate_btns item.
*   New divs have ID of candidate_btn_c(id) and class candidate_btn
*/
function addPosition(obj) {
	var newdiv = document.createElement("div");
	newdiv.setAttribute("id", "position_btn_" + obj.id);
	newdiv.setAttribute("class", "position_btn");
	
	//TODO: make the output look nicer
	var content = "<h2>Position name: " + obj.name + "</h2>\n"; //add the name line
	content = content + "<p>Description: " + obj.description + "</p>\n"; // add position text
	content = content + "<p>Position level: ";
	if(obj.level == 1) {
			content = content + "Federal</p>\n"; 
	}
	if(obj.level == 2) {
			content = content + "State</p>\n"; 
	}
	if(obj.level == 3) {
			content = content + "Local</p>\n"; 
	}

	$("#position_btns").append(newdiv,$("#position_btns")); //push the button div
	
	var tst = $("#position_btn_" + obj.id);
	tst.html(content);
	tst.click(obj, posClicked);
}

/** Renders an array of position information
*
*/
function renderPositions(resultArray) {
	jQuery.each(parsed,addPosition);
	resultsArray.forEach(addPosition);
}

function listAllCandidates(){
	$("#candidateResults").html('<div id="candidate_btns"></div>');
	$("#positionResults").html('<div id="position_btns"></div>');
	$.ajax({
		contentType: 'text/plain',
		data: null,
		dataType: 'text/plain',
		success: function(data){
			renderCandidateResults(jQuery.parseJSON(data.responseText));
		},
		error: function(data){
			if(data.status >= 400){
				alert("Error " + data.status);
			}
			renderCandidateResults(jQuery.parseJSON(data.responseText));
		},
		processData: false,
		type: 'GET',
		url: '/rest/candidates'
	});
};

function listAllPositions() {
	searchPositions("");
}

function searchCandidates(searchString){
	$("#candidateResults").html('<div id="candidate_btns"></div>');
	$("#positionResults").html('<div id="position_btns"></div>');
	$("#candidateInfo").html('<div id="candidateData"></div>');
	$.ajax({
		contentType: 'text/plain',
		data: '{"str":"' + searchString + '"}',
		dataType: 'text/plain',
		success: function(data){
			renderCandidateResults(jQuery.parseJSON(data.responseText));
		},
		error: function(data){
			if(data.status >= 400){
				alert("Error " + data.status);
			}
			renderCandidateResults(jQuery.parseJSON(data.responseText));
		},
		processData: false,
		type: 'SEARCH',
		url: '/rest/candidates'
	});
};

function searchPositions(strSrch){
	$("#candidateResults").html('<div id="candidate_btns"></div>');
	$("#positionResults").html('<div id="position_btns"></div>');
	$("#candidateInfo").html('<div id="candidateData"></div>');
	$.ajax({
		contentType: 'text/plain',
		data: '{"name":"' + strSrch + '"}',
		dataType: 'text/plain',
		success: function(data){
			//not called
		},
		error: function(data){
			if(data.status >= 400){
				alert("Error " + data.status);
			}
			var parsed  = jQuery.parseJSON(data.responseText);
			jQuery.each(parsed,_processPositions);
		},
		processData: false,
		type: 'SEARCH',
		url: '/rest/positions'
	});
};

function _processPositions(index, posObj){
	addPosition(posObj);
};