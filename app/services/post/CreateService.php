<?php
namespace App\services\post;

use App\dto\CreatePostDto;
use App\lib\ServerException;
use App\models\Post;
use App\models\PostToTag;
use App\services\concerns\Service;

use Exception;

class CreateService extends Service
{
    public function execute()
    {
        $pdo = $this->pdo;
        $request = $this->request;

        $current_user_id = $request->getCurrentUserId();

        try {
            $dto = new CreatePostDto(
                user_id: $current_user_id,
                title: $request->post['title'],
                content: $request->post['content'],
                tags: isset($request->post['tags']) ? $request->post['tags'] : [],
            );

            $pdo->beginTransaction();

            $post_id = Post::create($pdo, $dto);

            $post = Post::getById($pdo, $post_id);

            // TODO: O(n) の回避
            foreach ($dto->tags as $tag_id) {
                PostToTag::create($pdo, $post->id, $tag_id);
            }

            $pdo->commit();

            $post->getTags($pdo);

            return $post;
        } catch (Exception | ServerException $exception) {
            $pdo->rollBack();

            throw $exception;
        }
    }
}