<?php

    // redis 的汇总 封装   三私一共
    namespace libs;
    class Redis{
        
        private static $redis = null;
        private function __clone(){}
        // 私有构造方法
        private function __construct(){}
        // 公有
        public static function getInstance(){
            if(self::$redis==null){
                // 放到队列中 连接 Redis 
                self::$redis = new \Predis\Client([
                    'scheme' => 'tcp',
                    'host'   => '127.0.0.1',
                    'port'   => 6379,
                ]);
            }
            // 直接返回已有的redis对象
            return self::$redis;
        }

    }
    

?>