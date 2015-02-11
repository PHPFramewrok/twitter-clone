<?php
/**
 * Created by PhpStorm.
 * User: will
 * Date: 2/9/15
 * Time: 1:38 PM
 */

namespace Twitter\Factories;


use Twitter\RePost;

class RePostFactory {

    protected $repost;

    public function __construct(RePost $repost)
    {
        $this->repost = $repost;
    }

    public function create($userId, $postId)
    {
        $this->repost->user_id = $userId;
        $this->repost->post_id = $postId;
        $this->repost->save();
    }
}