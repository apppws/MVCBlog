<?php
    namespace libs;
    class Log{

        // 析构 一个传文件名
        public function __construct($filename)
        {
            // 打开日志文件
            $this->file = fopen(ROOT.'logs/'.$filename.'.log','a');
        }
        // 向日志文件追加内容
        public function log($count){
            // 获取当前的时间
            $date = date('Y-m-d H:i:s');
            //拼出日志内容的模式
            $m = $date . "\r\n";
            $m.=str_repeat('===',120)."\r\n";  //== 重复
            $m.=$count. "\r\n\r\n";
            fwrite($this->file,$m);  //写入文件 
        }
    }

?>