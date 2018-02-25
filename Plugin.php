<?php
/**
 * 一个简易的 Live2D 插件，建立在 <a href="https://github.com/journey-ad/live2d_src">@Jad</a> 的项目上
 * 
 * @package Pio
 * @author Dreamer-Paul
 * @version 1.0.1
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
        $ppd = Helper::options()->pluginUrl;

        echo "<canvas id='pio' width='280' height='250'></canvas>";
        echo "<script src='" . $ppd . "/Pio/l2d.js'></script>" . "\n";
        
        if(Typecho_Widget::widget('Widget_Options')->Plugin('Pio')->custom_model){
            echo "<script>loadlive2d('pio', '" . Typecho_Widget::widget('Widget_Options')->Plugin('Pio')->custom_model . "');</script>". "\n";
        }
        else{
            echo "<script>loadlive2d('pio', '" . $ppd . "/Pio/model.json');</script>". "\n";
        }
    }
}