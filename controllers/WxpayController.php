<?php
namespace controllers;
use Yansongda\Pay\Pay;
use Mockery\CountValidator\Exception;
    class WxpayController{
        // 配置
        protected $config = [
            // 这个是从https://pay.weixin.qq.com/wiki/doc/api/index.html  下载的接口 的模拟配置文件
            'app_id' => 'wx426b3015555a46be',
            'mch_id' => '1900009851',
            'key' => '8934e7d15453e97507ef794cf7b0519d',
            'notify_url'=>'http::requestbin.fullcontact.com/r6s2a1r6',  //请求的微信地址
        ];
        // 发起支付
        public function pay(){
            // 订单信息
            $order = [
                'out_trade_no'=>time(),  //获取时间戳
                'total_fee'=>'1',  //分 
                'body'=>'pwswx-测试',
            ];
            // 发起支付
            $pay = Pay::wechat($this->config)->scan($order);
            echo $pay->return_code,'<hr>';
            echo $pay->return_msg,'<hr>';
            echo $pay->appid,'<hr>';
            echo $pay->result_code,'<hr>';
            echo $pay->code_url,"<hr>";
        }
        // 接受支付完成的通知
        public function notify(){
            $pay = Pay::wechat($this->config);
            try{
                $data = $pay->verify();
                // 判断
                if($data->result_code == 'SUCCESS' && $data->return_code == 'SUCCESS'){
                    echo '共支付了：'.$data->total_fee.'分';
                    echo '订单的id'.$data->out_trade_no;
                }

            }catch(Exception $e){
                var_dump( $e->gerMessage() );
            }
            // 返回响应
            $pay->success()->send();
        }
    }


?>