<?php  
include_once "functions.php";
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Admin</title>
  <link rel="stylesheet" href="css/style-admin.css">
  <script type="text/javascript" src="jquery/jquery.js"></script>
  <script type="text/javascript" src="jquery/jquery-ui.js"></script>
  <script type="text/javascript" src="actions.js"></script>
  <script src="tinymce/js/tinymce/tinymce.min.js"></script>
  <script>tinymce.init({
    entity_encoding: "raw",
    selector: "textarea",
    theme: "modern",
    plugins: [
    "code advlist autolink lists link image charmap print preview hr anchor pagebreak",
    "searchreplace wordcount visualblocks visualchars code fullscreen",
    "insertdatetime media nonbreaking save table contextmenu directionality",
    "emoticons template paste textcolor colorpicker textpattern imagetools filemanager mention"
    ],
    toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image ",
    toolbar2: "print preview media | alignleft aligncenter alignright align justify | code | charmap",
    image_advtab: true,
    force_br_newlines : false,
    force_p_newlines : false,
    relative_urls: true,
    forced_root_block : '',
    templates: [
    {title: 'Test template 1', content: 'Test 1'},
    {title: 'Test template 2', content: 'Test 2'}
    ],
    mentions: {
    source: [
        { name: 'Lucas Porcelli' }, 
        { name: 'Juanpi Reddish' },
        { name: 'Rudy Buckler' },
        { name: 'Toto Whelan' },
        { name: 'Toto Wheli' },
        { name: 'Toto Whn' },
        { name: 'Toto Whn' },
		{ name: 'Rudy TALER' }
    ]
},
    setup: function(editor) {
      editor.on('change', function(e) {
        if(!verifTagDocument()){
          hideButtons();
          showErrorHeader();
          console.log("modified");
        }
        else {
          showButtons();
          hideErrorHeader();
          console.log("modified");
        }
      });
      editor.on('keyup', function(e) {
        if(!verifTagDocument()){
          hideButtons();
          showErrorHeader();
        }
        else {
          showButtons();
          hideErrorHeader();
        }
      }); 
      editor.on('blur', function(e) {
        if(!verifTagDocument()){
          hideButtons();
          showErrorHeader();
        }
        else {
          showButtons();
          hideErrorHeader();
        }
      });
      editor.on("click", function() {
        if(!verifTagDocument()){
          hideButtons();
          showErrorHeader();
        }
        else {
          showButtons();
          hideErrorHeader();
        }
      });
    }
  });
</script>
</head>
<body>
  <header id="header">
    <div id="WelcomeMessage">Bonjour Admin !</div>
  </header>
  <section id="principalSection">
    <div id="filesList">
      <div id="fr_filelist" class="fileList_opt"><a href="./index.php">French files</a></div>
      <div id="en_filelist" class="fileList_opt" ><a href="?l=en">English files</a></div>
      <?php if(isset($_GET['l']) && $_GET['l'] === "en") ShowEnglishFilesList(); else ShowFilesList(); ?>
    </div>
    <div id="showContainer">
      <div class="error-msg">
        <?php 
        if(isset($_GET['e'])) {
          if($_GET['e'] == 1)
            echo "Error J#1: deux fichiers ne peuvent pas avoir la même url"; 
          else if($_GET['e'] == 2)
            echo "Error J#1: two files can not have the same url header";
        }   
        ?>
        <div class="sccss-message">
          <?php 
          if(isset($_GET['e'])) {
            if($_GET['e'] == 10)
              echo "> Le fichier a bien été enregistré"; 
            else if($_GET['e'] == 11)
              echo "> The file was succesfully saved";
          }   
          ?>
        </div>
      </div>
      <form method="post" id="form_tinyMCE" action="actions.php">
        <div id="editor">
          <div id="tinyMCE">
            <textarea id="file_content" name="file_content">
              <?php 
              if(isset($_GET['f'])) 
                echo GetFile($_GET['f']);
              ?>
            </textarea> 
          </div>
        </div>
        <div id="container_buttons">
          <input id="file_nameInput" type="hidden" name="file_name" />
          <input id="lang" type="hidden" name="lang" value=<?php if(isset($_GET["l"]) && $_GET["l"] == "en") echo "\"en\""; else echo "\"fr\""; ?>/>
          <input id="file_languageInput" type="hidden" name="file_language" />
          <input type="submit" class="button" id="saveButton" name="save" value="Sauvegarder" />
          <input type="button" class="button_supp" id="deleteFile_Button" name="supprimer" value="Supprimer ce fichier">
          <input type="button" class="button_supp" id="deleteFile_Button_2" name="supprimer" value="Supprimer les deux fichiers">
          <div id="msgError"></div>
        </div>
        <div id="container_buttons_2">
          <div id="add_file" class="button_p2">
            <img src="ressources/add.png" alt="add_buton" width="50" height="50" />
            <div class="spinner" id="add_file_sp"></div>
            <p class="btn_msg">Create file</p>
          </div>
          <div id="compile_all" class="button_p2">
            <img src="ressources/compile_img.png" alt="compile_buton" width="50" height="50" />
            <div class="spinner" id="compile_all_sp"></div>
            <p class="btn_msg">Compile all files</p>
          </div>
          <div id="see_file" class="button_p2">
            <img src="ressources/see.png" alt="compile_buton" width="50" height="50" />
            <p class="btn_msg">Watch file</p>
          </div>
          <div id="header_file" class="button_p2">
            <img src="ressources/header.png" alt="header_buton" width="50" height="50" />
            <div class="spinner" id="add_file_sp"></div>
            <p class="btn_msg">Rewrite header</p>
          </div>
          <div id="langues">
            <div id="msg_lang">Langue:</div>
            <img id="frenchFlag" src="ressources/france-flag.svg.png" alt="Photo drapeau francais"  height="35" width="50"/>
            <img id="englishFlag" src="ressources/uk-flag.gif" alt="Photo drapeau anglais"  height="35" width="50"/>
          </div>
        </div>
      </form>
    </div>
  </section>
</body>
</html>