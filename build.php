<?php
/**
 * Created by PhpStorm.
 * User: Jess
 * Github: https://github.com/jessppp
 */

/*
 * 使用方法:
 * 将build.php放到根目录下,执行php build.php 模块名
 */

if(count($argv) < 2) {
	exit('请输入模块名!' . "\n");
}

$moduleName = $argv[1];

define('MODULE_PATH', './Application' . DIRECTORY_SEPARATOR . $moduleName);
define('ADMIN_PATH', MODULE_PATH . '/Admin');
define('BEHAVIOR_PATH', MODULE_PATH . '/Behavior');
define('COMMON_PATH', MODULE_PATH . '/Common');
define('CONF_PATH', MODULE_PATH . '/Conf');
define('CONTROLLER_PATH', MODULE_PATH . '/Controller');
define('MODEL_PATH', MODULE_PATH . '/Model');
define('SQL_PATH', MODULE_PATH . '/Sql');
define('TAGLIB_PATH', MODULE_PATH . '/Taglib');
define('VIEW_ADMIN_PATH', MODULE_PATH . '/View/Admin/Index');
define('VIEW_INDEX_PATH', MODULE_PATH . '/View/Index');

function buildDir() {
	mkdir(MODULE_PATH);
	mkdir(ADMIN_PATH);
	mkdir(BEHAVIOR_PATH);
	mkdir(COMMON_PATH);
	mkdir(CONF_PATH);
	mkdir(CONTROLLER_PATH);
	mkdir(MODEL_PATH);
	mkdir(SQL_PATH);
	mkdir(TAGLIB_PATH);
	mkdir(VIEW_ADMIN_PATH, 0755, true);
	mkdir(VIEW_INDEX_PATH, 0755, true);
}

function buildFile($moduleName) {
	file_put_contents(ADMIN_PATH . '/IndexAdmin.class.php', getAdminContent($moduleName));
	file_put_contents(BEHAVIOR_PATH . '/' . $moduleName . '.class.php', getBehaviorContent($moduleName));
	file_put_contents(COMMON_PATH . '/function.php', getCommonContent());
	file_put_contents(CONF_PATH . '/conf.php', getConfContent());
	file_put_contents(CONTROLLER_PATH . '/IndexController.php', getControllerContent($moduleName));
	file_put_contents(MODEL_PATH . '/IndexModel.class.php', getModelContent($moduleName));
	file_put_contents(SQL_PATH . '/install.sql', getInstallSqlContent($moduleName));
	file_put_contents(SQL_PATH . '/uninstall.sql', getUninstallSqlContent($moduleName));
	file_put_contents(TAGLIB_PATH . '/' . $moduleName . '.class.php', getTaglibContent($moduleName));
	file_put_contents(VIEW_ADMIN_PATH . '/index.html', getViewAdminContent());
	file_put_contents(VIEW_INDEX_PATH . '/index.html', getViewIndexContent());
	file_put_contents(MODULE_PATH . '/opencmf.php', getOpencmfContent($moduleName));
}

function getModelContent($moduleName) {
	$smallModuleName = strtolower($moduleName);
	$content = <<<EOF
<?php
/**
 * Created by PhpStorm.
 * User: Jess
 * Github: https://github.com/jessppp
 */

/**
 * 默认模型
 *
 */
namespace {$moduleName}\Model;
use Think\Model;
class IndexModel extends Model {
    /**
     * 数据库真实表名
     * 一般为了数据库的整洁，同时又不影响Model和Controller的名称
     * 我们约定每个模块的数据表都加上相同的前缀，比如微信模块用weixin作为数据表前缀
     *
     */
    protected \$tableName = '{$smallModuleName}_index';

    /**
     * 自动验证规则
     *
     */
    protected \$_validate = array(
        array('title', 'require', '标题不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('content', 'require', '内容不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 自动完成规则
     *
     */
    protected \$_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_BOTH, 'function'),
        array('status', '1', self::MODEL_INSERT),
    );

}


EOF;
	return $content;
}

function getViewIndexContent() {
	$content = <<<EOF
<extend name="\$_home_public_layout"/>

<block name="main">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="alert alert-danger" role="alert">
                    <h4>您现在访问的是CoreThink模块：</h4>
                    <p>该页面是为演示页面！</p>
                </div>
            </div>
        </div>
    </div>
</block>


EOF;
	return $content;
}

function getViewAdminContent() {
	$content = <<<EOF
<extend name="\$_admin_public_layout"/><block name="main">欢迎使用CoreThink模块</block>


EOF;
	return $content;
}

