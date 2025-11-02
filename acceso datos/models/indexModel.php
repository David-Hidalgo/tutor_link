<?php

class indexModel extends Model
{
    public function __construct() {
        parent::__construct();
    }
    
    public function getPosts()
    {
        $post = $this->_db->query("select * from posts");
        return $post->fetchall();
    }
    
    public function getPost($id)
    {
       $id = (int) $id; 
       $post = $this->_db->query("select * from posts where id=$id");
        return $post->fetch();
    }

    public function insertPost($title, $body)
    {
        $this->_db->prepare("insert into posts VALUES (null, :title, :body)")
             ->execute(
                     array(
                         ':title' => $title,
                         ':body'  => $body
                ));   
    } 
    public function editPost($id, $title, $body)
    {
       $id = (int) $id;
       
        $this->_db->prepare("UPDATE posts SET title= :title, body= :body WHERE id= :id")
             ->execute(
                     array(
                         ':id'=>$id,
                         ':title' => $title,
                         ':body'  => $body
                ));
       
    }
    
    public function deletePost($id)
    {
        $id = (int) $id;
       $this->_db->query("DELETE FROM posts WHERE id = $id");
    }
            
    
}
