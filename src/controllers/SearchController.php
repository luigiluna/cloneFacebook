<?php
namespace src\controllers;

use \core\Controller;
use \src\helper\UserHelper;


class SearchController extends Controller {

    private $loggedUser; //Vai receber a classe do usuario logado

    public function __construct(){
        $this->loggedUser = UserHelper::checkLogin();       
        if($this->loggedUser === false){ 
            $this->redirect('/login');
        }
        
    }


    public function index($atts = []) {
        $searchTerm = filter_input(INPUT_GET,'s');

        if(empty($searchTerm)){
            $this->redirect('/');
        }

        // Detectando o usuário acessado
        $id = $this->loggedUser->id;
        if(!empty($atts['id'])){
            $id = $atts['id'];
        }
       
        $users = UserHelper::searchUser($searchTerm);

        $this->render('search', [
            'loggedUser' => $this->loggedUser,
            'searchTerm' => $searchTerm,
            'users' => $users
        ]);
    }

   
 
}