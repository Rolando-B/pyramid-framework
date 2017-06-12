<?php

include_once("config.php");

function CompileAll($contentFolder, $compiledFolder) {
	preg_replace_callback("/\{\[\(link\|(.*)\|(.*)\)\]\}/", function($matches) {

			if(!NodeExists($matches[1], "content_files/")) { CreateFrenchFile($matches[1]); }
		}, file_get_contents(ABSOLUTEROOT."template-fr.php"));
		preg_replace_callback("/\{\[\(link\|(.*)\|(.*)\)\]\}/", function($matches) {
			if(!NodeExists($matches[1], "content_files/")) { CreateEnglishFile($matches[1]); }
		}, file_get_contents(ABSOLUTEROOT."template-en.php"));
	$files = scandir(ABSOLUTEROOT.$contentFolder);
	foreach ($files as $file) {
		if(substr($file, strrpos($file, ".")) == ".raw") {
			file_put_contents(ABSOLUTEROOT.$compiledFolder.substr($file, 0, -4), Compile(ABSOLUTEROOT.$contentFolder.$file));
		}
	}
	generateHtaccess($contentFolder, $compiledFolder);
}

function Compile($filename) {
	$final = applyTemplate($filename);
	echo $filename."</br>";
	$header = fgets(fopen($filename, 'r'));
	echo $header."</br>";
	$content = file_get_contents($filename, NULL, NULL, strlen($header));
	echo $content."</br>";
	$tags = array("link", "langs", "update", "slider", "breadcrumbs", "content");
	foreach ($tags as $tag) {
		$tagf = "Apply".ucfirst($tag)."Tag";
		$final = $tagf($final, $filename, $content);
	}
	$final = fixPaths($final);
	return $final;
}

function fixPaths($file) {
	$final = preg_replace_callback("/\.\.\/((files|images)\/.+)/",function($matches) {
		return ABSOLUTEURL.$matches[1];
	}, $file);
	return $final;
}

function ApplySliderTag($file, $filename, $content) {
	$final = preg_replace_callback("/\{\[\(slider\|(.*)\)\]\}/",function($matches) {
		$items = explode("|", $matches[1]);
		$result = "<div class=\"sliderContainer\">";
		foreach ($items as $item) {
			$result.="<div class='sliderDiv' style='background-image: url(\"".ABSOLUTEURL.$item."\");'></div>";
		}
		$result.="</div>";
		return $result;
	}, $file);
	return $final;
}

function ApplyContentTag($file, $filename, $content) {
	$final = preg_replace("/\{\[\(content\)\]\}/", $content, $file);
	return $final;
}

function ApplyLinkTag($file, $filename, $content) {
	$final = preg_replace_callback("/\{\[\(link\|(.*)\|(.*)\)\]\}/", function ($matches) {
		return "<a href='".ABSOLUTEURL.$matches[1]."'>".$matches[2]."</a>";
	}, $file);
	return $final;
}

function NodeExists($fileurl, $contentFolder) {
	$allFiles = scandir(ABSOLUTEROOT.$contentFolder);
	$a = false;
	foreach ($allFiles as $file) {
		$url = ABSOLUTEROOT.$contentFolder.$file;
		if(is_file($url) && GetFileInfo($url) == $fileurl){
			$file = file_get_contents($url);
			$a = !$a;
			break;
		}
	}
	return $a;
}

function ApplyLangsTag($file, $filename, $content) {
	$final = preg_replace_callback("/\{\[\(langs\)\]\}/", function () use ($filename) {

		$lang = GetLang($filename);
		$otherLang = ($lang == "en") ? "fr" : "en";
		$other = preg_replace("/\.".$lang."\./", ".".$otherLang.".", $filename);
		$url = GetFileInfo($other);
		if(!$url) $url = $otherLang;
		$langReplacement = '<nav class="langs"><input type="checkbox" name="langs" class="langs-checkbox" id="mylangs" disabled ';
		$langReplacement .= ($lang == "en") ?
		"><label class=\"langs-label\" for=\"mylangs\"><a href='".ABSOLUTEURL.$url."'>":
		"checked><label class=\"langs-label\" for=\"mylangs\"><a href='".ABSOLUTEURL.$url."'>";
		$langReplacement .= "<span class=\"lang-inner\"></span><span class=\"lang-switch\"></span></a></label></nav>";
		return $langReplacement;
	}, $file);
	return $final;
}

