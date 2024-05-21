<?php
namespace App\Model;

use Nette;

final class PostFacade
{

    public function __construct(
        private Nette\Database\Explorer $database,
        
    ) {
    }


    public function getPublicArticles()
    {
        return $this->database
            ->table('posts')
            ->where('created_at < ', new \DateTime)
            ->order('created_at DESC');
    }
    public function getPostById(int $postId)
    {
        return $this->database
            ->table('posts')
            ->get($postId);
    }
    public function getComments(int $postId)
    {

        return $post = $this->database
            ->table('comments')
            ->where('post_id', $postId)
            ->order('created_at');

    }
 
    public function insertPost(array $data)
    {
        $post = $this->database
            ->table('posts')
            ->insert($data);
        return $post;
    }
    
    public function updateRating(int $userId, int $postId, int $liked)
    {
        $row = $this->database->table('rating')
                              ->where('user_id', $userId)
                              ->where('post_id', $postId)
                              ->fetch();
    
        if ($row) {
            $this->database->table('rating')
                           ->where('user_id', $userId)
                           ->where('post_id', $postId)
                           ->update(['liked' => $liked]);
        } else {
            $this->database->table('rating')->insert([
                'user_id' => $userId,
                'post_id' => $postId,
                'liked' => $liked
            ]);
        }
    }
    
    

}