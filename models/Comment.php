<?php
    namespace models;
    use PDO;
    class Comment extends BaseModel{
        
        public function add($count,$list_id){
            // 预处理
            $stmt = self::$pdo->prepare("INSERT INTO comments(content,list_id,user_id) VALUES(?,?,?)");
            // 执行
            $stmt->execute([
                $count,
                $list_id,
                $_SESSION['id']
            ]);
        }

        //取出评论列表 和作者头像
        public function getComment($listid){
            // 取出评论及作者头像
            $stmt= self::$pdo->prepare("SELECT c.*,u.face FROM comments as c  LEFT JOIN users as u ON c.user_id=u.id WHERE c.list_id=? ORDER BY c.id DESC");
            
            $stmt->execute([
                $listid
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
?>