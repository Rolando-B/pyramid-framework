<?php
include_once "config.php";
$directory = "../content_files";
$directoryCompiledFiles = "../compiled_files";

function directoryContentFiles() {global $directory; return $directory; }
function directoryCompiledFiles() {global $directoryCompiledFiles; return $directoryCompiledFiles; }

//Get all files in $direcory
//CHANGE global -> function
function GetAllFiles($filesList) {
	$files = Array();
	$compteur = 0;
	foreach ($filesList as $fileName) {
		global $directory;
		$urlFile = $directory."/".$fileName;
		if(is_file($urlFile)) {
			$newFileURL = getFileName($urlFile);
			$newFileName = getFileTitle($urlFile);
			$files[$newFileURL] = $newFileName;
			$compteur++;
		}
	}
	return $files;
}


//Get file name from raw name
function GetFileName($urlFile) {
	$file = fopen($urlFile, "r+");

	$line = fgets($file);

	$firstLine = explode("|",$line);
	$lengthLine = strlen($firstLine[0]);
	$fileName = substr($firstLine[0], 1, $lengthLine);
	fclose($file);

	return $fileName;
}

function GetFileNameContent($line) {
	$firstLine = explode("|",$line);
	$lengthLine = strlen($firstLine[0]);
	$fileName = substr($firstLine[0], 1, $lengthLine);

	return $fileName;
}

function GetFileTitle($urlFile) {
	$file = fopen($urlFile, "r+");

	$line = fgets($file);

	$firstLine = explode("|",$line);
	$lengthLine = strlen($firstLine[1]);
	$fileName = substr($firstLine[1], 0, $lengthLine);
	fclose($file);

	return $fileName;
}

//Show all french files function
function ShowFilesList() {
	$pattern = "-en";
	$altern = 0;
	$filesList = getFrenchNodes(getFilesInDirectory());
	$namedFilesList = getAllFiles($filesList);
	echo "<ul>";

	foreach ($namedFilesList as $file=>$key) {
		if(strpos($file, $pattern) == false)
		 	if($altern == 0) {
			echo "<li id=\"".$file."\" name=\"".$key."\" lang=\"fr\">".$file."</li>";
			$altern++;
			}
			else {
				echo "<li id=\"".$file."\" name=\"".$key."\" lang=\"fr\">".$file."</li>";
				$altern--;
			}
		}
		echo "</ul>";
}

// Show all english files files
function ShowEnglishFilesList() {
	$pattern = ".fr";
	$altern = 0;
	$filesList = getEnglishNodes(getFilesInDirectory());
	$namedFilesList = getAllFiles($filesList);
	echo "<ul>";

	foreach ($namedFilesList as $file=>$key) {
		if(strpos($file, $pattern) == false)
		 	if($altern == 0) {
			echo "<li id=\"".$file."\" name=\"".$key."\" lang=\"en\" >".$file."</li>";
			$altern++;
			}
			else {
				echo "<li id=\"".$file."\" class=\"altern\" name=\"".$key."\" lang=\"en\">".$file."</li>";
				$altern--;
			}
		}
		echo "</ul>";
}

function GetEnglishFileByUrl() {
	$allFiles = getFilesInDirectory();
	$englishContent = "";
	foreach ($allFiles as $file) {
		global $directory;
		$url = $directory."/".$file;
		if(is_file($url) && getFileName($url) == $_GET['en_file']){
			$enFile = convertToEnglishFile($file);
			$url_en = $directory."/".$enFile;

			if(is_file($url_en)) {
				$englishContent = file_get_contents($url_en);
				break;
			}
			else {
				$englishContent = "error1";
			}
		}
	}
	return $englishContent;
}

function GetFrenchFileByUrl() {
	$allFiles = getFilesInDirectory();
	$frenchContent = "";
	foreach ($allFiles as $file) {
		global $directory;
		$url = $directory."/".$file;
		if(is_file($url) && getFileName($url) == $_GET['fr_file']){
			$frFile = convertToFrenchFile($file);
			$url_fr = $directory."/".$frFile;

			if(is_file($url_fr)) {
				$frenchContent = file_get_contents($url_fr);
				break;
			}
			else {
				$frenchContent = "error1";
			}
		}
	}
	return $frenchContent;
}

//Basically change .fr to .en keeping the node/file number
function ConvertToEnglishFile($file) {
	$fileInEnglish = str_replace(".fr", ".en", $file);
	return $fileInEnglish;
}

//Same but english .en to french .fr
function ConvertToFrenchFile($file) {
	$fileInFrench = str_replace(".en", ".fr", $file);
	return $fileInFrench;
}


//Save file with modified content
function SaveFile($fileName, $content) {
	global $directory;
	$directoryToSave = $directory."/".$fileName;
	$result = file_put_contents($directoryToSave, $content);
	if($result != false)
		return true;
	else
		return false;
}


