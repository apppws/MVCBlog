<?php
    namespace models;
    // use models\BaseModel;
    use PDO;
    class Blog extends BaseModel
    {
        public $tableName = 'lists';

        // 获取日志的浏览量
       public function getDisplay($id){
            // 使用日志ID拼出键名
            $key = "blog-{$id}";
            // var_dump($key);
            // 连接 Redis 
            $redis = new \Predis\Client([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 6379,
            ]);
            
            // var_dump(self::$pdo);
            // 判断 hash 中是否有这个键  如果有就操作内存 没有就取数据
            // 如果我查的 blog-4 如果有取数据 没有就添加
            if($redis->hexists('blog_display',$key)){
                // 累加 并且 返回添加完之后的值
                $newNum = $redis->hincrby('blog_displays', $key, 1);
                return $newNum;
            }else{
                // 从数据中取出浏览量
                $stmt = self::$pdo->prepare('SELECT display FROM lists WHERE id = ?');
                $stmt->execute([$id]);
                
                $display = $stmt->fetch( PDO::FETCH_COLUMN );
               
                $display++;
                // 保存到redis
                $redis->hset('blog_diaplay',$key,$display);
                return $display;
            }
        }
        // 获取到 redis 的display 保存到数据库
        public function displayTodb(){
            // 连接 Redis 
            $redis = new \Predis\Client([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 6379,
            ]);
            // 把数据返回 整个数据
            $data = $redis->hgetall('blog_displays');
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
    }

  ?> 