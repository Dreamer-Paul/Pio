# Pio

这是我（萧叶轩）的魔改版 Pio，非魔改版可以看原作者的仓库或者本仓库的 pr 分支

主要魔改点：

1. 增加返回到顶部按钮
2. 调整按钮栏高度
3. 调整消息框高度
4. 添加按钮的 开启/隐藏
5. 防止消息内容过多，进行截取前 30 个字符

现在你可以通过配置文件自定义显示哪些按钮，例如：

```js

var pio = new Paul_Pio({
    "mode": "static",
    "hidden": false,
    "content": {
        "welcome": "欢迎来到保罗的小窝"
    },
    "night": "single.night()",
    "model": ["static/pio/model.json"],
    "tips": true
});

```

修改为

```js

var pio = new Paul_Pio({
    "mode": "static",
    "hidden": false,
    "content": {
        "welcome": "欢迎来到保罗的小窝"
    },
    "night": "single.night()",
    "button": {
        totop: true,
        home: true,
        skin: true,
        info: true,
        night: true,
        close: true
    }
    "model": ["static/pio/model.json"],
    "tips": true
});

```

+ totop：返回到顶部
+ home：返回到主页
+ skin：更换皮肤
+ info：作者信息
+ night：夜间模式
+ close：关闭

为了简化代码， **值为 true 时是不显示按钮**，并且缺省时为显示按钮。

魔改版基本兼容原版，不需要过多配置。