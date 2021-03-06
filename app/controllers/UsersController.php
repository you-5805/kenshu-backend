<?php
namespace App\controllers;

use App\lib\Controller;
use App\services\user\SignupService;

class UsersController extends Controller
{
    const VIEW_DIR = 'users/';

    protected function beforeAction($request)
    {
        // ログイン状態の時、記事一覧画面へリダイレクト
        if ($request->isAuthenticated()) {
            return $request->redirect('/posts');
        }
    }

    protected function new($_)
    {
    }

    protected function create($request)
    {
        $service = new SignupService();
        $service->execute($request);

        // 記事一覧画面へリダイレクト
        $request->redirect('/posts');
    }
}
