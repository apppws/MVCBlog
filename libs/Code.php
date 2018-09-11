<?php

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;

// 创建一个基础的
$qrCode = new QrCode('weixin://wxpay/bizpayurl?pr=POVBacd');
// 大小
$qrCode->setSize(300);

// 设置高级设置
$qrCode->setWriterByName('png');  //后缀
$qrCode->setMargin(10);  
$qrCode->setEncoding('UTF-8');   //编码
$qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH);
$qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
$qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
$qrCode->setLabel('Scan the code', 16, __DIR__ . '/../assets/fonts/noto_sans.otf', LabelAlignment::CENTER);
$qrCode->setLogoPath(__DIR__ . '/../assets/images/symfony.png');
$qrCode->setLogoSize(150, 200);
$qrCode->setRoundBlockSize(true);
$qrCode->setValidateResult(false);
$qrCode->setWriterOptions(['exclude_xml_declaration' => true]);

// 直接输入
header('Content-Type: ' . $qrCode->getContentType());
echo $qrCode->writeString();

// 保存文件路径
$qrCode->writeFile(__DIR__ . '/qrcode.png');

// 创建响应对象
$response = new QrCodeResponse($qrCode);
?>