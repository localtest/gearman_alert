<!DOCTYPE html>
<html>

<head>
	<link rel="apple-touch-icon-precomposed" href="<?php echo WEB_DOMAIN;?>resource/img/apple-touch-icon-120x120-precomposed.png" />
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<link href="//cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.css" rel="stylesheet">
	<title>服务列表</title>
</head>

<body>
	<div class="list-group">
		<?php foreach ($t_list as $item) {?>
		<a href="<?php echo $item['link'];?>" class="list-group-item"><?php echo $item['name'];?></a>
		<?php }?>
	</div>
</body>
</html>
