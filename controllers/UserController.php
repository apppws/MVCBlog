<?php 
    namespace controllers;
    // 控制器  
    // 第一步引入模型
    use models\User;
    use models\Order;
    class UserController
    {
        // 显示批量上传
        public function filebig(){
            view('users.filebig');
        }
        // 处理大图片的 方法  ajax 到的服务器
        public function uploadall(){
            // 接收数据
            $count = $_POST['count'];
            $i = $_POST['i'];
            $size = $_POST['size'];
            $img = $_FILES['img'];
            $name = 'big_img_'.$_POST['img_name'];
            // 把每一个分片保存到服务器中
            move_uploaded_file($img['tmp_name'],ROOT.'tmp/'.$i);
            // 保存到 redis中
            $redis = \libs\Redis::getInstance();
            // 每上传一张图片  就把redis中 id+1
            $co = $redis->incr($name);
            //如果上传+的数量 等于总的数量就加载完成
            if($co == $count){
                // 合并所有的图片
                // 创建以追加的方式打开最终的文件  文件
                $fp = fopen(ROOT.'public/uploads/big/'.$name.'.png','a');
                // 在循环所有的分片
                for($i=0;$i<$count;$i++){
                    // 读取第i个元素文件并写入到大文件中
                    fwrite($fp,file_get_contents(ROOT.'tmp/'.$i));
                    // 在删除第i的临时文件
                    unlink(ROOT.'tmp/'.$i); 
                }
                // 关闭文件
                fclose($fp);
                // 在从redis中删除对应的编号这个变量
                $redis->del($name);
            }

        }
        // 上传相册
        public function arrayfile(){
            // 加载视图
            view('users.arrayfile');
        }
        // 处理相册表单
        public function doarrayfile(){
            // echo '<pre>';
            // var_dump($_FILES['image']);
            // 第一步先创建根目录
            $root = ROOT.'public/uploads/';
            //用日期创建
            $date = date('Y-m-d');
            // 判断这个目录是否存在
            if(!is_dir($root.'/'.$date)){
                // 不存在就创建
                mkdir($root.'/'.$date);
            }
            // 循环上传数5张数组
            foreach($_FILES['image']['name'] as $k=>$v){
                // 生成唯一的name
                $name = md5(time().rand(1,99999));
                // 获取文件的后缀
                $ext = strrchr($v,'.');
                // 拼接完整的 
                $name = $name.$ext;
                // var_dump($_FILES['image']['tmp_name'][$k]);
                // 移动到指定的目录
                move_uploaded_file($_FILES['image']['tmp_name'][$k],$root.$date.'/'.$name);
                echo $root.$date.'/'.$name;
            }
        }
        // 显示上传头像 
        public function headimg(){

            // 加载视图
            view('users.headimg');
        }
        // 接收上传的头像
        public function upheadimg(){
            // 第一步先创建根目录
            $root = ROOT.'public/uploads';
            // 用日期创建二级目录
            $date = date('Y-m-d');
            // 判断是否有这个目录  如果没有就创建 
            if(!is_dir($root.'/'.$date)){
                // 创建目录
                mkdir($root.'/'.$date,0777);
            }
            // 获取文件扩展名  
            $ext = strchr($_FILES['image']['name'],'.');  
            // var_dump($ext);
            // 生成唯一的文件名
            $name = md5(time().rand(1,99999));
            // var_dump($name);
            // 保存完整文件名  //../public/uploads/2018-09-13/4207400d328db0ed5cf093d963394456.jpg"
            $fullName = $root.'/'.$date.'/'.$name.$ext;
            // var_dump($fullName);
            // 保存到目录中
            move_uploaded_file($_FILES['image']['tmp_name'],$fullName);

        }
        // 查询订单的接口
        public function orderStatus(){
            $sn = $_GET['sn'];
            $model = new Order;
            $info = $model->findBySn($sn);
            echo $info['status'];
        }
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