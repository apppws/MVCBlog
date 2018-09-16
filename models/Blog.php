<?php
    namespace models;
    // use models\BaseModel;
    use PDO;
    class Blog extends BaseModel
    {
        public $tableName = 'lists';
        // 取出点赞过这个日志的用户信息
        public function zanList($id)
        {
            $sql = 'SELECT b.id,b.email,b.face
                    FROM praise a
                    LEFT JOIN users b ON a.user_id = b.id
                    WHERE a.list_id=?';                   
            // var_dump($sql);
            $stmt = self::$pdo->prepare($sql);

            $stmt->execute([
                $id
            ]);

            return $stmt->fetchAll( PDO::FETCH_ASSOC );
        }
        // 点赞
        function praise($id){
            // 判断是否点过赞
            $stmt = self::$pdo->prepare("SELECT COUNT(*) FROM praise WHERE user_id =? AND list_id=?");
            $stmt->execute([
                $_SESSION['id'],
                $id
            ]);
            $count = $stmt->fetch(PDO::FETCH_COLUMN);
            // 判断数量是否==1  不等于就返回false
            if($count==1){
                return false;
            }
            // 点赞
            $stmt = self::$pdo->prepare("INSERT INTO praise(user_id,list_id) VALUES(?,?)");
            // var_dump($stmt);
            $res = $stmt->execute([
                $_SESSION['id'],
                $id
            ]);
            // 更新点赞次数
            if($res){
                $stmt = self::$pdo->prepare("UPDATE lists SET zan=zan+1  WHERE id = ?");
                $stmt->execute([
                    $id,
                ]);
            }
            // 返回数量
            return $res;

        }

        // 修改数据
        function update($title,$content,$is_show,$id){
            // 连接sql 语句
            $stmt = self::$pdo->prepare("UPDATE {$this->tableName} SET title=?,content=?,is_show=? WHERE id=?");
            // 执行sql 语句
            $upd = $stmt->execute([
                $title,
                $content,
                $is_show,
                $id,
            ]);

        }

        // 删除列表数据   user_id 的作用是 只能删除自己的日志
        function delete($id){
            // 连接sql语句
            $stmt = self::$pdo->prepare("DELETE FROM {$this->tableName} WHERE id =? AND user_id =?");
            // 执行sql语句
            $del = $stmt->execute([
                $id,
                $_SESSION['id']
            ]);
        }

        // 添加发表日志的方法
        public function add($title,$content,$is_show){
            // 预处理
            $stmt = self::$pdo->prepare("INSERT INTO {$this->tableName} (title,content,is_show,user_id) VALUES(?,?,?,?)");
            $res = $stmt->execute([
                $title,
                $content,
                $is_show,
                $_SESSION['id'],
            ]);
            // 判断是否插入成功 
            if(!$res){
                echo "插入失败！";
                $error = $stmt->errorInfo();
                echo "<pre>";
                var_dump($error);
                exit;
            }
            // 返回新插入的记录ID
            return self::$pdo->lastInsertId();
        }

        // 查询数据
        function find($id)
        {
             // 取出日志的id
                 $stmt = self::$pdo->prepare("SELECT * FROM lists where id=?");
                 $stmt->execute([
                     $id,
                 ]);
                 return $stmt->fetch();
        }

        // 获取日志的浏览量
       public function getDisplay($id){
            // 使用日志ID拼出键名
            $key = "blog-{$id}";
            // var_dump($key);
            // 连接 Redis 
            $redis = \libs\Redis::getInstance();
            
            // var_dump(self::$pdo);
            // 判断 hash 中是否有这个键  如果有就操作内存 没有就取数据
            // 如果我查的 blog-4 如果有取数据 没有就添加
            if($redis->hexists('blog_display',$key)){
                // 累加 并且 返回添加完之后的值
                $newNum = $redis->hincrby('blog_display', $key, 1);
                echo $newNum;
                return $newNum;
            }else{
                // 从数据中取出浏览量
                $stmt = self::$pdo->prepare('SELECT display FROM lists WHERE id = ?');
                $stmt->execute([$id]);
                
                $display = $stmt->fetch( PDO::FETCH_COLUMN );
                
                $display++;
                // var_dump($display);
                // 保存到redis
                $redis->hset('blog_display',$key,$display);
                return $display;
            }
        }
        // 获取到 redis 的display 保存到数据库
        public function displayTodb(){
            // 连接 Redis 
            $redis = \libs\Redis::getInstance();
            // 把数据返回 整个数据
            $data = $redis->hgetall('blog_display');
            // echo "<pre>";
            // var_dump($data);
            // 循环更新到数据库中
            foreach($data as $k=>$v){
                // 因为保存的 id 是 blog-1...  
                $id = str_replace('blog-','',$k);
                // var_dump($id);
                // 更新数据库
                $sql = "UPDATE lists SET display = {$v} WHERE id = {$id}";
                // var_dump($sql);
                self::$pdo->exec($sql);
            }

        }

        // 为某一个日志生成一个静态页面  参数 日志的id
        public function makeHtml($id){
            $blog = $this->find($id);
            // 使用缓存 
            ob_start();
            // 加载视图到缓存区
            view('blog.content',[
                'blog'=>$blog,
            ]);
            // 从缓存区取出数据 并清除缓存
            $str = ob_end_clean();  //数据
            // var_dump($str);
            // 取出数据生成静态页面并拼接路径 保存
            file_put_contents(ROOT.'public/contents/'.$id.'.html',$str);
           
        }

        // 删除静态页面
        public function deletHtml($id){
            // 加@ 的原因是  有删除的文件就删除 没有就不删除  不会报错
            @unlink(ROOT.'public/contents/'.$id.'/html');
        }
    }

  ?> 