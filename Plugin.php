<?php
/**
 * 一个简易的 Live2D 插件，建立在 <a href="https://github.com/journey-ad/live2d_src">@Jad</a> 的项目上
 *
 * @package Pio
 * @author Dreamer-Paul
 * @version 1.1
 * @link https://paugram.com
 */

class Pio_Plugin implements Typecho_Plugin_Interface{

    /* 激活插件方法 */
    public static function activate(){
        Typecho_Plugin::factory('Widget_Archive')->header = array('Pio_Plugin', 'header');
        Typecho_Plugin::factory('Widget_Archive')->footer = array('Pio_Plugin', 'footer');
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
                  <a href='https://github.com/Dreamer-Paul/Pio/releases/tag/".$version."'>更新日志</a></p>";

            $update = file_get_contents("https://api.paugram.com/update/?name=".$name."&current=".$version."&site=".$_SERVER['HTTP_HOST']);
            $update = json_decode($update, true);

            if($update['text']){echo "<p>".$update['text']."</p>"; };
            if($update['message']){echo "<p>".$update['message']."</p>"; };

            echo "</div>";
        }
        paul_update("Pio", "1.1");

        // 自定义宽高
        $custom_width = new Typecho_Widget_Helper_Form_Element_Text('custom_width', NULL, NULL, _t('自定义宽度'), _t('在这里填入自定义宽度，部分模型需要修改'));
        $form->addInput($custom_width);

        $custom_height = new Typecho_Widget_Helper_Form_Element_Text('custom_height', NULL, NULL, _t('自定义高度'), _t('在这里填入自定义高度，部分模型需要修改'));
        $form->addInput($custom_height);

        // 自定义模型
        $custom_model = new Typecho_Widget_Helper_Form_Element_Text('custom_model', NULL, NULL, _t('自定义配置文件地址'), _t('在这里填入一个模型 JSON 配置文件地址，可供更换模型，不填则使用默认配置文件'));
        $form->addInput($custom_model);
    }

    /* 个人用户的配置方法 */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    /* 插件实现方法 */
    public static function header(){
        echo "<style>#pio{ left: 0; bottom: 0; z-index: 520; position: fixed; pointer-events: none; } @media screen and (max-width: 768px){ #pio{ width: 8em; } }</style>";
    }
    public static function footer(){
        $height = Typecho_Widget::widget('Widget_Options')->Plugin('Pio')->custom_height;
        $width = Typecho_Widget::widget('Widget_Options')->Plugin('Pio')->custom_width;

        if($height && $width){
            echo "<canvas id='pio' width='".$width."' height='".$height."'></canvas>";
        }
        else if($height){
            echo "<canvas id='pio' width='280' height='".$height."'></canvas>";
        }
        else if($width){
            echo "<canvas id='pio' width='".$width."' height='250'></canvas>";
        }
        else{
            echo "<canvas id='pio' width='280' height='250'></canvas>";
        }

        echo "<script src='" . Helper::options()->pluginUrl . "/Pio/l2d.js'></script>" . "\n";

        if(Typecho_Widget::widget('Widget_Options')->Plugin('Pio')->custom_model){
            echo "<script>loadlive2d('pio', '" . Typecho_Widget::widget('Widget_Options')->Plugin('Pio')->custom_model . "');</script>". "\n";
        }
        else{
            echo "<script>loadlive2d('pio', '" . Helper::options()->pluginUrl . "/Pio/model.json');</script>". "\n";
        }
    }
}