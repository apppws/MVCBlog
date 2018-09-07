<?php
// 使用redis 保存SESSION  
ini_set("session.save_handler","redis"); 
// 设置redis服务器的地址和端口
ini_set('session.save_path',"tcp://127.0.0.1:6379?database=3");
// 设置 session 10分钟过期
// ini_set("session.gc_maxlifetime",600);
// 并开启session 
session_start();
// 如果用户一post 方式访问网站 需要令牌    在使用 支付宝的时候  先注释
// if($_SERVER['REQUEST_METHOD']=='POST'){
//     if(!isset($_POST['_token'])){
//         die('违法操作');
//     }
//     if($_POST['_token']!= $_SESSION['token']){
//         die('违法操作');
//     }
// }
    //主入口
         // 第一步先定义一个常量   为了能加载这些文件  项目根目录  __FILE__代表当前文件是绝对路径
          define('ROOT', dirname(__FILE__). '/../');
        
        //   引入 composer 自动加载
          require(ROOT.'vendor/autoload.php');
        // 自动加载的函数
        spl_autoload_register('autoload');
         // 第二步实现自动加载
            function autoload($class)
            {
                     var_dump($class);
                    // 引入文件 并拼接  形成搜索
                    $path = str_replace('\\', '/', $class);
                    require(ROOT . $path . '.php');
            }
                

        // var_dump($_SERVER);
        // dir;
        // 第三步解析路由
            // 判断这个服务器是否获取到这个PATH_INFO
        if(php_sapi_name() == 'cli')
        {
            $controller = ucfirst($argv[1]) . 'Controller';
            $action = $argv[2];
        }else{
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

        // 跳转函数的 
        // 跳转到任意一页
        function redirect($url){
            // 跳转
            header('location:'.$url);
            exit;
        }
        // 跳回上一个页面
        function back(){
            redirect($_SERVER['HTTP_REFERER']);
        }

        // 提示消息的函数  0 alert 1:显示单独的页面 2：在下一个页面中显示
        // 参数：  $message:提示消息 $type: 函数的方式 $url: 跳转的地址 $seconds=5(只有type=1 时才会跳转)
        function message($message,$type,$url,$seconds=5){
            // 判断是什么类型
            if($type==0 ){
                // 用 alert 方式 js
                echo "<script>alert('{$message}');location.href='{$url}'</script>";
                exit;
            }else if($type==1){
                // 使用显示单独的页面
                // 加载视图文件 消息
                view('common.success',[
                    'message' => $message,
                    'url' => $url,
                    'seconds' => $seconds
                ]);
            }else if($type==2){
                // 把消息保存到session 中 
                $_SESSION['_MESS_'] = $message;
                // 跳转到下一个页面
                redirect($url);
            }
        }

        // CSRF 防御
        function csrf(){
            if(!isset($_SESSION['token'])){
                // 生成一个随机的字符串
                $token = md5( rand(1,99999).microtime() );
                $_SESSION['token'] = $token;
            }
            return $_SESSION['token'];
        }

        // 封装一个 令牌 隐藏域
        function csrf_input(){
            // 判断session 中是否有token 没有就添加 
            // 有就直接获取session中的token值
            $csrf = isset($_SESSION['token']) ? $_SESSION['token'] : csrf();
            // 输出给页面
            echo "<input type='hidden' name='_token' value='{$csrf}'>";
        }


?>