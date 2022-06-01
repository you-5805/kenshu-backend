<?php
namespace App\services;

use App\lib\Request;
use App\lib\ServerException;
use App\models\User;
use Ramsey\Uuid\Uuid;

class SignupService
{
    public static function execute(Request $request)
    {
        $uploaded_file_path = null;

        if ($request->files['profile_image']['size'] !== 0) {
            self::verifyUploadedImageFile($request->files['profile_image']);
            $uploaded_file_path = '/assets/img/users/' . Uuid::uuid4() . '_' . $request->files['profile_image']['name'];
        }

        // アップロードされた画像がない時、デフォルトの画像を設定する
        $profile_image_url = $uploaded_file_path !== null ? $uploaded_file_path : '/assets/img/default-icon.png';

        User::create(
            email: $request->post['email'],
            name: $request->post['name'],
            password: $request->post['password'],
            profile_image_url: $profile_image_url,
        );

        if ($uploaded_file_path) {
            move_uploaded_file($request->files['profile_image']['tmp_name'], '../public' . $uploaded_file_path);
        }

        $user = User::getByEmail($request->post['email']);

        $request->setSession('user_id', $user->id);

        return $user;
    }

    /**
     * throws Exception when invalid
     */
    private static function verifyUploadedImageFile(array $file)
    {
        // ファイル形式の確認
        $valid_types = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
        if (!in_array(mime_content_type($file['tmp_name']), $valid_types, true)) {
            throw ServerException::invalidRequest(display_text: '不正なファイル形式です');
        }

        // 画像サイズの確認
        if ($file['size'] > 10000000) {
            throw ServerException::invalidRequest(display_text: 'アップロードできるファイルサイズの上限は10MBです');
        }
    }
}
