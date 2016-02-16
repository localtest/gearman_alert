Gearman Alert
====

基于Gearman的负载预警

支持特性:

	1. 历史负载Graph图
	2. email告警


著作权归作者所有。
商业转载请联系作者获得授权，非商业转载请注明出处。
作者：郑宇
链接：http://www.zhihu.com/question/23378396/answer/40604931
来源：知乎

<img src="./intro/001.png" witdh=269 height=410 alt="001"/>
<img src="./intro/002.png" witdh=269 height=410 alt="002"/>
<img src="./intro/003.png" witdh=269 height=410 alt="003"/>

Todo:

	1. ES支持
	2. 短信告警

1. 启动Web Server(可以直接用php 自带的原生Server), 指定根目录为web目录
2. 发送告警信息，依赖于gearman的分发功能，首先需要部署gearman服务端