//Compare the tag name to all other to see to what file correspond the name
function FindFile($fileName) {
	$allFiles = getFilesInDirectory();
	foreach ($allFiles as $file) {
		global $directory;
		$url = $directory."/".$file;
		if(is_file($url) && getFileName($url) == $fileName){
			return $file;
			break;
		}
	}
}


//Scandir of all files in direcory
function getFilesInDirectory() {
	global $directory;
	$filesDirectory = scandir($directory);
	return $filesDirectory;
}


//Function delete a file with get and isset verifications
//Long because, code is repeated to delete french node or english node
function DeleteFile() {
	if(isset($_GET["file"]) && $_GET["delete"] === "true") {
		$fileToDelete = $_GET["file"];
		$delete = $_GET["delete"];

		if($delete) {
			global $directory;
			global $directoryCompiledFiles;

			$file = convertToFrenchFile(findFile($fileToDelete));
			$fileURL = $directory."/".$file;
			$fileURL2 = $directoryCompiledFiles."/".getCompiledFile($file);
			if(is_file($fileURL)) {
	     		$result = unlink($fileURL);
	     		if($result){
	     			echo "<p>Le fichier content a bien été supprimé</p>";
	     		}
		    }
		    if(is_file($fileURL2)) {
	     		$result2 = unlink($fileURL2);
	     		$_GET["delete"] = false;
	     		if($result2){
	     			echo "<p>Le fichier compiled a bien été supprimé</p>";
	     		}
		    }
		}
	}

	else if (isset($_GET["en_file"]) && $_GET["delete"] === "true") {
		$fileToDelete = $_GET["en_file"];
		$delete = $_GET["delete"];

		if($delete) {
			global $directory;
			global $directoryCompiledFiles;

			$file = convertToEnglishFile(findFile($fileToDelete));
			$fileURL = $directory."/".$file;
			$fileURL2 = $directoryCompiledFiles."/".getCompiledFile($file);

			if(is_file($fileURL)) {
	     		$result = unlink($fileURL);

	     		if($result){
	     			echo "<p>The content file has been succesfully deleted</p>";
	     		}
	     		if(is_file($fileURL2)) {
	     		$result2 = unlink($fileURL2);
	     		$_GET["delete"] = false;
	     		if($result2){
	     			echo "<p>The compiled file has been succesfully deleted</p>";
	     		}
		    }
		    }
		}
	}

	else if ($_GET["delete"] == "both") {
		DeleteBothFiles($_GET["file"]);
	}

}


//Delete both files

function DeleteBothFiles($fileToDelete) {
	$file_en = convertToEnglishFile(findFile($fileToDelete));
	$file_fr = convertToFrenchFile(findFile($fileToDelete));
	$fileURL_en = directoryContentFiles()."/".$file_en;
	$fileURL_fr = directoryContentFiles()."/".$file_fr;
	$fileURL2_en = directoryCompiledFiles()."/".getCompiledFile($file_en);
	$fileURL2_fr = directoryCompiledFiles()."/".getCompiledFile($file_fr);

	if(is_file($fileURL_en) && is_file($fileURL_fr) ) {
	    $result1 = unlink($fileURL_en);
	    $result2 = unlink($fileURL_fr);

	     if($result1 || $result2){
	     	echo "<p>Both contents files have been succesfully deleted</p>";
	     }
	     	if(is_file($fileURL2_en) || is_file($fileURL2_fr)) {
	     		$result3 = unlink($fileURL2_en);
	     		$result4 = unlink($fileURL2_fr);
	     		$_GET["delete"] = false;
	     	if($result3 || $result4){
	     			echo "<p>Both compiled files have been succesfully deleted</p>";
	     	}
		}
	}
	else
		echo "Could not delete both files, maybe the file traduction does not exist please verify this";
}

//Get only files in french
function GetFrenchNodes($allFiles) {
	$frenchFiles = array();
	$pattern = ".fr.";
	$compter = 0;
	foreach ($allFiles as $file) {
		if(strstr($file, $pattern) != false) {
			$frenchFiles[$compter] = $file;
			$compter++;
		}
	}
	return $frenchFiles;
}


// Get only files in english
function GetEnglishNodes($allFiles) {
	$englishFiles = array();
	$pattern = ".en.";
	$compter = 0;
	foreach ($allFiles as $file) {
		if(strstr($file, $pattern) != false) {
			$englishFiles[$compter] = $file;
			$compter++;
		}
	}
	return $englishFiles;
}

//Efface .raw to get the compiled file name
function getCompiledFile($name) {
	$position = strpos($name, ".raw");
	$compiledFile = substr($name, 0, $position);

	return $compiledFile;
}

//Create files function

