<?php
    namespace controllers;
    use libs\Email;
    class EmailController{
        // 发送邮件方法
        public function send(){
            // 开启 redis 
            $redis = new \Predis\Client([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 6379,
            ]);
            // 创建对象
            $email = new Email;
            // 设置php永远不超时
            ini_set('default_socket_timeout',-1);
            echo "发邮件启动成功 \r\n";
            // 循环对列表中取消息并发邮件
            while(true)
            {
                // 把redis 中的信息 取出来      0代表堵塞等待直到有消息之后向后执行代码
                $data = $redis->brpop('email',0);
                var_dump($data);
                // 取出消息之后反序列化 转成数组
                $message = json_decode($data[1],TRUE);
                // 发邮件
                $email->send($message['title'],$message['content'],$message['from']);
                echo "发送邮件成功！ 继续等待下一个..\r\n";
            }
        }
    }
?> 