function getTaglibContent($moduleName) {
	$content = <<<EOF
<?php
/**
 * Created by PhpStorm.
 * User: Jess
 * Github: https://github.com/jessppp
 */

namespace {$moduleName}\TagLib;
use Think\Template\TagLib;
/**
 * 标签库
 *
 */
class {$moduleName} extends TagLib {
    /**
     * 定义标签列表
     *
     */
    protected \$tags = array(
        'list' => array('attr' => 'name', 'close' => 1),  //文档列表
    );

    /**
     * 文档列表
     *
     */
    public function _list(\$tag, \$content) {
        \$name   = \$tag["name"];
        \$parse  = '<?php ';
        \$parse .= '\$__{$moduleName}_LIST__ = D("{$moduleName}/Index")->select();';
        \$parse .= ' ?>';
        \$parse .= '<volist name="__{$moduleName}_LIST__" id="'. \$name .'">';
        \$parse .= \$content;
        \$parse .= '</volist>';
        return \$parse;
    }
}



EOF;
	return $content;
}

function getInstallSqlContent($moduleName) {
	$moduleName = strtolower($moduleName);
	$content = <<<EOF

CREATE TABLE `oc_{$moduleName}_index` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(127) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='默认数据表';


EOF;
	return $content;
}

function getUninstallSqlContent($moduleName) {
	$moduleName = strtolower($moduleName);
	$content = <<<EOF
DROP TABLE IF EXISTS `oc_{$moduleName}_index`;


EOF;
	return $content;
}

function getControllerContent($moduleName) {
	$content = <<<EOF
<?php
/**
 * Created by PhpStorm.
 * User: Jess
 * Github: https://github.com/jessppp
 */

namespace {$moduleName}\Controller;
use Home\Controller\HomeController;
/**
 * 默认控制器
 *
 */
class IndexController extends HomeController {
    /**
     * 默认方法
     *
     */
    public function index() {
        \$this->assign('meta_title', "前台模块");
        \$this->display();
    }

    /**
     * 列表
     *
     */
    public function lists(\$cid) {
        \$map['cid']    = \$cid;
        \$map['status'] = 1;
        \$list = D('Index')->where(\$map)->select();
        \$this->assign('list', \$list );
        \$this->assign('meta_title', "{$moduleName}列表");
        \$this->display();
    }

    /**
     * 详情
     *
     */
    public function Detail(\$id) {
        \$map['status'] = 1;
        \$info = D('Index')->where(\$map)->find(\$id);
        \$this->assign('info', \$info );
        \$this->assign('meta_title', "{$moduleName}");
        \$this->display();
    }

}


EOF;
	return $content;
}

function getConfContent() {
	$content = <<<EOF
<?php
/**
 * Created by PhpStorm.
 * User: Jess
 * Github: https://github.com/jessppp
 */

return array(

    // 路由配置
    'URL_ROUTER_ON'     => true,
    'URL_MAP_RULES'     => array(
    ),
    'URL_ROUTE_RULES'   => array(

    ),

);

EOF;
	return $content;
}

function getCommonContent() {
	$content = <<<EOF
<?php
/**
 * Created by PhpStorm.
 * User: Jess
 * Github: https://github.com/jessppp
 */

EOF;
	return $content;
}

function getAdminContent($moduleName) {
	$content = <<<EOF
<?php
/**
 * Created by PhpStorm.
 * User: Jess
 * Github: https://github.com/jessppp
 */

namespace {$moduleName}\Admin;
use Admin\Controller\AdminController;
use Common\Util\Think\Page;
/**
 * 默认控制器
 *
 */
class IndexAdmin extends AdminController {
	/**
	 * 默认方法
	 *
	 */
	public function index() {
		// 获取列表
		\$map["status"] = array("egt", "0");  // 禁用和正常状态
		\$p = !empty(\$_GET["p"]) ? \$_GET["p"] : 1;
		\$model_object = D("Index");
		\$data_list = \$model_object
			->page(\$p, C("ADMIN_PAGE_ROWS"))
			->where(\$map)
			->order("sort asc,id asc")
			->select();
		\$page = new Page(
			\$model_object->where(\$map)->count(),
			C("ADMIN_PAGE_ROWS")
		);

		// 使用Builder快速建立列表页面。
		\$builder = new \Common\Builder\ListBuilder();
		\$builder->setMetaTitle("页面标题")  // 设置页面标题
		->addTopButton("addnew")    // 添加新增按钮
		->addTopButton("resume")  // 添加启用按钮
		->addTopButton("forbid")  // 添加禁用按钮
		->setSearch("请输入ID/标题", U("index"))
			->addTableColumn("id", "ID")
			->addTableColumn("title", "标题")
			->addTableColumn("create_time", "创建时间", "time")
			->addTableColumn("sort", "排序")
			->addTableColumn("status", "状态", "status")
			->addTableColumn("right_button", "操作", "btn")
			->setTableDataList(\$data_list)     // 数据列表
			->setTableDataPage(\$page->show())  // 数据列表分页
			->addRightButton("edit")           // 添加编辑按钮
			->addRightButton("forbid")  // 添加禁用/启用按钮
			->addRightButton("delete")  // 添加删除按钮
			->display();
	}

	/**
	 * 新增
	 *
	 */
	public function add() {
		if (IS_POST) {
			\$model_object = D("Index");
			\$data = \$model_object->create(format_data());
			if (\$data) {
				\$id = \$model_object->add(\$data);
				if (\$id) {
					\$this->success("新增成功", U("index"));
				} else {
					\$this->error("新增失败".\$model_object->getError());
				}
			} else {
				\$this->error(\$model_object->getError());
			}
		} else {
			// 使用FormBuilder快速建立表单页面
			\$builder = new \Common\Builder\FormBuilder();
			\$builder->setMetaTitle("新增")  // 设置页面标题
			->setPostUrl(U("add"))      // 设置表单提交地址
			->addFormItem("title", "text", "标题", "标题")
				->addFormItem("content", "kindeditor", "内容", "内容")
				->display();
		}
	}

	/**
	 * 编辑
	 *
	 */
	public function edit(\$id) {
		if (IS_POST) {
			\$model_object = D("Index");
			\$data = \$model_object->create(format_data());
			if (\$data) {
				\$id = \$model_object->save(\$data);
				if (\$id !== false) {
					\$this->success("更新成功", U("index"));
				} else {
					\$this->error("更新失败".\$model_object->getError());
				}
			} else {
				\$this->error(\$model_object->getError());
			}
		} else {
			// 使用FormBuilder快速建立表单页面。
			\$builder = new \Common\Builder\FormBuilder();
			\$builder->setMetaTitle("编辑")  // 设置页面标题
			->setPostUrl(U("edit"))     // 设置表单提交地址
			->addFormItem("id", "hidden", "ID", "ID")
				->addFormItem("title", "text", "标题", "标题")
				->addFormItem("content", "kindeditor", "内容", "内容")
				->addFormItem("sort", "num", "排序", "用于显示的顺序")
				->setFormData(D("Index")->find(\$id))
				->display();
		}
	}
}


EOF;

	return $content;
}

