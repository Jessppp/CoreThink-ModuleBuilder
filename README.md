# CoreThink模块生成工具

CoreThink是一款强大的CMF,后台htmlbuilder非常方便增删改查的页面生成,但是由于官方自动生成模块需要充值中级会员,索性自己写了个生成器

使用方法:
将build.php放到CoreThink项目根目录下,
执行php build.php 模块名
本工具就会自动生成相应的模块

注意:
- 生成后需要自己修改Sql目录下的install.sql与uninstall.sql文件
- 如果有修改默认的数据表前缀配置,需要修改生成的Model/IndexModel.class.php文件
