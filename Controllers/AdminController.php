<?php
namespace App\Controllers;

use App\Models\AnnoncesModel;
use PDOException;

class AdminController extends Controller
{
    public function index()
    {
        // On vérifie si on est admin
        if($this->isAdmin()){
            $this->render('admin/index', [], 'admin');
        }
    }

    /**
     * Affiche la liste des annonces sous forme de tableau
     * @return void 
     */
    public function annonces()
    {
        if($this->isAdmin()){
            $annoncesModel = new AnnoncesModel;

            $annonces = $annoncesModel->findAll();

            $this->render('admin/annonces', compact('annonces'), 'admin');
        }
    }

    /**
     * Supprime une annonce si on est admin
     * @param int $id 
     * @return void 
     */
    public function supprimeAnnonce(int $id)
    {
        if($this->isAdmin()){
            $annonce = new AnnoncesModel;

            $annonce->delete($id);

            header('Location: '.$_SERVER['HTTP_REFERER']);
        }
    }

    /**
     * Active ou désactive une annonce
     * @param int $id 
     * @return void 
     */
    public function activeAnnonce(int $id)
    {
        if($this->isAdmin()){
            $annoncesModel = new AnnoncesModel;

            $annonceArray = $annoncesModel->find($id);

            if($annonceArray){
                $annonce = $annoncesModel->hydrate($annonceArray);

                // if($annonce->getActif()){
                //     $annonce->setActif(0);
                // }else{
                //     $annonce->setActif(1);
                // }

                $annonce->setActif($annonce->getActif() ? 0 : 1);

                $annonce->update();
            }
        }
    }


    /**
     * Vérifie si on est admin
     * @return true 
     */
    private function isAdmin()
    {
        // On vérifie si on est connecté et si "ROLE_ADMIN" est dans nos rôles
        if(isset($_SESSION['user']) && in_array('ROLE_ADMIN', $_SESSION['user']['roles'])){
            // On est admin
            return true;
        }else{
            // On n'est pas admin
            $_SESSION['erreur'] = "Vous n'avez pas accès à cette zone";
            header('Location: /');
            exit;
        }
    }

}