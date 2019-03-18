window.addEventListener("load", main, false);


function main(){
	var updateB = document.getElementById("updateButton");

	updateB.addEventListener("click", createForm);  
}

function createForm(){
	var bandNameText = document.getElementById("bandName").innerHTML;
	var welcomeText = document.getElementById("wTA").value;
	var youtubeLink = document.getElementById("video").src;
	var imgSrc = document.getElementById("imgId").src;

	var divContainer = document.getElementById("wrapperDivId");
	divContainer.innerHTML = "";

	var formDivContainer = document.createElement("div");
	formDivContainer.setAttribute("class", "formDivContainer");
	formDivContainer.setAttribute("style", "margin:2% 15% 2% 15%;");

	var table = document.createElement("table");
	table.setAttribute('style', 'border-spacing: 0 10px; border:none;');


	var form = document.createElement("form");
	form.setAttribute('method', 'post');
	form.setAttribute('action', 'home_page_admin.php');
	form.setAttribute('enctype', 'multipart/form-data');


	var photoMaxSize = document.createElement("input");
	photoMaxSize.setAttribute('name', 'MAX_FILE_SIZE');
	photoMaxSize.setAttribute('type', 'hidden');
	photoMaxSize.setAttribute('value', '2097152'); //2 MB

	var bandNameInput = document.createElement("input");

	bandNameInput.setAttribute("type", "text");
	bandNameInput.setAttribute("name", "bandNameText");
	bandNameInput.setAttribute("id", "bandNameText");
	bandNameInput.setAttribute("value", bandNameText);
	bandNameInput.setAttribute('style', 'width:100%; box-sizing:border-box;');

	//add the other elements
	
	var labelBN = document.createElement("label");
	labelBN.setAttribute('for', 'bandNameText');
	labelBN.innerHTML = 'Band Name ';

	addTableRow(table, labelBN, bandNameInput);

	var wlcmTxtTA = document.createElement("textarea");
	wlcmTxtTA.setAttribute('id','wlcmTxtArea');
	wlcmTxtTA.setAttribute('name','wlcmTxtArea');
	wlcmTxtTA.setAttribute('rows','15');
	wlcmTxtTA.setAttribute('cols','40');
	wlcmTxtTA.innerHTML =  welcomeText;

	var labelTA = document.createElement('label');
	labelTA.innerHTML = 'Welcome text';

	addTableRow(table, labelTA, wlcmTxtTA);

	var youtubeLnkInput = document.createElement('input');

	youtubeLnkInput.setAttribute("type", "text");
	youtubeLnkInput.setAttribute("name", "youtubeLnkText");
	youtubeLnkInput.setAttribute("id", "youtubeLnkText");
	youtubeLnkInput.setAttribute("value", youtubeLink);
	youtubeLnkInput.setAttribute('style', 'width:100%; box-sizing:border-box;');

	//add the other elements
	var labelYT = document.createElement("label");
	labelYT.innerHTML = 'Youtube link';

	addTableRow(table, labelYT, youtubeLnkInput);	

	var labelImg = document.createElement('label');
	labelImg.innerHTML = 'Upload new image';	

	var imgInput = document.createElement('input');
	imgInput.setAttribute('type', 'file');
	imgInput.setAttribute('name', 'imgInput');
	imgInput.setAttribute('id', 'imgInput');
	imgInput.setAttribute('accept', 'image/*');

	addTableRow(table, labelImg, imgInput);	

	var submitBtn = document.createElement('input');
	submitBtn.setAttribute('type','submit');
	submitBtn.setAttribute('name','submitBtn');
	submitBtn.setAttribute('value','Submit');

	var emptyLabel = document.createElement('label');	

	addTableRow(table, emptyLabel, submitBtn);

	form.appendChild(table);
	formDivContainer.appendChild(form);
	divContainer.appendChild(formDivContainer);
}

function addTableRow(table, label, input){
    var r = table.insertRow();
	var c1 =	r.insertCell(0);
	var c2 = r.insertCell(1);

	c1.appendChild(label);
	c2.appendChild(input);

}
