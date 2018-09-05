<?php

    namespace controllers;
    use models\Blog;
    class IndexController
    {
        // 控制器 
        function index(){
            $blog = new Blog;
            $blogs = $blog->get("SELECT * FROM lists  LIMIT 10");
            return view('index.index',[
                'blogs'=>$blogs,
            ]);
        }
        
    }

?>