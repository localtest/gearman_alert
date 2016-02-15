<!DOCTYPE html>
<html>

<head>
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
