
$("document").ready(function() {

	//hideButtons();
	var header;
	fileByUrlInit();
	//Choosing a file in the list
	$("#filesList li").click(function(){
    hideErrorHeader();
		$("#langues #englishFlag").attr("name", this.id);
		$("#langues #frenchFlag").attr("name", this.id);
		$("#file_nameInput").attr("value", this.id);
		$("#file_languageInput").attr("value", this.lang);
    $("#filesList li").removeClass("selected");
    $(this).addClass("selected");
    showButtons();

    if(this.lang == "fr") {
      $.ajax({url: "actions.php?etat=getFile&fr_file="+this.id, success: function(result){
          tinyMCE.activeEditor.setContent(result);
          showButtons();
          header = getOldHeader();
      }});
    }
    if(this.lang == "en") {
      $.ajax({url: "actions.php?etat=getFile&en_file="+this.id, success: function(result){
          tinyMCE.activeEditor.setContent(result);
          showButtons();
          header = getOldHeader();
      }});
    }
	});

	// Get the traducted english file with AJax
    $("#langues #englishFlag").click(function() {
    	$.ajax({url: "actions.php?etat=getFile&en_file="+this.name, success: function(result){
       		if(result == "error1"){
       			tinyMCE.activeEditor.setContent("<p>This file doesn't have its own translation</p>");
       			hideButtons();
       		}
       		else if(result == "") {
       			alert("<p>Select a file in the list please</p>");
       			hideButtons();
       		}
       		else {
       			tinyMCE.activeEditor.setContent(result);
       			showButtons();
       		}
       		$("#file_languageInput").attr("value", "en");
    	}});
    });


    // Get the traducted french file with AJax
    $("#langues #frenchFlag").click(function() {
      $.ajax({url: "actions.php?etat=getFile&fr_file="+this.name, success: function(result){
          if(result == ""){
            tinyMCE.activeEditor.setContent("<p>Choississez un fichier dans la liste s'il vous plaît");
          }
          else {
            tinyMCE.activeEditor.setContent(result);
            showButtons();
          }
          $("#file_languageInput").attr("value", "fr");
      }});
    });


    //Delete the file that has been choosed
    $("#deleteFile_Button").click(function() {
    	var file = $("#file_nameInput").val();
  		var language = $("#file_languageInput").val();
  		if(language == "fr") {
			$.ajax({url: "actions.php?delete=true&file="+file, success: function(result){
       			alert(result);
    		}});
	   	}	    				
    	else {
    		$.ajax({url: "actions.php?delete=true&en_file="+file, success: function(result){
       			alert(result);
    		}});
    	}
    });

    // Delete both files
    $("#deleteFile_Button_2").click(function() {
    	var file = $("#file_nameInput").val();
		$.ajax({url: "actions.php?delete=both&file="+file, success: function(result){
       		tinyMCE.activeEditor.setContent(result);
       	}});	
    });
    

    // Add file function
    $("#add_file img").click(function(){
      $(this).hide();
      $("#add_file_sp").show();
      var lang = $("#lang").val();
      $.ajax({url: "actions.php?create", success: function(result1){
      $("#file_nameInput").attr("value", result1);
      $("#file_languageInput").attr("value", "fr");
      $("#langues #englishFlag").attr("name", result1);
      $("#langues #frenchFlag").attr("name", result1);
      if(lang == "fr") {
        $.ajax({url: "actions.php?etat=getFile&fr_file="+result1, success: function(result2){
           tinyMCE.activeEditor.setContent(result2);
           $("#add_file_sp").hide();
           $("#add_file img").show();
          }});
      } else {
        $.ajax({url: "actions.php?etat=getFile&en_file="+result1, success: function(result2){
            tinyMCE.activeEditor.setContent(result2);
            $("#add_file_sp").hide();
            $("#add_file img").show();
        }});
      }
      showButtons();
      }});
    	
    });
    
    // Compile all button on click
    $("#compile_all img").click(function(){
      $(this).hide();
      $("#compile_all_sp").show();
      $.ajax({url: "actions.php?compile", success: function(result){
          $("#compile_all img").show();
          $("#compile_all_sp").hide();
          alert(result);
      }});
    });

    // On click det url to redirect to
    $("#see_file").click(function(){
      var t = $("#file_nameInput").val();
      var l = $("#file_languageInput").val();
      $.ajax({url: "actions.php?redirect="+t+"&l="+l, success: function(result){
          if(result != "Error Juanpi#3")
            window.open(result);
          else
            alert("Sélectionnez d'abord un fichier s'il vous plaît");
      }});
    });

    $("#header_file").click(function(){
      var content = header + tinyMCE.activeEditor.getContent();
      if(content == "undefined")
        alert("Select a file first");
      else {
        tinyMCE.activeEditor.setContent(content);
      }   
    });



});

// Header
function getOldHeader() {
  var tinyMCEContent = tinyMCE.activeEditor.getContent();
  var split_str = tinyMCEContent.split("]",1);
  return split_str[0]+"]";
}
// Header file verification
function verifTagDocument() {
	var tinyMCEContent = tinyMCE.activeEditor.getContent();
	var split_str = tinyMCEContent.split("|",3);
	var tag1verif = false;
	var tag2verif = false;
	var tag3verif = false;
	var subSplit_str;

	if(split_str[2] != undefined){
		subSplit_str = split_str[2].split("]");
	}
	else 
		subSplit_str = "not valid";


	if(split_str[0].indexOf("[") == 0 && split_str[0] != "" && split_str[0] != "[" && split_str[0].indexOf(" ") == -1)
		tag1verif = true;
	if (split_str[1] != "")
		tag2verif = true;
	if (subSplit_str[0] != "" && subSplit_str != "not valid" && subSplit_str[1] != undefined)
		tag3verif = true;

	return tag1verif && tag2verif && tag3verif;
}

function hideButtons() {
	$("#saveButton").hide();
	$("#deleteFile_Button").hide();
	$("#deleteFile_Button_2").hide();
}

function showButtons() {
	$("#saveButton").show();
	$("#deleteFile_Button").show();
	$("#deleteFile_Button_2").show();
}

function showErrorHeader() {
	var divError = $("#msgError");
	divError.html("L'entête du document n'est pas valide, merci de respecter le format suivant: [url(sans espaces)|titre|menu]");
}

function hideErrorHeader() {
	var divError = $("#msgError");
	divError.html("");
}

function fileByUrlInit() {
  var file;
  var l = document.location.search;
  var s_1 = l.split("=");
  if(s_1[0] == "?l" && s_1[1] == "en&f") {
    file = s_1[2].split("&");
    $("#langues #englishFlag").attr("name", file[0]);
    $("#langues #frenchFlag").attr("name", file[0]);
    $("#file_nameInput").attr("value", file[0]);
    $("#file_languageInput").attr("value", "en");
  }
  else if(s_1[0] == "?f") {
    file = s_1[1].split("&");
    $("#langues #englishFlag").attr("name", file[0]);
    $("#langues #frenchFlag").attr("name", file[0]);
    $("#file_nameInput").attr("value", file[0]);
    $("#file_languageInput").attr("value", "fr");
  }
  else
    hideButtons();
}