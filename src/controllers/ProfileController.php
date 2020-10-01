<?php
namespace src\controllers;

use \core\Controller;
use \src\helper\UserHelper;
use \src\helper\PostHelper;

class ProfileController extends Controller {

    private $loggedUser; //Vai receber a classe do usuario logado

    public function __construct(){
        $this->loggedUser = UserHelper::checkLogin();       
        if($this->loggedUser === false){ 
            $this->redirect('/login');
        }
        
    }


    public function index($atts = []) {
        $page = intval(filter_input(INPUT_GET,'page'));

        // Detectando o usuário acessado
        $id = $this->loggedUser->id;
        if(!empty($atts['id'])){
            $id = $atts['id'];
        }
        // Pegando Informações do usuário
        $user = UserHelper::getUser($id, true);     //retorna as infos basicas do usuario ou completas (true)

        if(!$user){
            $this->redirect('/');
        }

        $dateFrom = new \DateTime($user->birthDate);
        $dateTo = new \DateTime('today');
        $user->ageYears = $dateFrom->diff($dateTo)->y;

        // Pegando o feed do usuário
        $feed = PostHelper::getUserFeed(
            $id, 
            $page, 
            $this->loggedUser->id
        );

        //Verificar se EU sigo o usuário
        $isFollowing = false;
        if($user->id != $this->loggedUser->id){
            $isFollowing = UserHelper::isFollowing($this->loggedUser->id, $user->id);
        }

        $this->render('profile', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'feed' => $feed,
            'isFollowing' => $isFollowing
        ]);
    }

    public function follow($atts) {
        $to = intval($atts['id']);
        

        if(UserHelper::idExists($to)){
            if(UserHelper::isFollowing($this->loggedUser->id, $to)){
                UserHelper::unFollow($this->loggedUser->id, $to);
            } else {
                UserHelper::follow($this->loggedUser->id, $to);
            }
        }

        $this->redirect('/perfil/'.$to);
    }

    public function friends($atts = []) {
        // Detectando o usuário acessado
        $id = $this->loggedUser->id;
        if(!empty($atts['id'])){
            $id = $atts['id'];
        }
        // Pegando Informações do usuário
        $user = UserHelper::getUser($id, true);     //retorna as infos basicas do usuario ou completas (true)

        if(!$user){
            $this->redirect('/');
        }

        $dateFrom = new \DateTime($user->birthDate);
        $dateTo = new \DateTime('today');
        $user->ageYears = $dateFrom->diff($dateTo)->y;

        //Verificar se EU sigo o usuário
        $isFollowing = false;
        if($user->id != $this->loggedUser->id){
            $isFollowing = UserHelper::isFollowing($this->loggedUser->id, $user->id);
        }

        $this->render('profile_friends', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'isFollowing' => $isFollowing
        ]);
        

    }

    public function photos($atts = []) {
        // Detectando o usuário acessado
        $id = $this->loggedUser->id;
        if(!empty($atts['id'])){
            $id = $atts['id'];
        }
        // Pegando Informações do usuário
        $user = UserHelper::getUser($id, true);     //retorna as infos basicas do usuario ou completas (true)

        if(!$user){
            $this->redirect('/');
        }

        $dateFrom = new \DateTime($user->birthDate);
        $dateTo = new \DateTime('today');
        $user->ageYears = $dateFrom->diff($dateTo)->y;

        //Verificar se EU sigo o usuário
        $isFollowing = false;
        if($user->id != $this->loggedUser->id){
            $isFollowing = UserHelper::isFollowing($this->loggedUser->id, $user->id);
        }

        $this->render('profile_photos', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'isFollowing' => $isFollowing
        ]);
        

    }

}