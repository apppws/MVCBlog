<?php
    namespace controllers;
    use models\Blog;
    class BlogController
    {
        // 发表日志方法
        public  function create(){
            // 加载发表日志视图
            view('blogs.create');
        }

        // 提交过来的方法
        public function store(){
            $blog = new Blog;
            $ccc =  $blog->insert([
                'title' => $_POST['title'],
                'content' => $_POST['content'],
                'is_show' => $_POST['is_show']
            ]);
            var_dump($ccc);
            echo "插入数据成功";
        }

    }