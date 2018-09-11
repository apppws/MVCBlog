<?php
namespace controllers;
use Yansongda\Pay\Pay;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;
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
            // echo $pay->return_code,'<hr>';
            // echo $pay->return_msg,'<hr>';
            // echo $pay->appid,'<hr>';
            // echo $pay->result_code,'<hr>';
            // echo $pay->code_url,"<hr>";   //这个是最重要的
            view('blogs.qrcode',['qrcode'=>$pay->code_url]);
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

        // 生成二维码
        public function qrcode(){
            // echo 'weixin://wxpay/bizpayurl?pr=POVBacd';
            // $qrCode = new QrCode('weixin://wxpay/bizpayurl?pr=POVBacd');
            // 大小
            // $qrCode->setSize(300);
            // $qrCode->setWriterByName('png');  //后缀
            // // $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH);
            // // $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
            // // $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
            // // $qrCode->setLabel('Scan the code', 16, __DIR__ . '/../assets/fonts/noto_sans.otf', LabelAlignment::CENTER);
            // // $qrCode->setLogoPath(__DIR__ . '/../assets/images/symfony.png');
            // // $qrCode->setLogoSize(150, 200);
            // // $qrCode->setRoundBlockSize(true);
            // // $qrCode->setValidateResult(false);
            // // $qrCode->setWriterOptions(['exclude_xml_declaration' => true]);

            // header('Content-Type:'.$qrCode->getContentType());
            // echo $code->writeString();
            // // 保存文件路径
            // $qrCode->writeFile(__DIR__ . '/qrcode.png');

            // // 创建响应对象
            // $response = new QrCodeResponse($qrCode);
        }
    }


?>