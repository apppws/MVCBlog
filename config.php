<?php
    // 配置文件
    return [
        'redis' => [
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ],
        'db' => [
            'host' => '127.0.0.1',
            'dbname' => 'blog',
            'user' => 'root',
            'pass' => '123456',
            'charset' => 'utf8',
        ],
        'email' => [
            'port' => 25,
            'host' => 'smtp.126.com',
            'name' => 'apppws@126.com', 
            'pass' => '965322pws',
            'from_email' => 'apppws@126.com',
            'from_name' => '彭~~',
            'mode' => 'ceshi',
        ]
    ]
?>