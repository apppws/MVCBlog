<?php 
    namespace controllers;
    // 控制器  
    // 第一步引入模型
    use models\User;
    class UserController
    {
        public function hello()
        {
            // 第二步 从模型中取数据
            $user = new User;
            $name = $user->getName();

            // 第三步 加载视图
            view('users.hello',[
                'name'=>$name
                ]);
        }
    }
?>