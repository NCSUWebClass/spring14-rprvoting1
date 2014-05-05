/** Callback for each item in result array
*   Inserts new div containing the candidate button 
*   at the end of the candidate_btns item.
*   New divs have ID of candidate_btn_c(id) and class candidate_btn
*/
function addButton(candidateObj) {
	var newdiv = document.createElement("div");
	newdiv.setAttribute("id", "candidate_btn_c" + candidateObj.id);
	newdiv.setAttribute("class", "candidate_btn profile_link profile");
	
	var content = '<div class="image">';
	//content = content + '<object data="PHOTO.jpg" type="image/jpg">\n';
	if(!candidateObj.img){
		candidateObj.img = "PHOTO.jpg";
	}
	content = content + '<img src="' + candidateObj.img + '" class="pic" alt="candidateimage" />\n';
	//content = content + "</object><br />";
	content = content + '<p class="caption">' + candidateObj.firstName + " " + candidateObj.lastName + "</p>\n"; //add the name line
	content = content + "</div>";
	content = content + '<div class="demo">';
	content = content + "<h4>" + candidateObj.position.name + "</h4>\n"; // add position text
	content = content + '<p class="text">' + candidateObj.shortDesc + '</p>\n'; //add description 
	content = content + "</div>"

	$("#candidate_btns").append(newdiv,$("#candidate_btns")); //push the button div
	
	var tst = $("#candidate_btn_c" + candidateObj.id);
	tst.html(content);
	tst.click(candidateObj, showCandidate);
}

function putCandidateInfo(candidateObj){
	//TODO: make the output look nicer

	var content = '<table class="table1"> \n <tbody> \n <th colspan="2">';
	content = content + candidateObj.firstName + " " + candidateObj.lastName + "</th>\n"; //add the name line	
	content = content + '<tr> \n <td width="200"> \n <div class ="jimprofile"> \n' ;
	content = content + '<img id="pic" src="' + candidateObj.img + '" alt="candidateimage">\n </div> \n </td>';
	content = content + '<td width ="150"> \n <div class ="jimprofile">';
	content = content + '<ul> \n' + candidateObj.position.name +'</li><li></li>\n';
	content = content + '\n <li class ="listy">Description: ' + candidateObj.longDesc +'</li>\n';
	content = content + "</tr></tbody></table>";

	content = content + '<h3>Q and A</h3>';
       
	content = content + '<div id="menu"> \n';
 
	for(var i = 0; i < candidateObj.QandA.length; i++) {
		content = content + '<p class="header">' + candidateObj.QandA[i].question + '</p> \n <div> \n<p>' + candidateObj.QandA[i].answer + '</p> \n </div>';
	}	
	content = content + '</div>';

	content = content + '<p></p><p>Articles:</p>\n<ul>\n <div class="articleclass">';
	
	for(var i = 0; i < candidateObj.articles.length; i++) {
		content = content + '<li><a href="' + candidateObj.articles[i] + '">' + candidateObj.articles[i] + '</a></li>\n';
	}	
	content = content + '</ul>\n</div>';
	

	var tst = $("#candidateData");
	tst.html(content);
      $("#menu").accordion({collapsible: true, active: false});

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
	newdiv.setAttribute("class", "position_btn profile");
	
	
	var content = "<h4>" + obj.name + "</h4>\n"; //add the name line
	content = content + '<p class="text">Description: ' + obj.description + '<br />'; // add position text
	content = content + "Position level: ";
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