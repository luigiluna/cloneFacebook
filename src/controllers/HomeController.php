<?php
namespace src\controllers;

use \core\Controller;
use \src\helper\UserHelper;
use \src\helper\PostHelper;

class HomeController extends Controller {

    private $loggedUser; //Vai receber a classe do usuario logado

    public function __construct(){
        $this->loggedUser = UserHelper::checkLogin();       
        if($this->loggedUser === false){ 
            $this->redirect('/login');
        }
        
    }


    public function index() {
       $page = intval(filter_input(INPUT_GET,'page'));          //Magica, pega o numero de paginas pelo link

       $feed = PostHelper::getHomeFeed(
           $this->loggedUser->id,
            $page
        );

        $this->render('home', [
              'loggedUser' => $this->loggedUser,
              'feed' => $feed
        ]);
    }

}