<?php

    namespace models;
    // 模型 连接数据库 获取数据
    class User extends BaseModel
    {
        public $tableName = 'users';
        public function getName(){
            return "tom";
        }

        //注册 传两个参数  email / password  并插入数据库   
        public function adduser($email,$password){
            // 预定义 
            $stmt = self::$pdo->prepare("INSERT INTO {$this->tableName} (email,password) VAlUES(?,?)");
            // var_dump($stmt);
            return $stmt->execute([
                $email,
                $password,
            ]);
        
        }
        // 登录 查找数据
        public function login($email,$password){
            // 预处理
             $stmt = self::$pdo->prepare("SELECT * FROM {$this->tableName} WHERE email= ? AND password =?"); 
            // 执行并传值
            $stmt->execute([
                $email,
                $password,
            ]);
            $data = $stmt->fetch();
            // 判断是否查询成功
            if($data){
                //如果查询到了  就保存到session中  返回1
                $_SESSION['id'] = $data['id'];
                $_SESSION['email'] = $data['email'];

                return true;
            }else{
                return false;
            }
        }

    }

?>