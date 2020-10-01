<?php
namespace src\controllers;

use \core\Controller;
use \src\helper\UserHelper;
use \src\helper\PostHelper;

class PostController extends Controller {

    private $loggedUser; //Vai receber a classe do usuario logado

    public function __construct(){
        $this->loggedUser = UserHelper::checkLogin();  
        if($this->loggedUser === false){ 
            $this->redirect('/login');
        }
        
    }


    public function new() {
        $body = filter_input(INPUT_POST, 'body');
        
        if($body){
            PostHelper::addPost($this->loggedUser->id,
            'text',
            $body
            );
        }

        $this->redirect('/');
    }

}