function createEnglishFile($url) {
	global $directory;
	$allfiles = getFilesInDirectory();
	$files = getEnglishNodes($allfiles);
	$file_name;
	$index_1 = 0;

	$index_1 = lastFileIndex($files) + 1;
	
	$file_name = "node_".$index_1.".en.php.raw";
	
	$file_path = $directory."/".$file_name;

	$result_1 = fopen($file_path, 'w+');
	if($result_1 == false)
		die("La création du fichier a échoué");
	else {
		$r1 = file_put_contents($file_path, "[".$url."|titre_en|menu_menu] <div class=\"content\">// Ne pas supprimer juste changer");
		if($r1 != false)
			print_r("node_".$index_1.".en créé\n");
		else {
			print_r("<p>Error lors de la création du fichier</p>");
		}
	}
	
}

function createFrenchFile($url) {
	global $directory;
	$allfiles = getFilesInDirectory();
	$files = getFrenchNodes($allfiles);
	$file_name;
	$index_1 = 0;

	$index_1 = lastFileIndex($files) + 1;
	
	$file_name = "node_".$index_1.".fr.php.raw";
	$file_path = $directory."/".$file_name;

	$result_1 = fopen($file_path, 'w+');
	if($result_1 == false)
		die("La création du fichier a échoué");
	else {
		$r1 = file_put_contents($file_path, "[".$url."|titre_fr|menu_menu] <div class=\"content\">// Ne pas supprimer juste changer");
		if($r1 != false)
			print_r("node_".$index_1.".fr créé\n");
		else {
			print_r("<p>Error lors de la création du fichier</p>");
		}
	}
}

function createFile() {
	global $directory;
	$all_files = getFilesInDirectory();
	$file_name_fr;
	$file_name_en;
	$index_1 = 0;

	$index_1 = lastFileIndex($all_files) + 1;
	
	$file_name_fr = "node_".$index_1.".fr.php.raw";
	$file_name_en = "node_".$index_1.".en.php.raw";

	$file_path_fr = $directory."/".$file_name_fr;
	$file_path_en = $directory."/".$file_name_en;

	$result_1 = fopen($file_path_fr, 'w+');
	$result_2 = fopen($file_path_en, 'w+');
	if($result_1 == false || $result_2 == false)
		die("La création du fichier a échoué");
	else {
		$r1 = file_put_contents($file_path_fr, "[node_".$index_1.".fr|titre_fr|menu_menu] <div class=\"content\">// Ne pas supprimer juste changer");
		$r2 = file_put_contents($file_path_en, "[node_".$index_1.".en|title_en|menu_en] <div class=\"content\">// Not delete this line just change it");
		if($r1 != false && $r2 != false)
			print_r("node_".$index_1." créé");
		else {
			print_r("<p>Error lors de la création du fichier</p>");
		}
	}
}


// Get last index of $array used to know how many files there are
function lastFileIndex($array) {
	$index_ARR = Array();
	$i = 0;
	foreach ($array as $key) {
		$index_ARR[$i] = getIndexFile($key);
		$i++;
	}

	usort($index_ARR, "compareFonction");
	return end($index_ARR);
}


// get node number from the node name
function getIndexFile($fileName) {
	$index_1 = substr($fileName, strpos($fileName, "_") + 1, 2);
	if(strpos($index_1, ".") != false) {
		$index_1 = substr($index_1, 0, 1);
	}
	return $index_1;
}

function compareFonction($a ,$b) {
	if($a > $b)
		return 1;
	else
		return 0;
}

function CompareFilesName($file1, $file2) {
	if(trim($file1) == trim($file2))
		return true;
	else
		return false;
}

function GetFile($fileName) {
	foreach (GetFilesInDirectory() as $file) {
		$dir = directoryContentFiles()."/".$file;
		if(is_file($dir) && $fileName == GetFileName($dir))
			print_r(GetContentFile($dir));
	}
}

function GetContentFile($dir) {
	if(is_file($dir)) {
		$content = file_get_contents($dir);
		return $content;
	}
	else {
		return false;
	}
}

function AbsoluteUrlFile($fileName, $lang) {
	if($lang == "fr") {
		$f = ConvertToFrenchFile(FindFile($fileName));
		if(is_file(directoryContentFiles()."/".$f)) {
			$name = GetFileName(directoryContentFiles()."/".$f);
			print_r(ABSOLUTEURL.$name);
		}
		else
			print_r("Error Juanpi#3");
	}
	else {
		$f = ConvertToEnglishFile(FindFile($fileName));
		if(is_file(directoryContentFiles()."/".$f)) {
			$name = GetFileName(directoryContentFiles()."/".$f);
			print_r(ABSOLUTEURL.$name);
		}
		else
			print_r("Error Juanpi#3");
	}
}



/*-------------------------------------------------------------- Verifications ------------------------------------------------------------------------------*/

function VerifyFileName($name_STR) {
	$name_STR = str_replace(" ", "_", $name_STR);
	return $name_STR;
}

//print_r(VerifyFileName("hello hey"));