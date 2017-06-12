<?php

include_once "functions.php";
include_once "generate.php";
$redirectPage = "index.php";
$redirectPageEN = "index.php?l=en";
$contentFolder = "content_files/";
$compiledFolder = "compiled_files/";

if(isset($_POST["file_content"]) && isset($_POST["file_name"]) && isset($_POST["file_language"])) {
	$content = $_POST["file_content"];
	$file = FindFile($_POST["file_name"]);
	$language = $_POST["file_language"];
	$files = getFilesInDirectory();

	if($language == "en") {
		$verif = true;
		$file = ConvertToEnglishFile($file);
		// Verification double entete
		$urlFile = directoryContentFiles()."/".$file;
		foreach ($files as $each) {
			$urlEachFile  = directoryContentFiles()."/".$each;
			if(is_file($urlEachFile)) {
				if(CompareFilesName(GetFileName($urlEachFile), GetFileNameContent($content)) && !CompareFilesName($file, $each))
					$verif = false;
			}
		}
		if($verif) {
			SaveFile($file, $content);
			CompileAll($contentFolder, $compiledFolder);
			$f = GetFileName(directoryContentFiles()."/".$file);
			global $redirectPageEN;
			header("Location: ".$redirectPageEN."&f=".$f."&e=11");
		}
		else {
			global $redirectPageEN;
			$f = GetFileName(directoryContentFiles()."/".$file);
			header("Location: ".$redirectPageEN."&f=".$f."&e=2");
		}
	}

	else {
		$verif = true;
		$file = ConvertToFrenchFile($file);
		// Verification double entete
		$urlFile = directoryContentFiles()."/".$file;
		foreach ($files as $each) {
			$urlEachFile = directoryContentFiles()."/".$each;
			if(is_file($urlEachFile)) {
				if(CompareFilesName(GetFileName($urlEachFile), GetFileNameContent($content)) && !CompareFilesName($file, $each)) 
					$verif = false;
			}
		}
		if($verif) {
			SaveFile($file, $content);
			CompileAll($contentFolder, $compiledFolder);
			$f = GetFileName(directoryContentFiles()."/".$file);
			global $redirectPage;
			header("Location: ".$redirectPage."?f=".$f."&e=10");
		}
		else {
			global $redirectPage;
			$f = GetFileName(directoryContentFiles()."/".$file);
			header("Location: ".$redirectPage."?f=".$f."&e=1");
		}
	}
}



else if (isset($_GET["etat"]) && $_GET["etat"] == "getFile") {
    if(isset($_GET['en_file']))
		print_r(GetEnglishFileByUrl());
	if(isset($_GET['fr_file']))
		print_r(GetFrenchFileByUrl());
}


if(isset($_GET["delete"])) {
	DeleteFile();
	CompileAll($contentFolder, $compiledFolder);
}

if(isset($_GET["create"])) {
	createFile();
	CompileAll($contentFolder, $compiledFolder);
}

if(isset($_GET["compile"])) {
	CompileAll($contentFolder, $compiledFolder);
	echo "All files were compiled";
}

if(isset($_GET["redirect"]) && isset($_GET["l"])) {
	AbsoluteUrlFile($_GET["redirect"], $_GET["l"]);
}