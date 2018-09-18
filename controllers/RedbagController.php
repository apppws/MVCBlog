<?php
namespace controllers;

use function GuzzleHttp\json_encode;

class RedbagController{
    // 显示抢红包页面
    public function rob_view(){
        view('redbag.rob');
    }
    // 初始化
    public function init(){
        $redis = \libs\Redis::getInstance();
        // 初始化数据
        $redis->set('redbag_stock',20);
        // 初始化空的集合
        $key = 'redbag_'.date('Ymd');   //k值
        // echo $key;
        // 测试并添加
        $redis->sadd($key,'-1');
        // 设置过期时间
        $redis->expire($key,3600);
    }

    // 监听队列，当有新的数据时就生成订单
    public function makeOrder(){
        $redis = \libs\Redis::getInstance();
        // 调用模型
        $model = new \models\Redbag;
        // 设置socket 永不超时
        ini_set('default_socket_timeout',-1);

        echo "开始了...";
        // 循环监听列表
        while(true){
            // 从队列中取数据 设置永不超时
            $data = $redis->brpop('redbag_count',0);
            // var_dump($data);
            //处理数据
            $userId = $data[1];
            // 下订单
            $model->create($userId);
            echo "有人抢了红包";
        }
    }
    // 抢红包
    public function rob(){
        // 1 判断有没有登录
        if(!isset($_SESSION['id'])){
            echo json_encode([
                'status_code'=>'401',
                'message'=>'未登录'
            ]);
            exit;
        }
        // 2 判断是否是 22 点到 00 点
        if(date("H")<6 || date("H") > 24){
            echo json_encode([
                'status_code'=>'403',
                'message'=>'时间段不允许'
            ]);
            exit;
        }
        // 3判断今天是否已经抢过
        $key = 'redbag_'.date('Ymd');
        $redis = \libs\Redis::getInstance();
        $exits = $redis->sismember($key,$_SESSION['id']);
        if($exits){
            echo json_encode([
                'status_code'=>'403',
                'message'=>'今天已经抢过了'
            ]);
            exit;
        }
        // 4 减少库存量 并返回减完后的数字
        $stock = $redis->decr('redbag_stock');
        if($stock<0){
            echo json_encode([
                'status_code'=>'403',
                'message'=>'今天的红包已经结束了！'
            ]);
            exit;
        }

        // 5下单（放队列）
        $redis->lpush('redbag_count',$_SESSION['id']);
        // 6 把id放到集合中表示已经抢过了
        $redis->sadd($key,$_SESSION['id']);

            echo json_encode([
                'status_code'=>'200',
                'message'=>'恭喜你~抢到本站的红包！'
            ]);
    }
}


?>

