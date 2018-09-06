<?php 
    namespace controllers;
    class UploadController{

        // 上传的
        public function upload(){
            // 第一步接收图片 
            $file = $_FILES['image'];
            // 第二步生成随机文件名
            $filename = time();  //时间戳
            // 第三移动图片 并拼接文件地址
            move_uploaded_file($file['tmp_name'],ROOT.'public/uploads/'.$filename.'.png');
            // 第四步 把数组转成JSON格式返回
            echo json_encode([
                'success'=>true,
                'file_path'=>'public/uploads/'.$filename.'.png'
            ]);
        }

    }

?>