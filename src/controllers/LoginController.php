<?php
namespace src\controllers;

use \core\Controller;
use \src\helper\UserHelper;

class LoginController extends Controller {

    public function signin(){
        $flash = '';
        if(!empty($_SESSION['flash'])){
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }
        $this->render('sigin', [
            'flash' => $flash
        ]);
    }

    public function signinAction(){
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);     //Email para verificaçãod o login
        $password = filter_input(INPUT_POST, 'password');

        if($email &&  $password){
            $token = UserHelper::verifyLogin($email, $password);   //Helper que verifica e retorna o token
            if($token){                                             //Tudo certo
                $_SESSION['token'] = $token;
                $this->redirect('/');                
            } else {
                $_SESSION['flash'] = 'E-mail e/ou senha não conferem.';
                $this->redirect('/login');
            }
            
        } else {
            $this->redirect('/login');
        }


    }


    public function signup(){
        $flash = '';
        if(!empty($_SESSION['flash'])){
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }
        $this->render('sigup', [
            'flash' => $flash
        ]);
    }

    public function signupAction(){
        $name = filter_input(INPUT_POST, 'name');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);     
        $password = filter_input(INPUT_POST, 'password');
        $birthDate = filter_input(INPUT_POST, 'birthDate');

        if($name && $email && $password && $birthDate){
            $birthDate = explode('/', $birthDate);        //Divide em vetores
            if(count($birthDate)!=3){                     //Verifica se existe 3 itens       
                $_SESSION['flash'] = 'Data de nascimento inválida!';
                $this->redirect('/cadastro');
            }

            $birthDate = $birthDate[2].'-'.$birthDate[1].'-'.$birthDate[0];
            if(strtotime($birthDate) === false){          //verifica se é uma data real
                $_SESSION['flash'] = 'Data de nascimento inválida!';
                $this->redirect('/cadastro');      
            }

            if(UserHelper::emailExists($email)===false){
                $token = UserHelper::addUser($name, $email, $password, $birthDate);
                $_SESSION['token'] =  $token;
                $this->redirect('/');
            } else{
                $_SESSION['flash'] = 'Email já cadastrado!';
                $this->redirect('/cadastro');
            }

        } else {
            $this->redirect('/cadastro');
        }

    }

    public function logout(){
        $_SESSION['token'] = ' ';
        $this->redirect('/login');
    }
}