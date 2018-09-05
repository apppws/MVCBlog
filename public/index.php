<?php
    //主入口
         // 第一步先定义一个常量   为了能加载这些文件  项目根目录  __FILE__代表当前文件是绝对路径
          define('ROOT', dirname(__FILE__). '/../');
        
        //   引入 composer 自动加载
          require(ROOT.'vendor/autoload.php');

         // 第二步实现自动加载
            function autoload($class)
            {
                // var_dump($class);
                    // 引入文件 并拼接  形成搜索
                    $path = str_replace('\\', '/', $class);
                    require(ROOT . $path . '.php');
            }
                // 自动加载的函数
            spl_autoload_register('autoload');

        // var_dump($_SERVER);
        // dir;
        // 第三步解析路由
            // 判断这个服务器是否获取到这个PATH_INFO
        if (isset($_SERVER['PATH_INFO'])) {
            $pathInfo = $_SERVER['PATH_INFO'];
            // var_dump($pathInfo);
            // die;
            // 根据 / 转成数组
            $pathInfo = explode('/', $pathInfo);
            // var_dump($pathInfo);
            // 得到控制器名和方法名 ：
            $controller = ucfirst($pathInfo[1]) . 'Controller';
            // var_dump($controller);
            $action = $pathInfo[2];
            // var_dump($action);
        } else {
            // 默认控制器和方法
            $controller = 'IndexController';
            $action = 'index';
        }

        // 为控制器添加命名空间
        $fullController = 'controllers\\' . $controller;
        // var_dump($fullController);
        // 创建控制器对象
        $_C = new $fullController;
        // 调用方法
        $_C->$action();


    // view 函数
        function view($dirfile, $data = [])
        {
                // 判断是否传数据
            if ($data) {
                    // 解压数组为变量
                extract($data);  //不用 在视图文件上就不用 $data['变量名']
            }
            // var_dump($data);
            // var_dump($dirfile);
                // 拼接视图文件的路径
            $path = str_replace('.', '/', $dirfile) . ".html";
            // var_dump($path);
                // 在加载视图
            require(ROOT . 'View/' . $path);
        }

        // 配置文件的方法
        function config($name){
            // 获取配置文件（特点：无论调用多次，只包含一次配置文件）
            // 静态局部变量：函数执行结束，也不会销毁，一直存在到整个脚本结束
            // 普通局部亦是：函数执行完就销毁了
            static $config = null;
            if($config==null){
                // 引入配置文件
                $config = require(ROOT.'config.php');
            }
            // 返回name
            return $config[$name];
        }

?>