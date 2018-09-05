<?php 
    namespace libs;
    // 对发送邮箱进行封装
    class Email{
        // 进行三公一私的方法
        public $mail; //定义属性
        // 定义构造方法
        public function __construct()
        {   
            // 从配置文件中读取配置
            $config = config('email');
            // 设置邮件服务器账号
            $transpost = (new \Swift_SmtpTransport($config['host'],$config['port']))
                ->setUsername($config['name'])
                ->setPassword($config['pass']);
                // 创建发邮件对象
            $this->mail = new Swift_Mailer($transport);
        }
        // $to ['邮箱地址','名称']
        public function send($title,$content,$to){
            // 从配置文件中读取配置
            $config = config('email');
            // 创建邮件信息
            $message = new \Swift_Message();
            $message->setSubject($title) //标题
                    ->setForm([$config['from_email']=>$config['from_name']]) 
                    ->setTo([
                        $to[0],
                        $to[0]=>$to[1]
                    ])
                    ->setBody($content,'text/html');  //发送的内容格式
            // 判断如果是 调试模式 就写入文本中
            if($config['mode'] == 'debug'){
                // 获取邮件的所有信息
                $mess = $message->toString();
                // 把邮件内容记录到日志中 
                $log = new Log('email');
                $log->log( $mess ); 
            }else{
                // 发送邮件
                $this->mail->send($message);
            }
         }
    }
?>