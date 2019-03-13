window.addEventListener("load", main, false);


function main(){
	var updateB = document.getElementById("updateButton");

	updateB.addEventListener("click", updateHomePage);  
}

function updateHomePage(){
	var bandNameText = document.getElementById("bandName").innerHTML;
	var welcomeText = document.getElementById("wTA").value;
	var youtubeLink = document.getElementById("video").src;
	var imgSrc = document.getElementById("imgId").src;

	var divContainer = document.getElementById("wrapperDivId");
	divContainer.innerHTML = "";

	var formDivContainer = document.createElement("div");
	formDivContainer.setAttribute("class", "formDivContainer");
	formDivContainer.setAttribute("style", "margin:2% 35% 2% 35%;");

	//create form
	var form = document.createElement("form");
	form.setAttribute('method', 'post');
	form.setAttribute('action', 'home_page_admin.php');


	var bandNameInput = document.createElement("input");

	bandNameInput.setAttribute("type", "text");
	bandNameInput.setAttribute("name", "bandNameText");
	bandNameInput.setAttribute("id", "bandNameText");
	bandNameInput.setAttribute("value", bandNameText);

	//add the other elements
	
	var labelBN = document.createElement("label");
	labelBN.setAttribute('for', 'bandNameText');
	labelBN.innerHTML = 'Band Name: ';

	form.appendChild(labelBN);
	form.appendChild(bandNameInput);

	formDivContainer.appendChild(form);
	divContainer.appendChild(formDivContainer);
}
