<?php
namespace src\helper;
use \src\models\User;
use \src\models\UserRelation;
use \src\helper\PostHelper;

class UserHelper {


    public static function checkLogin(){                                                   
        if(!empty($_SESSION['token'])){
            $token =  $_SESSION['token'];

            $data = User::select()->where('token', $token)->one();
            
            if(count($data) > 0){
                
                $loggedUser = new User();
                $loggedUser->id = $data['id'];
                $loggedUser->name = $data['name'];
                $loggedUser->avatar = $data['avatar'];
                
                return $loggedUser;
            }
             
        }

        return false;
    }

    public static function verifyLogin($email, $password){
        $user = User::select()->where('email', $email)->one();        //verificando o email
        if($user){
            if(password_verify($password, $user['password'])){
                $token = md5(time().rand(0,9999).time());             //Gerando token aleatório para verificaçoes 

                User::update()                                      //Atualizando token no banco
                    ->set('token', $token)
                    ->where('email',$email)
                ->execute();

                return $token;
            }
        }
        return false;
    }

    public function emailExists($email){
        $user = User::select()->where('email', $email)->one();
        return $user ? true : false;
    }

    public function idExists($id){
        $user = User::select()->where('id', $id)->one();
        return $user ? true : false;
    }

    public function getUser($id, $full = false){
        $data = User::select()->where('id', $id)->one();

        if($data){
            $user = new User();
            $user->id = $data['id'];
            $user->name = $data['name'];
            $user->birthDate = $data['birthdate'];
            $user->city = $data['city'];
            $user->work = $data['work'];
            $user->avatar = $data['avatar'];
            $user->cover = $data['cover'];
            $user->email = $data['email'];

            if($full){
                $user->followers=[];
                $user->following=[];
                $user->photos=[];

                //followers
                $followers = UserRelation::select()->where('user_to', $id)->get();       //Pegando todas as pessoas que seguem o usuario $id.  user_to é o usuario final. Se 'A' segue 'B' entao user_from: a , user_to: b
                foreach($followers as $follower){
                    $userData = User::select()->where('id', $follower['user_from'])->one();

                    $newUser = new User();
                    $newUser->id = $userData['id'];
                    $newUser->name = $userData['name'];
                    $newUser->avatar = $userData['avatar'];

                    $user->followers[] = $newUser;
                } 
                //following
                $following = UserRelation::select()->where('user_from', $id)->get();       //Pegando todas as pessoas que o usuario $id segue.  
                foreach($following as $follower){
                    $userData = User::select()->where('id', $follower['user_to'])->one();

                    $newUser = new User();
                    $newUser->id = $userData['id'];
                    $newUser->name = $userData['name'];
                    $newUser->avatar = $userData['avatar'];

                    $user->following[] = $newUser;
                    
                } 

                //photos
                $photos = PostHelper::getPhotosFrom($id);
                $user->photos = $photos; 
            }
            return $user;
        }
        return false;
    }
    
    public function addUser($name, $email, $password, $birthdate){
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $token = md5(time().rand(0,9999).time()); 
        User::insert([
            'email' => $email,
            'password' => $hash,
            'name' => $name,
            'birthdate' => $birthdate,
            'token' => $token              //Ja deixando o usuario logado
        ])->execute();

        return $token; 
    }

    public static function isFollowing($from, $to){
        $data = UserRelation::select()
            ->where('user_from', $from)
            ->where('user_to', $to)
        ->one();

        if($data){
            return true;
        }
        return false;
    }

    public static function follow($from, $to){
        UserRelation::insert([
            'user_from' => $from,
            'user_to' => $to
        ])->execute();
    }

    public static function unFollow($from, $to){
        UserRelation::delete()
            ->where('user_from', $from)
            ->where('user_to', $to)
        ->execute();
    }

    public static function searchUser($term){
        $users = [];
        $data = User::select()->where('name', 'like', '%'.$term.'%')->get();           //Pega em qualquer parte do nome
        
        if($data){
            foreach($data as $user){
                $newUser = new User();
                $newUser->id = $user['id'];
                $newUser->name = $user['name'];
                $newUser->avatar = $user['avatar'];

                $users[] = $newUser;
            }
        }
        return $users;

    }

    public function updateUser($id, $updateFields){
       if(isset($updateFields['password'])){
        $hash = password_hash($updateFields['password'], PASSWORD_DEFAULT);
            User::Update()
                ->set('name', $updateFields['name'])
                ->set('birthdate', $updateFields['birthDate'])
                ->set('email', $updateFields['email'])
                ->set('city', $updateFields['city'])
                ->set('work', $updateFields['work'])
                ->set('avatar', $updateFields['avatar'])
                ->set('cover', $updateFields['cover'])
                ->set('password', $hash)
                ->where('id', $id )
            ->execute();
       } else {
            User::Update()
                ->set('name', $updateFields['name'])
                ->set('birthdate', $updateFields['birthDate'])
                ->set('email', $updateFields['email'])
                ->set('city', $updateFields['city'])
                ->set('work', $updateFields['work'])
                ->set('avatar', $updateFields['avatar'])
                ->set('cover', $updateFields['cover'])
                ->where('id', $id )
            ->execute();
        }

    } 
}

