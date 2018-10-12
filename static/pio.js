/* ----

# Pio Plugin
# By: Dreamer-Paul
# Last Update: 2018.10.12

一个支持换模型的 Live2D 插件，供 Typecho 使用。

本代码为奇趣保罗原创，并遵守 MIT 开源协议。欢迎访问我的博客：https://paugram.com

---- */

var poster_girl = function (prop) {
    var current = {
        idol: 0,
        canvas: document.getElementById("pio"),
        body: document.getElementsByClassName("pio-container")[0],
        root: document.location.protocol +'//' + document.location.hostname +'/'
    };

    var elements = {
        home: current.body.getElementsByClassName("home")[0],
        skin: current.body.getElementsByClassName("skin")[0],
        info: current.body.getElementsByClassName("info")[0],
        close: current.body.getElementsByClassName("close")[0]
    };

    var dialog = document.createElement("div");
    dialog.className = "dialog";
    current.body.appendChild(dialog);

    /* - 方法 */
    var modules = {
        // 更换模型
        idol: function () {
            current.idol < (prop.model.length - 1) ? current.idol++ : current.idol = 0;
            return current.idol;
        },
        // 随机内容
        rand: function (arr) {
            return arr[Math.floor(Math.random() * arr.length + 1) - 1];
        },
        // 创建对话框方法
        render: function (text) {
            if(text.constructor === Array){
                dialog.innerText = modules.rand(text);
            }
            else if(text.constructor === String){
                dialog.innerText = text;
            }
            else{
                dialog.innerText = "输入内容出现问题了 X_X";
            }

            dialog.classList.add("active");

            clearTimeout(this.t);
            this.t = setTimeout(function () {
                dialog.classList.remove("active");
            }, 3000);
        },
        // 移除方法
        destroy: function () {
            current.body.parentNode.removeChild(current.body);
        }
    };

    /* - 提示操作 */
    var action = {
        // 欢迎
        welcome: function () {
            if(document.referrer !== "" && document.referrer.indexOf(current.root) === -1){
                var referrer = document.createElement('a');
                referrer.href = document.referrer;
                prop.content.welcome && prop.content.welcome[1] ? modules.render(prop.content.welcome[1].replace(/%t/, "“" + referrer.hostname + "”")) : modules.render("欢迎来自 “" + document.referrer + "” 的朋友！");
            }
            else{
                prop.content.welcome && prop.content.welcome[0] ? modules.render(prop.content.welcome[0]) : modules.render("欢迎来到保罗的小窝！");
            }
        },
        // 文章
        article: function () {
            if(prop.selector.articles){
                var a = document.querySelectorAll(prop.selector.articles), b;
                prop.content.article ? b = prop.content.article : b = "想阅读 %t 吗？";

                for(var i = 0; i < a.length; i++){
                    a[i].onmouseover = function () {
                        modules.render(b.replace(/%t/, "“" + this.innerText + "”"));
                    }
                }
            }
        },
        // 触摸
        touch: function () {
            if(prop.content.touch){
                current.canvas.onclick = function () {
                    modules.render(prop.content.touch);
                }
            }
            else{
                current.canvas.onclick = function () {
                    modules.render(["你在干什么？", "再摸我就报警了！", "HENTAI!", "你够了喔！"]);
                }
            }
        },
        // 右侧按钮
        buttons: function () {
            // 返回首页
            if(elements.home){
                elements.home.onclick = function () {
                    location.href = current.root;
                };
                elements.home.onmouseover = function () {
                    prop.content.home ? modules.render(prop.content.home) : modules.render("点击这里回到首页！");
                };
            }

            // 更换模型
            if(elements.skin){
                elements.skin.onclick = function () {
                    loadlive2d("pio", prop.model[modules.idol()]);
                    prop.content.skin && prop.content.skin[1] ? modules.render(prop.content.skin[1]) : modules.render("新衣服真漂亮~");
                };
                elements.skin.onmouseover = function () {
                    prop.content.skin && prop.content.skin[0] ? modules.render(prop.content.skin[0]) : modules.render("想看看我的新衣服吗？");
                };
            }

            // 关于我
            if(elements.info){
                elements.info.onclick = function () {
                    window.open("https://paugram.com/coding/add-poster-girl-with-plugin.html");
                };
                elements.info.onmouseover = function () {
                    modules.render("想了解更多关于我的信息吗？");
                };
            }

            // 关闭看板娘
            if(elements.close){
                elements.close.onclick = function () {
                    modules.destroy();
                };
                elements.close.onmouseover = function () {
                    prop.content.close ? modules.render(prop.content.close) : modules.render("QWQ 下次再见吧~");
                };

                document.cookie = "posterGirl=false;" + "path=/";
            }
        }
    };

    /* - 运行 */
    var begin = {
        static: function () {
            action.welcome(); action.article();
            current.body.classList.add("static");
        },
        fixed: function () {
            action.welcome(); action.article(); action.touch(); action.buttons();
        },
        draggable: function () {
            action.welcome(); action.article(); action.touch(); action.buttons();

            var body = current.body;
            body.onmousedown = function () {
                var location = {
                    x: event.clientX - this.offsetLeft,
                    y: event.clientY - this.offsetTop
                };

                function move(e) {
                    body.classList.add("active");
                    body.style.left = (event.clientX - location.x) + 'px';
                    body.style.top  = (event.clientY - location.y) + 'px';
                }

                document.addEventListener("mousemove", move);
                document.addEventListener("mouseup", function () {
                    body.classList.remove("active");
                    document.removeEventListener("mousemove", move);
                });
            };
        }
    };

    // 判断模式并运行
    switch (prop.mode){
        case "static": begin.static(); break;
        case "fixed":  begin.fixed(); break;
        case "draggable": begin.draggable(); break;
    }

    if(prop.hidden === true){ current.body.classList.add("hidden") }

    loadlive2d("pio", prop.model[0]);
};

// 请保留版权说明
if (window.console && window.console.log) {
    console.log("%c Pio %c https://paugram.com ","color: #fff; margin: 1em 0; padding: 5px 0; background: #673ab7;","margin: 1em 0; padding: 5px 0; background: #efefef;");
}