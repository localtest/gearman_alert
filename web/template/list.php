<!DOCTYPE html>
<html>

<head>
	<link rel="apple-touch-icon-precomposed" href="<?php echo WEB_DOMAIN;?>resource/img/apple-touch-icon-120x120-precomposed.png" />
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no" />
	<link href="http://cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.css" rel="stylesheet">
	<title><?php echo $title;?></title>
</head>

<body>

	<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<?php if ($show_return) { ?>
			<a class="navbar-toggle collapsed" href="javascript:history.go(-1);">返回</a>
			<?php }?>

			<a class="navbar-brand" href="#">服务监控</a>
		</div>
	</div>
	</nav>

	<div class="list-group" style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;">
		<?php foreach ($t_list as $item) {?>
		<a href="<?php echo $item['link'];?>" class="list-group-item"><?php echo $item['name'];?></a>
		<?php }?>
	</div>
</body>
</html>
