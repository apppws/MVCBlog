<?php
    namespace controllers;
    use models\Blog;
    class BlogController
    {
        // 发表日志方法
        public  function create(){
            // 加载发表日志视图
            view('blogs.create');
        }

        // 提交过来的方法
        public function store(){
            $blog = new Blog;
             $blog->insert([
                'title' => $_POST['title'],
                'content' => $_POST['content'],
                'is_show' => $_POST['is_show']
            ]);
            // var_dump($ccc);
            echo "插入数据成功";
        }

        // 日志列表 
         public function list()
         {     
            // ******************* 搜索
            //  全部数据
             $where = 1;
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
            

             $blog = new Blog;
             $blogs = $blog->get("SELECT * FROM lists WHERE $where ORDER BY $orderby $orderway");
            //  echo "<pre>";
            //  var_dump($where);
            // 加载列表页视图
            view('blogs.list',[
                'blogs'=>$blogs
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

    }