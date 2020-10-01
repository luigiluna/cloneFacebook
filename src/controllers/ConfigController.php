<?php
namespace src\controllers;

use \core\Controller;
use \src\helper\UserHelper;


class ConfigController extends Controller {

    private $loggedUser; //Vai receber a classe do usuario logado

    public function __construct(){
        $this->loggedUser = UserHelper::checkLogin();       
        if($this->loggedUser === false){ 
            $this->redirect('/login');
        }
        
    }


    public function index() {

        $flash = '';
        if(!empty($_SESSION['flash'])){
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }
        
        $user = UserHelper::getUser($this->loggedUser->id,True);

        $user->birthDate = explode('-', $user->birthDate);

        $user->birthDate = $user->birthDate[2].'/'.$user->birthDate[1].'/'.$user->birthDate[0];

        $this->render('config', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'flash' => $flash
        ]);
    }

    public function configAction(){
     
        $name = filter_input(INPUT_POST, 'name');
        $birthDate = filter_input(INPUT_POST, 'birthDate');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);     
        $city = filter_input(INPUT_POST, 'city');
        $work = filter_input(INPUT_POST, 'work');

        $newPassword = filter_input(INPUT_POST, 'newPassword');
        $confirmNewPassword = filter_input(INPUT_POST, 'confirmNewPassword');

        $updateFields = [];

        
        $user = UserHelper::getUser($this->loggedUser->id,True);

        


        if(empty($name) or empty($birthDate) or empty($email)){
            $_SESSION['flash'] = 'Preencha os campos obrigatórios';
            $this->redirect('/config');
        }


        if(UserHelper::emailExists($email) && ($email!=$user->email)){
            $_SESSION['flash'] = 'Email já existente';
            $this->redirect('/config');
        }

        $birthDate = explode('/', $birthDate);        //Divide em vetores
        if(count($birthDate)!=3){                     //Verifica se existe 3 itens       
            $_SESSION['flash'] = 'Data de nascimento inválida!';
            $this->redirect('/config');
        }

        $birthDate = $birthDate[2].'-'.$birthDate[1].'-'.$birthDate[0];

        if(strtotime($birthDate) === false){          //verifica se é uma data real
            $_SESSION['flash'] = 'Data de nascimento inválida!';
            $this->redirect('/config');      
        }

        

        if($newPassword != $confirmNewPassword){
            $_SESSION['flash'] = 'As senhas não coincidem';
            $this->redirect('/config');
        }

        if(!empty($newPassword)){
            $updateFields['password'] = $newPassword;
        }


        //avatar
        if(isset($_FILES["avatar"]) && !empty($_FILES["avatar"]["tmp_name"])){

            $newAvatar = $_FILES["avatar"];

            if(in_array($newAvatar['type'], ['image/jpeg', 'image/jpg', 'image/png'])){
                $avatarName = $this->cutImage($newAvatar, 200, 200, 'media/avatars');
                $updateFields['avatar'] = $avatarName;
                
            }
        } else {
            $updateFields['avatar'] = $user->avatar;
        }

        //cover
        if(isset($_FILES["cover"]) && !empty($_FILES["cover"]["tmp_name"])){
            $newCover = $_FILES["cover"];

            if(in_array($newCover['type'], ['image/jpeg', 'image/jpg', 'image/png'])){
                $coverName = $this->cutImage($newCover, 850, 310, 'media/covers');
                $updateFields['cover'] = $coverName;
            }
        } else {
            $updateFields['cover'] = $user->cover;
        }

        $updateFields['name'] = $name;
        $updateFields['birthDate'] = $birthDate;
        $updateFields['email'] = $email;
        $updateFields['city'] = $city;
        $updateFields['work'] = $work;
        

        $_SESSION['flash'] = '';
        UserHelper::updateUser($user->id, $updateFields);
       
    }

    private function cutImage($file, $w ,$h, $folder){
        list($widthOrig, $heightOrig) = getimagesize($file['tmp_name']);
        $ratio = $widthOrig / $heightOrig;

        $newWidth = $w;
        $newHeight = $newWidth / $ratio;

        if($newHeight < $h){
            $newHeight = $h;
            $newWidth = $newHeight * $ratio;
        }

        $x = $w - $newWidth;
        $y = $h - $newHeight;
        $x = $x < 0 ? $x / 2 : $x;
        $y = $y < 0 ? $y  /2 : $y;

        $finalImage = imagecreatetruecolor($w, $h);
        switch($file['type']){
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($file['tmp_name']);
            break;
            case 'image/png':
                $image = imagecreatefrompng($file['tmp_name']);
            break;
        }

        imagecopyresampled(
            $finalImage, $image,
            $x, $y, 0, 0,
            $newWidth, $newHeight, $widthOrig, $heightOrig
        );
         
        $fileName = md5(time().rand(0,9999)).'.jpg';

    
        imagejpeg($finalImage, $folder.'/'.$fileName);

        
    

        return $fileName;

    }
}