function getBehaviorContent($moduleName) {
	$content = <<<EOF
<?php
/**
 * Created by PhpStorm.
 * User: Jess
 * Github: https://github.com/jessppp
 */
namespace {$moduleName}\Behavior;
use Think\Behavior;
use Think\Hook;
defined('THINK_PATH') or exit();
/**
 * 行为扩展
 *
 */
class {$moduleName}Behavior extends Behavior {
    /**
     * 行为扩展的执行入口必须是run
     *
     */
    public function run(&\$content) {
        // 行为扩展逻辑
    }
}


EOF;

	return $content;
}

function getOpencmfContent($moduleName) {
	$content = <<<EOF
<?php
/**
 * Created by PhpStorm.
 * User: Jess
 * Github: https://github.com/jessppp
 */

return array(
    // 模块信息
    'info' => array(
        'name'        => '{$moduleName}',
        'title'       => '标题',
        'icon'        => 'fa fa-flask',
        'icon_color'  => '#F9B440',
        'description' => '描述',
        'developer'   => 'Jess',
        'website'     => 'http://jesuspan.sinaapp.com',
        'version'     => '1.0.0',
        'dependences' => array(
            'Admin'   => '1.0.0',
        )
    ),

    // 用户中心导航
    'user_nav' => array(
        'center' => array(
            '0' => array(
                'title' => '导航1',
                'icon'  => 'fa fa-flask',
                'url'   => '{$moduleName}/Index/index',
            ),
        ),
        'main' => array(
            '0' => array(
                'title' => '导航2',
                'icon'  => 'fa fa-flask',
                'url'   => '{$moduleName}/Index/index',
            ),
        ),
    ),

    // 模块配置
    'config' => array(
        'status' => array(
            'title'   => '是否开启',
            'type'    => 'radio',
            'options' => array(
                '1' => '开启',
                '0' => '关闭',
            ),
            'value' => '1',
        ),
        'taglib' => array(
            'title'  => '加载标签库',
            'type'   =>'checkbox',
            'options'=> array(
                '{$moduleName}' => '{$moduleName}',
            ),
            'value'  => array(
                '0'  => '{$moduleName}',
            ),
        ),
        'behavior' => array(
            'title'   => '行为扩展',
            'type'   =>'checkbox',
            'options'=> array(
                '{$moduleName}' => '{$moduleName}',
            ),
        ),
    ),

    // 后台菜单及权限节点配置
    'admin_menu' => array(
        '1' => array(
            'pid'   => '0',
            'title' => '一级模块名',
            'icon'  => 'fa fa-flask',
        ),
        '2' => array(
            'pid'   => '1',
            'title' => '二级模块名',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '3' => array(
            'pid'   => '2',
            'title' => '三级模块名',
            'icon'  => 'fa fa-group',
            'url'   => '{$moduleName}/Index/index'
        ),
    ),
);


EOF;
	return $content;
}

buildDir();
buildFile($moduleName);
