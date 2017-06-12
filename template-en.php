<?php
include_once("../admin/config.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta lang="en">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="<?php echo ABSOLUTEURL; ?>front/css/style.css" rel="stylesheet">
	<link href="<?php echo ABSOLUTEURL; ?>front/css/content.css" rel="stylesheet">
	<title>My first website</title>
</head>
<body>
	<div class="page-wrap">
		<header class="site-header">
			<h1>{[(link|en|My first website)]}</h1>
			<span class="langs">{[(langs)]}</span>
			<nav class = "menu_container">
				<ul>
					<li>{[(link|en|My first page)]}</li>
				</ul>
			</nav>
		</header>
		<section class="content">
			<div class="content_text">
				{[(content)]}
			</div>
		</section>
	</div>
	<footer class="site-footer">
		<span>{[(update)]}</span>
	</footer>
</body>
<script type="text/javascript" src="<?php echo ABSOLUTEURL; ?>front/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTEURL; ?>front/js/script.js"></script>
</html>