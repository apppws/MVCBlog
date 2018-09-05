<?php

    namespace models;
    // 模型 连接数据库 获取数据
    class User extends BaseModel
    {
        public $tableName = 'users';
        public function getName(){
            return "tom";
        }

        // 传两个参数  email / password  并插入数据库
        public function adduser($email,$password){
            // 预定义 
            $stmt = self::$pdo->prepare("INSERT INTO {$this->tableName} (email,password) VAlUES(?,?)");
            // var_dump($stmt);
            return $stmt->execute([
                $email,
                $password,
            ]);
        
        }

    }

?>