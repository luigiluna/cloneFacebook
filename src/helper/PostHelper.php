<?php
namespace src\helper;
use \src\models\Post;
use \src\models\User;
use \src\models\UserRelation;

class PostHelper {

    public static function addPost($idUser, $type, $body){

        $body = trim($body);                                   

        if(!empty($idUser) && !empty($body)){

            Post::insert([
                'id_user' => $idUser,
                'type' => $type,
                'created_at' => date('Y-m-d H:i:s'),
                'body' => $body
            ])->execute();
        }
    }

    public function _postListToObject($postList, $loggedUserId){
        $posts = [];
        foreach($postList as $postItem){                         //Transformando os posts do banco de dados em objetos do tipo post
            $newPost = new Post();
            $newPost->id = $postItem['id'];
            $newPost->type = $postItem['type'];
            $newPost->created_at = $postItem['created_at'];
            $newPost->body = $postItem['body'];
            $newPost->mine = false;

            if($postItem['id_user'] == $loggedUserId){
                $newPost->mine = true;
            }
            
            $newUser = User::select()->where('id', $postItem['id_user'])->one();
            $newPost->user = new User();
            $newPost->user->id = $newUser['id'];
            $newPost->user->name = $newUser['name'];
            $newPost->user->avatar = $newUser['avatar'];

            //TO DO: Preencher informações de like
            $newPost->likeCount = 0;
            $newPost->liked=false;

            //TO DO: Preencher informações de comments
            $newPost->comments = [];

            $posts[] = $newPost;
        }
        return $posts;
    }

    

    public static function getUserFeed($idUser, $page, $loggedUserId){
        $perPage = 2;

        $postList = Post::select()
            ->where('id_user', $idUser)                        //Pega os posts dos usuarios que eu sigo / Users é um array contendo o id dos usuarios que eu sigo.
            ->orderBy('created_at', 'desc')
            ->page($page,$perPage)
        ->get();       
        
        $pageCount = Post::select()
            ->where('id_user', $idUser)                        //Pega os posts dos usuarios que eu sigo / Users é um array contendo o id dos usuarios que eu sigo.
        ->count(); 

        $pageCount = ceil($pageCount / $perPage);

        $posts = self::_postListToObject($postList, $loggedUserId);

        return [
            'posts' => $posts,
            'pageCount' => $pageCount,
            'currentPage' =>$page
        ];

    }

    public static function getHomeFeed($idUser,$page){
        $perPage = 2;

        $userList = UserRelation::select()->where('user_from', $idUser)->get();             //Lista de Usuarios que eu sigo
        $users = [];
        foreach($userList as $userItem){
            $users[]=$userItem['user_to'];
        }
        $users[]=$idUser;

        $postList = Post::select()
            ->where('id_user', 'in', $users)                        //Pega os posts dos usuarios que eu sigo / Users é um array contendo o id dos usuarios que eu sigo.
            ->orderBy('created_at', 'desc')
            ->page($page,$perPage)
        ->get();       
        
        $pageCount = Post::select()
            ->where('id_user', 'in', $users)                        //Pega os posts dos usuarios que eu sigo / Users é um array contendo o id dos usuarios que eu sigo.
        ->count(); 

        $pageCount = ceil($pageCount / $perPage);

        $posts = self::_postListToObject($postList, $idUser);

        return [
            'posts' => $posts,
            'pageCount' => $pageCount,
            'currentPage' =>$page
        ];
    }

    public function getPhotosFrom($idUser){
        $photosData = Post::select()
            ->where('id_user', $idUser)
            ->where('type', 'photo')
        ->get();
        
        $photos = [];

        foreach($photosData as $photo){
            $newPost = new Post();
            $newPost->id = $photo['id'];
            $newPost->type = $photo['type'];
            $newPost->created_at = $photo['created_at'];
            $newPost->body = $photo['body'];
            $photos[] = $newPost;
        }
        return $photos;
    }
}