function ApplyBreadcrumbsTag($file, $filename, $content) {
	$final = preg_replace_callback("/\{\[\(breadcrumbs\)\]\}/", function () use ($filename) {
		$lang = GetLang($filename);
		$url = explode("/", GetFileInfo($filename));
		$u = "";
		$parts = "<a href='".ABSOLUTEURL.$lang."'>".$lang."</a>";
		if($url[0] == "fr" || $url[0] == "en") return $parts;
		foreach ($url as $part) {
			echo $part;
			$u .= (empty($u)) ? $part : "/".$part;
			$parts.="<span>-></span><a href='".ABSOLUTEURL.$u."'>".$part."</a>";
		}
		return $parts;
	}, $file);
	return $final;
}

function ApplyUpdateTag($file, $filename, $content) {
	$final = preg_replace_callback("/\{\[\(update\)\]\}/", function() use ($filename) {
		$lang = GetLang($filename);
		$replacement = ($lang == "en") ? "Last modified on : ".date("F d Y H:i:s.", filemtime($filename)) : "Dernière modification le : ".date("d/m/Y H:i:s.", filemtime($filename));
		return $replacement;
	}, $file);
	return $final;
}

function GetLang($file) {
	$lang = substr($file, strpos($file, ".") + 1, 2);
	return $lang;
}

function GetFileInfo($filename, $info = 'url') {
	$infoIndex = 0;
	if($info == 'title') $infoIndex++;
	if($info == 'menu') $infoIndex++;

	if(!file_exists($filename)) return false;
	$file = fopen($filename, 'r');
	$line = fgets($file);
	fclose($file);
	$line = substr($line, 1, -1);
	$array = explode("|", $line);
	return $array[$infoIndex];
}

function applyTemplate($file){

	$lang = GetLang($file);
	$path = ABSOLUTEROOT."template-".$lang.".php";
	$form = file_get_contents($path);
	return $form;
}

function generateHtaccess($contentDir, $compiledDir) {

	//Static part

	$contentHtaccess = "Options +FollowSymlinks\n";
	$contentHtaccess .= "RewriteEngine On\n";
	$contentHtaccess .= "RewriteBase /".ROOTDIR."\n";
	$contentHtaccess .= "RewriteRule ^admin(.*) admin$1 [L]\n";
	$contentHtaccess .= "RewriteRule ^stats(.*) stats$1 [L]\n";
	$contentHtaccess .= "RewriteRule ^files(.*) files$1 [L]\n";
	$contentHtaccess .= "RewriteRule ^compiled(.*) compiled$1 [L]\n";
	$contentHtaccess .= "RewriteRule ^users(.*) users$1 [F]\n";
	$contentHtaccess .= "RewriteRule ^(.*)/$ ".ABSOLUTEROOT."$1\n";

	$content_files = scandir(ABSOLUTEROOT.$contentDir);

	foreach ($content_files as $fileName) {
		$fileURL = $contentDir.$fileName;
		if(is_file(ABSOLUTEROOT.$fileURL)) {

			$file = fopen(ABSOLUTEROOT.$fileURL, "r+");
			$destinationURL = $compiledDir.substr($fileName, 0, strlen($fileName) - 4);

			$line = fgets($file);
			$line = substr($line, 1, -1);
			$fileHeader = explode("|",$line);
			$url = $fileHeader[0];
			fclose($file);

			$contentHtaccess .= "RewriteRule ^".$url."$ ".$destinationURL." [L]\n";
		}
	}

	file_put_contents(ABSOLUTEROOT.".htaccess", $contentHtaccess);
}

//CompileAll($contentFolder, $compiledFolder);