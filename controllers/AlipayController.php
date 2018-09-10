<?php
    namespace controllers;
    // 先引入这个包
    use Yansongda\Pay\Pay;
    class AlipayController{

        // 设定配置文件
        public $config = [
            // appid 号
            'app_id'=>'2016091600527335',
            //通知地址
            // 'notify_url'=>'https://openapi.alipaydev.com/gateway.do',
                'notify_url'=>'http://requestbin.fullcontact.com/rewlmcre',
            // 调回地址
            'return_url'=>'http://localhost:9999/alipay/return',
            // 支付宝公钥
            'ali_public_key'=>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAqezjjFgYiMrbCWugQqthGzZSk/8JsIJZlLEm6TAfg2CLFQ4Ks3czKaJ7O1Oe6X4X7vVA18KbOKVfI+a0MypWNRcHpqVhbO4jrdawhXQJAgTDgkRZh2UAPZw4aELz1fQxCIto7itY0vQfU94P5tljLX755eFPIbzWfkVHD1pKLlg/Ylrbv0gHXRRuSPDGDznO/hmhsyO+2XqZokqqLDT0lPVDkdHWEGgF+tWRZAza9CEif4/Hs2Gj2z8DO1FF6ieEikC0ywuQDCvSAxUy7cl5AnpzbqzFsSIZgU0+ag+VtfYbV2KQIgJawHziOT9WLJYhSNeHRQBa6jL7e/3VZyyATQIDAQAB',
            // 商户应用密钥
            'private_key'=>'MIIEpAIBAAKCAQEAxG42Ea9YCkCMyRCB/ZJE9eE4VjI0iAmPB9JV6h1Za5drR9nrBrkYW/9mQd8LxeUzFyVCapWDUe88x6UUYSPaatcsmr6IW8xzrgcWJWszfRaGr3MzMLsQHnV02zyviqNkIlgsgoRApkLWEPwNRlwXGxyg4HtdQIjHeNL4VvOBBNKitI0cO9GNlGXwO8EkA82HZh2TIP6d9emxIzBHs6wJ6CbgmJ2dkAVGeBl7PXtS4/kcg+6fYm/0Xtbq8LL9jOhvR67QhGkXDRU0WU5gAx3Y+xoLs0eR1XXwtHWl45L1vxWGrNI1m+p5ng6Q2d0SRiKrMBNRM6VlqWFICW8HGROUBwIDAQABAoIBAQCrEJgB2sQ/WWvCBqBOJj3sK9GWL27UMg7f7utnUVv4eQuyrTMizbtLEycIoqhsFWji9U3b6I1Oo5w4+ai/2Ct09CMiOGAkIe90VTOSLsPOYfm1fgvMmnw1KnE0JKtzf0vLJSLOH0L2CCrI69jbt4Nf1xS7qnPRqcydio0/nBx2BzuLarZmnePKJ0P4UmsYgQG8J/JLOuULyLNXmwaIodaunj563/bkdHNiBBg2tC/nTb44AR8iY/RAxJW+hnmMnpWvNGpbumI1RaZPepzyrTmHGreB0x656V7b/doub3KSCtPZjwusxVPftS6muzG4tUQ0YM2ftL97iJQvQfrfb4WhAoGBAOQcTO/HYqN68X1iBkykYcJVswOiZhHAVnCnoMjqgt7nJVtnPbvvAl1Igv8BXEND8oSZ7xCV2LXy1xbozWQkLQ/RRLWt8mNZqFbnKFtJ+eUp7dpuyOqW12dmQM2njmmnM4FVZN3MYeobKHDYJlDo07XASmQ0oHbct5NO4DHJ+ELfAoGBANxyWQzvNNqCCnLxKzijm45kCydo712vAVYsNkn1TfONpWl8adJyzkDBwGekrKBs25uA5jOBZICGR7DsT4a3zxt5RpUXX4WMMASUmetdrFyTpAffsib5r+jLLPMXfYOb9C/u5S2z2FN98Tqx+2XfI3bTAag7jXjgfPX5Y4WGdrvZAoGAFekd/r4hHGDXx1peDoiPl1ISAtxbf4MBCosfZ40XCwAa13/AL0gS6xDm/EWOLivdpJ0AmJA8I6XywRGVgPP0nBtWxTizGpXnFInZl4MwjLGNVjjj9ZyNjjIFMXvRsxZLXTXtnVxfX1RCeyxX6dejVkblHmDrtN8Yhv7BjCbBQPMCgYEAvVGqrpQERR/nD12U69h+MGQ0vAy/fSpdsH7ZxNxZrK/Z/eSuEOEtxqleruPaqQ+z7jFeAZ+/Cy3HBed8SMs0n3igqEvhahTB7D0ejubsrrjQ5z4yhoxqiTdsC/0BevSFWmEFCyHnx5RihjDyIUPn9hUy2CME1Wmdh7U8xiB7eckCgYBmyckGg4B8qnnn9fV66wIsaqypb2tPHnXFBbOEfC4u3xHNLU8QCUZc6qoK3wyuVVWs+75yztpZzFE/TYrL9CXi4MjrY38nHFLwvsIXOFTxEnvXbog2T8ECT2Gj1xlOX6Z0vjTV9rLCmw9uq0nYiQ/dSJ4GjaUqtdYPKtdnpz62Qw==',
            // 沙箱模式
            'mode'=>'dev',
        ];
        // 发起支付
        public function pay(){
            // 第一接收订单编码
            $sn = $_POST['sn'];
            // 取出订单的信息
            $order = new \models\Order;
            // 根据编码取出订单信息  
            $data = $order->findBySn($sn);
            // 判断如果订单还未支付就跳到支付宝  
            if($data['status']==0){
                $ord=[
                    'out_trade_no'=>$sn,  //本地订单id
                    'total_amount'=>$data['money'],  //支付金额
                    'subject'=>'pws测试用户充值：'.$data['money'].'元', //支付标题
                ];
                // 调用这个方法 发起支付
                $alipay = Pay::alipay($this->config)->web($ord);
                $alipay->send();
            }else{
                die('订单状态不能进行支付！');
            }  
            
        }
        // 支付完成跳回
        public function return(){
            // 是的  进行验证 
            $data = Pay::alipay($this->config)->verify();
            // 提示
            echo '<h1>支付成功！</h1><hr>';
            // 把数据 全部打出来
            var_dump($data->all() );
        }

        // 接收支付完成的通知
        public function notify(){
            $alipay = Pay::alipay($this->config);
            try{
                // 判断消息是否是支付宝发过来的 以及判断这个消息有么有被中途串改 如果改了就抛出异常
                $data = $alipay->verify(); 
                // 判断支付状态
                if($data->trade_status=='TRADE_SUCCESS' || $data->trade_status =='TRADE_FINISHED'){
                    //更新订单状态
                    $order = new \models\Order;
                    // 获取订单信息
                    $orderInfo = $order->findBySn($data->out_trade_no);
                    // 如果订单信息状态为未支付状态
                    if($orderInfo['status']==0){
                        // 设置订单为已支付状态
                        $order->setPaid($data->out_trade_no);
                    }
                }
            }catch(\Exception $e){
                echo "失败";
                var_dump($e->getMessage());
            }
            // 返回响应
            $alipay->success()->send();
        }

        // 退款
        public function redund(){
            // 生成唯一的退款订单号
            $refundNo = md5( rand(1,99999) .microtime() );
            try{

                // 退款
                $ret =Pay::alipay($this->config)->refund([
                    'out_trade_no'=>'1536313837',  //之前的订单流水号
                    'refund_amount'=>'0.01',  //退款金额
                    'out_request_no'=>$refundNo, //退款订单号
                ]);
                // var_dump($ret);
                // code 状态码等于 10000 就表示 退出
                if($ret->code == 10000){
                    echo "退款成功！";
                }
                else
                {
                    echo "退款失败，错误信息".$ret->sub_msg;
                    echo "错误编号:".$ret->sub_code;
                }

            }catch(\Exception $e){
                var_dump($e->getMessage());
            }
        }


    }


?>