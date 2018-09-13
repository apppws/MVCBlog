<?php
namespace controllers;
use Yansongda\Pay\Pay;

    class WxpayController{
        // 配置
        protected $config = [
            // 这个是从https://pay.weixin.qq.com/wiki/doc/api/index.html  下载的接口 的模拟配置文件
            'app_id' => 'wx426b3015555a46be',
            'mch_id' => '1900009851',
            'key' => '8934e7d15453e97507ef794cf7b0519d',
            // 'notify_url'=>'http::requestbin.fullcontact.com/r6s2a1r6',  //请求的微信地址
            'notify_url'=>'http://241eb913.ngrok.io/wxpay/notify',
        ];
        // 发起支付
        public function pay(){
            // 第一接收订单编号
            $sn = $_POST['sn'];
            // var_dump($sn);
            // 取出订单的信息
            $order = new \models\Order;
            // 根据编号取出订单信息
            $data = $order->findBySn($sn);
            // var_dump($data);
            // 判断如果订单信息还未支付就跳转
            if($data['status']==0){
                 // 订单信息
                $order = [
                    'out_trade_no'=>$data['sn'],  //获取时间戳
                    'total_fee'=>$data['money']*100,  //分 
                    'body'=>'pwswx-测试:'.$data['money'].'元',
                ];
                // 发起支付
                $pay = Pay::wechat($this->config)->scan($order);
                // 判断 是否发送成功
                if($pay->return_code=='SUCCESS' && $pay->result_code=='SUCCESS'){
                    // 发送成功就加载视图   //生成二维码图片
                    view('users.wxpay',[
                        'code'=>$pay->code_url,
                        'sn'=>$sn
                        ]);
                }
            }else{
                die('订单状态不能完成支付');
            }
           
           
        }
        // 接受支付完成的通知
        public function notify(){
            // 模拟一下数据
            // $log = new \libs\Log('wxpay');
            // $log->log('接收到微信的消息');
            $pay = Pay::wechat($this->config);
            try{
                $data = $pay->verify();
                // 记录一下日志
                // $log->log('验证成功，接收的数据是：' . file_get_contents('php://input'));
                // 判断支付状态
                if($data->result_code == 'SUCCESS' && $data->return_code == 'SUCCESS'){
                    // 记录
                    // $log->log('支付成功');
                    //更新订单状态
                    $order = new \models\Order;
                    // 获取订单信息
                    $orderInfo = $order->findBySn($data->out_trade_no);
                    var_dump($orderInfo);   
                    // 如果订单信息状态为未支付状态
                    if($orderInfo['status']==0){
                        var_dump($orderInfo['status']);
                        // 开始事务
                        $order->startTrans();
                        // 设置订单为已支付状态
                        $red1 = $order->setPaid($data->out_trade_no);
                        // 更新用户余额
                        $user = new  \models\User;
                        $red2 = $user->addMoney($orderInfo['money'],$orderInfo['user_id']);
                        // 判断事务
                        if($red1 && $red2){
                            // 提交事务
                            $order->commit();
                        }else{
                            // 就回滚事务
                            $order->rollback();
                        }
                    }
                }

            }catch(Exception $e){
                // 记录日志
                // $log->log('验证失败！'.$e->getMessage());
                var_dump( $e->getMessage() );
            }
            // 返回响应
            $pay->success()->send();
        }
    }


?>