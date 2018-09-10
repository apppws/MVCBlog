<?php 
    namespace models;
    use PDO;
    class Order extends BaseModel{
        // 下订单
        public function create($money){
            // 调用那个算法
            $flake = new \libs\Snowflake(1023);
            // 预处理 插入数据
            $stmt =  self::$pdo->prepare("INSERT INTO orders(user_id,money,sn) VALUES(?,?,?)");
            // 执行
            $stmt->execute([
                $_SESSION['id'],
                $money,
                $flake->nextId(),
            ]);
        }
         // 搜索订单 并返回数据
         public function search()
         {     
            // 在 用户只能看自己的日志  
             $where = 'user_id='.$_SESSION['id'];

            // ****************排序
            // 默认的排序条件
            $odby = 'created_at';
             $odway = 'desc';
            // **************翻页
            // 获取每页条数
            $perpage = 10; 
            // 获取当前页   判断是否存在 如果存在就获取当前页 如果不是默认1
            $page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1; 
            // 计算初始值  当前页-1 * 每页条数
            $offset = ($page-1)*$perpage;

            
             // 翻页按钮
            // 第一获取 总的记录数
            $stmt = self::$pdo->prepare("SELECT COUNT(*) FROM orders WHERE $where");
            $stmt->execute();
            $count = $stmt->fetch( PDO::FETCH_COLUMN );

            // 第二步 总的页数  取整数并向上取整
            $pageCount = ceil($count/$perpage);
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
            
            // 预处理 sql
            $stmt = self::$pdo->prepare("SELECT * FROM orders WHERE $where ORDER BY $odby $odway LIMIT $offset,$perpage");
            // 执行 sql语句
            $stmt->execute();
            // 取数据
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // 加载列表页视图
            return [
                'pageBtn'=>$pageBtn,
                'data'=>$data,
            ];
        }

        // 添加方法取数据取订单信息
        public function findBySn($sn){
            // 预处理
            $stmt = self::$pdo->prepare("SELECT * FROM orders WHERE sn =?");
            // 执行
            $stmt->execute([$sn]);
            // 取数据
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // 更新订单信息 更新数据库
        public function setPaid($sn){
            // 预处理
            $stmt = self::$pdo->prepare("UPDATE orders SET status=1,pay_time=now() WHERE sn = ?");
            // 执行
            $stmt->execute([$sn]);
            
        }

    }

?>