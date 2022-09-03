<?php
/**
 * FootnotesPlus 注脚增强（悬停提示、注脚标题等）
 *
 * @package FootnotesPlus
 * @author 玖玖kyuu
 * @version 0.01
 * @link https://www.moyu.win
 * @date 2022.9.3
 */

class FootnotesPlus_Plugin implements Typecho_Plugin_Interface {

    /**
     * 启用插件方法,如果启用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        //在博客页首输出
        Typecho_Plugin::factory('Widget_Archive')->header = array('FootnotesPlus_Plugin', 'header');
        //在博客页脚输出
        Typecho_Plugin::factory('Widget_Archive')->footer = array('FootnotesPlus_Plugin', 'footer');
        //文章页输出 之前
        Typecho_Plugin::factory('Widget_Archive')->beforeRender = array('FootnotesPlus_Plugin', 'beforeRender');
        //文章页输出 之后
        Typecho_Plugin::factory('Widget_Archive')->afterRender = array('FootnotesPlus_Plugin', 'afterRender');

        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('FootnotesPlus_Plugin', 'contentEx');

        return "插件已开启，请进行配置";
    }

    /**
     * 获取插件配置面板
     *
     * @static
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        //是否使用插件jQuery，FootnotesPlus 必备
        $jQuerySwitch = new Typecho_Widget_Helper_Form_Element_Checkbox('jQuerySwitch', array(1 => '是否使用插件的jQuery'),1,'一般选项', '如果主题自带jQuery可关闭，但是必须保证jQuery在FootnotesPlus之前加载并且只加载一个jQuery');
        $form->addInput($jQuerySwitch);

        //是否插入注脚标题
        $isInsertFootnotesLabel = new Typecho_Widget_Helper_Form_Element_Checkbox('isInsertFootnotesLabel', array(0 => '是否插入注脚标题'),NULL,NULL, '位置显示在注脚上方');
        $form->addInput($isInsertFootnotesLabel);

        //注脚标题内容
        $insertFootnotesLabelContent = new Typecho_Widget_Helper_Form_Element_Text('insertFootnotesLabelContent', NULL, "<p>注释</p>", _t('注脚标题：'), '可以使用HTML代码');
        $form->addInput($insertFootnotesLabelContent);

        //是否使用插件jQuery Migrate，FootnotesPlus 必备
        $isJQueryMigrate = new Typecho_Widget_Helper_Form_Element_Checkbox('isJQueryMigrate', array(1 => '是否使用插件的jQuery Migrate'),1,NULL, '如果主题自带jQuery Migrate可关闭');
        $form->addInput($isJQueryMigrate);

        //是否使用方括号样式
        $isSquareBrackets = new Typecho_Widget_Helper_Form_Element_Checkbox('isSquareBrackets', array(0 => '是否使用方括号样式 例[1]、[2]'),NULL,NULL, NULL);
        $form->addInput($isSquareBrackets);

        //是否使用自定义css
        $isCustomCss = new Typecho_Widget_Helper_Form_Element_Checkbox('isCustomCss', array(0 => '是否使用自定义css'),NULL,'高级选项 (如果你不了解，请勿开启)', NULL);
        $form->addInput($isCustomCss);

        //是否使用自定义js
        $isCustomJs = new Typecho_Widget_Helper_Form_Element_Checkbox('isCustomJs', array(0 => '是否使用自定义js'),NULL,NULL, NULL);
        $form->addInput($isCustomJs);

        //jQuery地址
        $jQueryUrl = new Typecho_Widget_Helper_Form_Element_Text('jQueryUrl', NULL, '//cdn.bootcss.com/jquery/3.6.0/jquery.min.js', _t('jQuery地址：'), '默认无需修改');
        $form->addInput($jQueryUrl);

        //jQuery Migrate 地址
        $jQueryMigrateUrl = new Typecho_Widget_Helper_Form_Element_Text('jQueryMigrateUrl', NULL, '//cdn.bootcdn.net/ajax/libs/jquery-migrate/3.3.2/jquery-migrate.min.js', _t('jQuery Migrate地址：'), '默认无需修改');
        $form->addInput($jQueryMigrateUrl);

        //自定义css内容
        $customCssContent = new Typecho_Widget_Helper_Form_Element_Textarea('customCssContent', NUll, NUll, _t('自定义css内容：'), '插件禁用后数据将丢失，请注意自行备份');
        $form->addInput($customCssContent);

        //自定义js内容
        $customJsContent = new Typecho_Widget_Helper_Form_Element_Textarea('customJsContent', NUll, NUll, _t('自定义js内容：'), '插件禁用后数据将丢失，请注意自行备份');
        $form->addInput($customJsContent);

    }



    /* 插件实现方法 */
    public static function header(){
        $isCustomCss = Helper::options()->plugin("FootnotesPlus")->isCustomCss;
        $customCssContent = Helper::options()->plugin("FootnotesPlus")->customCssContent;
        if($isCustomCss){
            echo "\n<style id='footnotes-plus-custom-css' type='text/css'>\n" .$customCssContent. "\n"."</style>". "\n";
        }
    }

