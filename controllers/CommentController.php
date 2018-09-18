<?php 
    namespace controllers;

    use function GuzzleHttp\json_encode;

    class CommentController{
        // 发表评论
        public function comment(){
            // 处理json原始页面的问题
            $data =  file_get_contents('php://input');
            // 并把数据转换为数组
            $_POST = json_decode($data,TRUE);

            // 1 检测是否有登录
            if(!isset($_SESSION['id'])){
                echo json_encode([
                    'status_code'=>'401',
                    'message'=>'未登录'
                ]);
                exit;
            }
            // 2 接收表单中的数据
            $content = e($_POST['content']);
            $blog_id = $_POST['list_id'];

            // 3.插入到评论表中
            $model = new \models\Comment;
            $model->add($content,$blog_id);

            // 4.返回新数据
            echo json_encode([
                'status_code'=>'200',
                'message'=>'发表成功！',
                // 并返回数据
                'data'=>[
                    'content'=>$content,
                    'headimg'=>$_SESSION['headimg'],
                    'email'=>$_SESSION['email'],
                    'created_at'=>date('Y-m-d H:i:s'),
                ]
            ]);
            exit;
        }

        // 评论列表
        public function comment_list(){
            // 1 接收日志id
            $listid = $_GET['id'];
            // 2.获取日志的评论
            $model =  new \models\Comment;
            $data = $model->getComment($listid);
            // 转成json
            echo json_encode([
                'status_code'=>200,
                'data'=>$data
            ]);
        }
    }

?>