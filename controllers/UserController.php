<?php 
    namespace controllers;
    // 控制器  
    // 第一步引入模型
    use models\User;
    use models\Order;
    class UserController
    {
        // 服务器添加接口
        public function money(){
            $user = new User;
            echo $user->getMoney();
        }
        // 显示充值页面
        public function charge(){
            view('users.charge');
        }
        // 执行充值页面
        public function docharge(){
            // 接收数据 生成订单
            $money = $_POST['money'];
            $model = new Order;
            $model->create($money);
            message('充值订单已经生成，请立即支付！',2,'/user/orders');    

        }
        // 订单的列表
        public function orders(){
            // new 模型
            $model = new Order;
            $data = $model-> search();
            // 加载视图
            view('users.order',$data);

        }
        // 退出 
        public  function  logout(){
            $_SESSION =[];
            die('退出成功');
        }
        // 显示登陆页面
        public function login(){
            view('users.login');
        }
        // 处理登录表单
        public function dologin(){
            // 第一接收表单
            $email = $_POST['email'];
            $password = md5($_POST['password']);
            // 第二调用数据 进行查询
            $user = new User;
            $dat = $user->login($email,$password);
            // 判断是否查询到数据
            if($dat){
                message('登录成功',2,'/blog/list');
            }else{
                message('账号或者密码错误',1,'/blog/login');
            }
        }

        // 注册页面的显示
        public function register(){
            view('users.register');
        }

        // 处理注册表单 
        public function store(){
            // 第一步 接收表单 
            $email = $_POST['email'];
            $password = md5($_POST['password']);
            // 第二步调用Models  插入数据库
            $user = new User;
            $res = $user->adduser($email,$password);
            // 判断这个是否插入数据成功 如果没有 提示 并die
            if(!$res){
                die('注册失败');
            }
            // 第三步 发送邮件
            // 从邮箱地址中取出名字
            $name = explode('@',$email);
            echo $email;
            // 构造收件人的地址  apppws@126.com   收件人就是 apppws
            $from = [$email,$name[0]];
            // var_dump($from);
            // 构造消息数组
            $message = [
                'title'=>'欢迎加入apppws邮箱文件',
                'content'=>'点击<a href="">点击激活</a>',
                'from'=>$from,
            ];
            // var_dump($message);
            // 把数组序列化(字符串)
            $message = json_encode($message);
            // var_dump($message);
            // 把message 放到 redis 队列中 打开 redis
            $redis = \libs\Redis::getInstance();
            // lpush 放数据
            $redis->lpush('email',$message);
            // 成功
            echo "ok";
        }
    }
?>