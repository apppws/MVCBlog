<?php

namespace models;
use PDO;
class Redbag extends BaseModel {

    public function create($user_id){
        $stmt = self::$pdo->prepare("INSERT INTO redbag(user_id) values(?)");
        $stmt->execute([
            $user_id
        ]);
    }

}