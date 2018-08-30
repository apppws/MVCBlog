<?php
    namespace models;
    use PDO;
    class BaseModel
    {
        private static $_pdo = null;
        private $_dbname = 'blog';
        private $_host = '127.0.0.1';
        private $_user = 'root';
        private $_password = '';

        public function __construct()
        {
            if(self::$_pdo === null)
            {
                // 生成 PDO 对象，连接数据库
                self::$_pdo = new PDO('mysql:dbname='.$this->_dbname.';host='.$this->_host, 
                                    $this->_user, 
                                    $this->_password);
                // 设置编码
                self::$_pdo->exec('SET NAMES utf8');
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
            return self::$_pdo->lastInsertId();
        }
        // 执行sql语句
        public function exec($sql)
        {
            $ret = self::$_pdo->exec($sql);
            if($ret === false)
            {
                echo $sql , '<hr>';
                $error = self::$_pdo->errorInfo();
                die($error[2]);
            }
            return $ret;
        }
    }