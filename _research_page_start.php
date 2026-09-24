<?php include("_security.php"); include("_config.php"); include("_globals.php"); include("_security_access.php"); include_once("_f_research.php"); requireLoggedIn($isLoggedIn); include("_master_head.php"); ?>
<div class="grid-container">
<div class="grid-emptyblack"></div><div class="grid-header"><?php include("_master_header.php"); ?></div><div class="grid-emptyblack"></div>
<?php $breadcrumimage = randomBreadcrumImage(); ?>
<div class="grid-breadcrum" style="background-image:url(images/<?= htmlspecialchars($breadcrumimage) ?>);"><?php include("_master_breadcrum.php"); ?></div>
<div class="grid-topmenu"><?php include("_master_menu.php"); ?></div>
<div class="grid-empty"></div><div class="grid-main"><div class="main">
