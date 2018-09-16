<?php
    namespace controllers;
    use models\Blog;
    use PDO;
    use Illuminate\Support\Facades\Redirect;
    class BlogController
    {
        // 点赞用户
        public function zan_list(){
            $id = $_GET['id'];

            // 获取这个日志所有点赞的用户
            $model = new \models\Blog;
            $data = $model->zanList($id);

            // 转成 JSON 返回 
            echo json_encode([
                'status_code' => 200,
                'data' => $data,
            ]);

        }
        // 点赞功能
        public function zan(){
            $id = $_GET['id'];
            // 判断是否登录
            if(!isset($_SESSION['id'])){
                echo json_encode([
                    'status_code'=>'403',
                    'message'=>'还没登录哦'
                ]);
                exit;
            }
            // 点赞
            $blog = new \models\Blog;
            $res = $blog->praise($id);
            if($res){
                echo json_encode([
                    'status_code'=>'200',
                ]);
                exit;
            }else{
                echo json_encode([
                    'status_code'=>'403',
                    'message'=>"你已经点过赞了哦"   
                ]);
                exit;
            }
        }
        // 发表日志方法
        public  function create(){
            // 加载发表日志视图
            view('blogs.create');
        }

        // 提交过来的方法
        public function store(){
                $title  = $_POST['title'];
                $content =$_POST['content'];
                $is_show = $_POST['is_show'];
                $blog = new Blog;
                $blog->add($title,$content,$is_show);
                // 跳转 
                message('发表成功！',2,'/blog/list');
        }

        // 日志列表 
         public function list()
         {     
            // ******************* 搜索
            //  全部数据
            // 在 用户只能看自己的日志  
             $where = 'user_id='.$_SESSION['id'];
            // 判断关键字 是否存在 是否为空
            if(isset($_GET['keyword']) && $_GET['keyword']){
                // 拼接查询字段的sql语句
                $where .= " AND(title LIKE '%{$_GET['keyword']}%')  OR content LIKE '%{$_GET['keyword']}%'";
            }
            // 判断发表时间 是否存在 是否为空
            if(isset($_GET['start_date']) && $_GET['start_date']){
                // 拼接查询字段的sql语句
                $where .= " AND created_at >='{$_GET['start_date']}'";
            }
            // 判断修改时间 是否存现 是否为空 
            if(isset($_GET['end_date']) && $_GET['end_date']){
                // 拼接查询字段的sql语句
                $where .= " AND created_at <='{$_GET['end_date']}'";
            }
            // 判断是否显示
            if(isset($_GET['is_show']) && $_GET['is_show']){
                $where .= " AND is_show={$_GET['is_show']}";
            }

            // ****************排序
            // 默认的排序条件
            $orderby = 'created_at';
            //排序方式 倒序 
            $orderway = 'desc';
            // 设置排序的字段 条件
            if(isset($_GET['order_by']) && $_GET['order_by']=='display'){
                $orderby = 'display';
            }
            // 设置排序的方式
            if(isset($_GET['order_way']) && $_GET['order_way']=='asc'){
                $orderway = 'asc';
            }

            // **************翻页
            // 获取每页条数
            $perpage = 10; 
            // 获取当前页   判断是否存在 如果存在就获取当前页 如果不是默认1
            $page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1; 
            // 计算初始值  当前页-1 * 每页条数
            $offset = ($page-1)*$perpage;
            // 拼出limit
            $limit = $offset.','.$perpage;

             $blog = new Blog;
             $blogs = $blog->get("SELECT * FROM lists WHERE $where ORDER BY $orderby $orderway LIMIT $limit");

             // 翻页按钮
            // 第一获取 总的记录数
             $totalPage = $blog->count($where);
            // 第二步 总的页数  取整数并向上取整
            $pageCount = ceil($totalPage/$perpage);
            //  var_dump($pageCount);
            // 第三步制作按钮
            $pageBtn = '';
                for($i=1;$i<=$pageCount;$i++)
                {   
                    // 加样式
                    if($i == $page){
                        $class = "class='active'";
                    }
                    else{
                        $class='';
                    }
                    $pageBtn.= "<a $class href='?page={$i}'>{$i}</a>";
                }
            //  echo "<pre>"; 可以格式化
            //  var_dump($where);
            // 加载列表页视图
            view('blogs.list',[
                'blogs'=>$blogs,
                'pagebtn' =>$pageBtn
            ]);
        }
        

        // 模拟数据
        public function Mock()
        {
            $user =  new Blog;
            $user->exec('TRUNCATE TABLE lists');
            // 循环100条
            for($i = 0;$i<100;$i++){
                $user->insert([
                    'title' => $this->getchar(10,100),
                    'content' => $this->getChar(100,300),
                    'display'=>rand(5,1000),
                    'is_show'=>rand(0,1),
                    'created_at' =>date('Y-m-d H:i:s',rand(1000000000,1543233112)),
                    'updated_at' =>date('Y-m-d H:i:s',rand(1000000000,1543233112)),
                ]);
                
            }
            echo "成功！";  
        }

        // 随机生成汉字
        private function getChar($num)  // $num为生成汉字的数量
        {
            $b = '';
            for ($i=0; $i<$num; $i++) {
                // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
                $a = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
                // 转码
                $b .= iconv('GB2312', 'UTF-8', $a);
            }
            return $b;
        }

        // 修改日志列表
        function edit(){
            // 获取id
            $id = $_GET['id'];
            $mind = new Blog;
            $blog = $mind->find($id);
            // echo '<pre>';
            // var_dump($blog);
            // 加载到修改页面
            view('blogs.edit',[
                'blog' => $blog
            ]);
        }

        // 处理修改列表
        function update(){
            // 先获取id
            $title = $_POST['title'];
            $content = $_POST['content'];
            $is_show = $_POST['is_show'];
            $id = $_POST['id'];
            // 调用模型
            $blog = new Blog;
            $blog->update($title,$content,$is_show,$id);

            // 判断如果日志时公开的 就生成静态页面  
            if($is_show==1){
                $blog->makeHtml($id);
            }else{
                $blog->deletHtml($id);
            }

            // 跳转到列表页
            message('修改成功！', 2, '/blog/list');

           
        }

        /**
         * URL跳转
         * @param string $url 跳转地址
         * @param int $time 跳转延时(单位:秒)
         * @param string $msg 提示语
         */
        function redirect($url, $time = 0, $msg = '') {
            $url = str_replace(array("\n", "\r"), '', $url); // 多行URL地址支持
            if (empty($msg)) {
                $msg = "系统将在 {$time}秒 之后自动跳转到 {$url} ！";
            }
            if (headers_sent()) {
                $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
                if ($time != 0) {
                    $str .= $msg;
                }
                exit($str);
            } else {
                if (0 === $time) {
                    header("Location: " . $url);
                } else {
                    header("Content-type: text/html; charset=utf-8");
                    header("refresh:{$time};url={$url}");
                    echo($msg);
                }
                exit();
            }
        }

        // 删除页面
        function deletes(){
            // 接收id
            $id = $_POST['id'];
            // 调用模型  
            $del =  new Blog;
            $del->delete($id);
             // 静态页删除掉
                $blog->deleteHtml($id);

                message('删除成功',2,'/blog/list');

        }
        // 显示私有日志
            public function content()
            {
                // 1. 接收ID，并取出日志信息
                $id = $_GET['id'];
                $model = new Blog;
                $blog = $model->find($id);

                // 2. 判断这个日志是不是我的日志
                if($_SESSION['id'] != $blog['user_id'])
                    die('无权访问！');

                // 3. 加载视图
                view('blogs.content', [
                    'blog' => $blog,
                ]);

            }
        // 实现日志列表静态OB模型
        function blog_to_html(){

            // 第一步连接数据库
            $blog = new Blog;
            $blogs = $blog->get("SELECT * FROM lists");
            // 第二步开始缓存
            // 1）开启ob
            ob_start();
            // 2)生成静态页面
            foreach($blogs as $v){
                // 加载视图  在页面中 把blogs 传到页面中
                view('blogs.content',[
                    'blog'=>$v
                ]);
                // 取出缓存区的内容
                $str = ob_get_contents();
                // 生成静态页面
                file_put_contents(ROOT.'public/contents/'.$v['id'].'.html',$str);
                // 清除缓存区
                ob_clean();
            }
        }

        // 获取日志的浏览量
        public function display(){
            // 先获取id
            $id = (int)$_GET['id'];
            // var_dump($id);
            $blog = new Blog;
            // var_dump($blog);
            $display =  $blog->getDisplay($id);
            // 返回多个数据时必须要用 JSON
            echo json_encode([
                'display' => $display,
                'email' => isset($_SESSION['email']) ? $_SESSION['email'] : '',
                'money' => $_SESSION['money'],
                 'headimg' => $_SESSION['headimg']=='' ? '/images/headimg.jpg' : $_SESSION['headimg'],
            ]);
        }
        // 调用模型的 
        public function displaydb(){
            $blog = new Blog;
            $blog->displayTodb();
        }
    }