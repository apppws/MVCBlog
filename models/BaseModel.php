<?php
    namespace models;
    use PDO;
    class BaseModel
    {
        public static $pdo = null;
        private $_dbname = 'blog';
        private $_host = '127.0.0.1';
        private $_user = 'root';
        private $_password = '';

        public function __construct()
        {
            /*
                写 pdo 设置一个静态的变量  
                用的时候判断 self:pdo === null  单例模式   这样能加快 访问  
                不用 new 一次 就连接一次数据库
            */
            if(self::$pdo === null)
            {
                // 生成 PDO 对象，连接数据库
                self::$pdo = new PDO('mysql:dbname='.$this->_dbname.';host='.$this->_host, 
                                    $this->_user, 
                                    $this->_password);
                // 设置编码
                self::$pdo->exec('SET NAMES utf8');
            }
        }
        // 插入数据
        function insert($data){
            // var_dump($data);
            // 取出数组中的键 构造新数组
            $keys = array_keys($data);
            // 取出数组中值 构造新数组
            $value = array_values($data);
            // 拼出值的字符串
            $keyString = implode(',',$keys);
            $valueString = implode("','",$value);
            // 拼出sql语句
            $sql = "INSERT INTO {$this->tableName} ($keyString) VALUES ('$valueString')";
            // var_dump($sql);
            // 执行sql 语句
            $this->exec($sql);
            // 返回插入数据记录id
            return self::$pdo->lastInsertId();
        }
        // 执行sql语句
        public function exec($sql)
        {
            $ret = self::$pdo->exec($sql);
            if($ret === false)
            {
                echo $sql , '<hr>';
                // 错误信息 
                $error = self::$pdo->errorInfo();
                die($error[2]);
            }
            return $ret;
        }
        
        
        // 查询数据的方法
            //1) 错误信息的提示 查询
        function query($sql)
        {
            $ret = self::$pdo->query($sql);
            if($ret === false)
            {
                echo $sql , '<hr>';
                // 错误信息 
                $error = self::$pdo->errorInfo();
                die($error[2]);
            }
             // 设置返回数组的结构为关联数组
             $ret->setFetchMode(PDO::FETCH_ASSOC);
            return $ret;
        }
            // 2）获取所有数据
        function get($sql)
        {
            $stmt = $this->query($sql);
            return  $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
            // 2)获取一条数据
        function getRow($sql)
        {
            $stmt = $this->query($sql);
            return $stmt->fetch();
        }
            // 3）集中指定列的值
        function getOne($sql)
        {
            $stmt = $this->query($sql);
            return $stmt->fetchColumn();
        }
            // 4)查询记录数
        function count($where)
        {
            $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE $where";
            // var_dump($sql);
            return $this->getone($sql);
        }
            


    }