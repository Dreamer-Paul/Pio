<?php
/**
 * 一个简易的 Live2D 插件，在 <a href="https://github.com/journey-ad/live2d_src">@Jad</a> 的项目上增加交互功能
 *
 * @package Pio
 * @author Dreamer-Paul
 * @version 2.4
 * @link https://paugram.com
 */

class Pio_Plugin implements Typecho_Plugin_Interface{

    /* 激活插件方法 */
    public static function activate(){
        Typecho_Plugin::factory('Widget_Archive') -> header = array('Pio_Plugin', 'header');
        Typecho_Plugin::factory('Widget_Archive') -> footer = array('Pio_Plugin', 'footer');
    }

    /* 禁用插件方法 */
    public static function deactivate(){}

    /* 插件配置方法 */
    public static function config(Typecho_Widget_Helper_Form $form){

        // 插件信息与更新检测
        function paul_update($name, $version){
            echo "<style>.paul-info{text-align:center; margin:1em 0;} .paul-info > *{margin:0 0 1rem} .buttons a{background:#467b96; color:#fff; border-radius:4px; padding:.5em .75em; display:inline-block}</style>";
            echo "<div class='paul-info'>";
            echo "<h2>Pio 看版娘插件 (".$version.")</h2>";
            echo "<p>By: <a href='https://github.com/Dreamer-Paul'>Dreamer-Paul</a></p>";
            echo "<p class='buttons'><a href='https://paugram.com/coding/add-poster-girl-with-plugin.html'>项目介绍</a>
                  <a href='https://github.com/Dreamer-Paul/Pio/releases'>更新日志</a></p>";

            $update = file_get_contents("https://api.paugram.com/update/?name=".$name."&current=".$version."&site=".$_SERVER['HTTP_HOST']);
            $update = json_decode($update, true);

            if(isset($update['text'])){echo "<p>".$update['text']."</p>"; };
            if(isset($update['message'])){echo "<p>".$update['message']."</p>"; };

            echo "</div>";
        }
        paul_update("Pio", "2.4");

        // 读取模型文件夹
        $models = array();
        $load = glob("../usr/plugins/Pio/models/*");

        foreach($load as $key => $value){
            $single = substr($value, 26);
            $models[$single] = ucfirst($single);
        };

        // 选择模型
        $choose_models = new Typecho_Widget_Helper_Form_Element_Checkbox('choose_models', $models, ['pio'], _t('选择模型'), _t('选择插件 Models 目录下的模型，每个模型为一个文件夹，并确定配置文件名为 <a>model.json</a>'));
        $form -> addInput($choose_models);

        // 选择外链模型
        $custom_model = new Typecho_Widget_Helper_Form_Element_Text('custom_model', NULL, NULL, _t('选择外链模型'), _t('在这里填入一个模型配置文件 <a>model.json</a> 的地址，可供使用外链模型，不填则使用插件目录下的模型'));
        $form -> addInput($custom_model);

        // 自定义定位
        $position = new Typecho_Widget_Helper_Form_Element_Radio('position',
            array(
              'left' => _t('靠左'),
              'right' => _t('靠右'),
            ),
            'left', _t('自定义位置'), _t('自定义看板娘所在的位置'));
        $form -> addInput($position);

        // 自定义宽高
        $custom_width = new Typecho_Widget_Helper_Form_Element_Text('custom_width', NULL, NULL, _t('自定义宽度'), _t('在这里填入自定义宽度，部分模型需要修改'));
        $form -> addInput($custom_width);

        $custom_height = new Typecho_Widget_Helper_Form_Element_Text('custom_height', NULL, NULL, _t('自定义高度'), _t('在这里填入自定义高度，部分模型需要修改'));
        $form -> addInput($custom_height);

        // 夜间模式函数
        $night = new Typecho_Widget_Helper_Form_Element_Text('night', NULL, NULL, _t('夜间模式函数'), _t('如果你的主题支持夜间模式，请在这里填写主题对应的 JS 函数'));
        $form -> addInput($night);

        // 展现模式
        $custom_mode = new Typecho_Widget_Helper_Form_Element_Radio('custom_mode',
            array(
              'static' => _t('静态'),
              'fixed' => _t('固定'),
              'draggable' => _t('可移动'),
            ),
            'static', _t('展现模式'), _t('自定义看板娘的展现模式。静态模式将不启用按钮交互功能'));
        $form -> addInput($custom_mode);

        // 隐藏看板娘
        $hidden = new Typecho_Widget_Helper_Form_Element_Radio('hidden',
            array(
              '0' => _t('关闭'),
              '1' => _t('开启'),
            ),
            '0', _t('隐藏看板娘'), _t('开启后将在移动设备上隐藏看板娘'));
        $form -> addInput($hidden);

        // 是否开启时间小贴士
        $tips = new Typecho_Widget_Helper_Form_Element_Radio('tips',
            array(
              '0' => _t('关闭'),
              '1' => _t('开启'),
            ),
            '0', _t('时间小贴士'), _t('开启后将在没有访问来源的情况下展示，覆盖入站提示'));
        $form -> addInput($tips);

        // 交互提示扩展
        $dialog = new Typecho_Widget_Helper_Form_Element_Textarea('dialog', NULL, NULL, _t('交互提示扩展'), _t('在这里填入你的自定义交互提示配置信息，如想保持默认，请留空'));
        $form -> addInput($dialog);
    }

