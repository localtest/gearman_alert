<!DOCTYPE html>
<html>

<head>
	<link rel="apple-touch-icon-precomposed" href="<?php echo WEB_DOMAIN;?>resource/img/apple-touch-icon-120x120-precomposed.png" />
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<link href="http://cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.css" rel="stylesheet">
	<title><?php echo $title;?></title>
</head>

<body>

	<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">服务监控</a>
		</div>
		<?php if ($show_return) { ?>
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
				<li><a href="javascript:history.go(-1);">返回</a></li>
			</ul>
		</div>
		<?php }?>
	</div>
	</nav>

	<div class="list-group" style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;">
		<?php foreach ($t_list as $item) {?>
		<a href="<?php echo $item['link'];?>" class="list-group-item"><?php echo $item['name'];?></a>
		<?php }?>
	</div>
</body>
</html>