    public static function footer(){
        $pluginUrl = Helper::options()->pluginUrl;

        $isSquareBrackets = Helper::options()->plugin("FootnotesPlus")->isSquareBrackets;

        $jQuerySwitch = Helper::options()->plugin("FootnotesPlus")->jQuerySwitch;
        $isJQueryMigrate = Helper::options()->plugin("FootnotesPlus")->isJQueryMigrate;

        $isCustomJs = Helper::options()->plugin("FootnotesPlus")->isCustomJs;

        $jQueryMigrateUrl = Helper::options()->plugin("FootnotesPlus")->jQueryMigrateUrl;

        $jQueryUrl = Helper::options()->plugin("FootnotesPlus")->jQueryUrl;

        $customJsContent = Helper::options()->plugin("FootnotesPlus")->customJsContent;

        //css
        echo  "<link rel='stylesheet' id='qtipstyles-css'  href='" . $pluginUrl . "/FootnotesPlus/qtip/jquery.qtip.min.css' type='text/css' media='' />". "\n";
        echo  "<link rel='stylesheet' id='easyfootnotescss-css'  href='" . $pluginUrl . "/FootnotesPlus/easy-footnotes.css' type='text/css' media='' />". "\n";



        //js
        //判断是否启动插件的jQuery
        if($jQuerySwitch){
            echo "<script src='" . $jQueryUrl . "'></script>" . "\n";
        }
        if($isJQueryMigrate){
            echo "<script src='" . $jQueryMigrateUrl . "'></script>" . "\n";
        }

        echo "<script src='" . $pluginUrl . "/FootnotesPlus/qtip/jquery.qtip.min.js'></script>" . "\n";

        //是否使用方括号
        if($isSquareBrackets){
            var_dump($isSquareBrackets);
            echo "<script type=\"text/javascript\">$.qtip.isSquareBrackets = true</script>" . "\n";
        }

        echo "<script src='" . $pluginUrl . "/FootnotesPlus/qtip/jquery.qtipcall.js'></script>" . "\n";

        //自定义内容
        if($isCustomJs){
            echo "<script type=\"text/javascript\">\n".$customJsContent."\n"."</script>" . "\n";
        }
    }

    /**
    * 文章输出之前
     */
    public static function beforeRender($archive){
//        $archive->content = $archive->content."||||||||||ss";
    }

    /**
     * 文章输出之后
     */
    public static function afterRender($archive){
//        var_dump($archive);
    }


    /**
    * 文章调整
     */
    public static function contentEx($content, $widget, $lastResult) {
        $isInsertFootnotesLabel= Helper::options()->plugin("FootnotesPlus")->isInsertFootnotesLabel;
        if(!$isInsertFootnotesLabel){
            return $content;
        }
//       var_dump();

        $insertFootnotesLabelContent= Helper::options()->plugin("FootnotesPlus")->insertFootnotesLabelContent;

        $searchStr = "<div class=\"footnotes\"><hr>";
        $pos = strpos($content,$searchStr);

       if($pos){
         return  substr_replace($content,$insertFootnotesLabelContent,$pos+strlen($searchStr),0);
       }
        return $content;

//        return $content;
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {

    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {
//        return "已移除";
    }
}