    /* 个人用户的配置方法 */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    /* 插件实现方法 */
    public static function header(){
        echo("<link href='" . Helper::options() -> pluginUrl . "/Pio/static/pio.css' rel='stylesheet' type='text/css'/>\n");
    }
    public static function footer(){
        // 生成画布
        function getCanvas(){
            $height = Typecho_Widget::widget('Widget_Options') -> Plugin('Pio') -> custom_height;
            $width  = Typecho_Widget::widget('Widget_Options') -> Plugin('Pio') -> custom_width;

            return '<canvas id="pio" width="' . (!$width ? 280 : $width) . '" height="' . (!$height ? 250: $height) . '"></canvas>';
        }

        // 生成载入器
        function getLoader(){
            $plugin = Typecho_Widget::widget('Widget_Options') -> Plugin('Pio');

            $config = array(
                "mode" => $plugin -> custom_mode,
                "hidden" => $plugin -> hidden == 1 ? true : false,
                "content" => $plugin -> dialog ? json_decode($plugin -> dialog, true) : array()
            );

            if($plugin -> custom_model){
                $model = array($plugin -> custom_model);
            }
            else if($plugin -> choose_models){
                $model = $plugin -> choose_models;

                if(is_array($model)){
                    foreach($model as &$item){
                        $item = Helper::options() -> pluginUrl . "/Pio/models/" . $item . "/model.json";
                    }
                }
                else{
                    $model = array(Helper::options() -> pluginUrl . "/Pio/models/" . $model . "/model.json");
                }
            }
            else{
                $model = array(Helper::options() -> pluginUrl . "/Pio/models/pio/model.json");
            }

            if($plugin -> night){
                $config["night"] = $plugin -> night;
            }

            if($plugin -> tips){
                $config["tips"] = true;
            }

            $config["model"] = $model;

            return '<script>var pio = new Paul_Pio(' . json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ');</script>';
        }

        $canvas = getCanvas();
        $loader = getLoader();
        $position = Typecho_Widget::widget('Widget_Options') -> Plugin('Pio') -> position == "left" ? " left" : " right";

        echo str_replace(array("{position}", "{canvas}"), array($position, $canvas),
            '<div class="pio-container{position}"><div class="pio-action"></div>{canvas}</div>'
        );

        echo "<script src='" . Helper::options() -> pluginUrl . "/Pio/static/l2d.js'></script>" . "\n";
        echo "<script src='" . Helper::options() -> pluginUrl . "/Pio/static/pio.js'></script>" . "\n";
        echo $loader;
    }
}
