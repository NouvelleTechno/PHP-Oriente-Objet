<?php
namespace App\Controllers;

use App\Core\Form;
use App\Models\AnnoncesModel;

class AnnoncesController extends Controller
{
    /**
     * Cette méthode affichera une page listant toutes les annonces de la base de données
     * @return void 
     */
    public function index()
    {
        // On instancie le modèle correspondant à la table 'annonces'
        $annoncesModel = new AnnoncesModel;

        // On va chercher toutes les annonces actives
        $annonces = $annoncesModel->findBy(['actif' => 1]);

        // On génère la vue
        $this->render('annonces/index', compact('annonces'));
    }

    /**
     * Affiche 1 annonce
     * @param int $id Id de l'annonce 
     * @return void 
     */
    public function lire(int $id)
    {
        // On instancie le modèle
        $annoncesModel = new AnnoncesModel;

        // On va chercher 1 annonce
        $annonce = $annoncesModel->find($id);

        // On envoie à la vue
        $this->render('annonces/lire', compact('annonce'));
    }

    /**
     * Ajouter une annonce
     * @return void 
     */
    public function ajouter()
    {
        // On vérifie si l'utilisateur est connecté
        if(isset($_SESSION['user']) && !empty($_SESSION['user']['id'])){
            // L'utilisateur est connecté
            // On vérifie si le formulaire est complet
            if(Form::validate($_POST, ['titre', 'description'])){
                // Le formulaire est complet
                // On se protège contre les failles XSS
                // strip_tags, htmlentities, htmlspecialchars
                $titre = strip_tags($_POST['titre']);
                $description = strip_tags($_POST['description']);

                // On instancie notre modèle
                $annonce = new AnnoncesModel;

                // On hydrate
                $annonce->setTitre($titre)
                    ->setDescription($description)
                    ->setUsers_id($_SESSION['user']['id']);

                // On enregistre
                $annonce->create();

                // On redirige
                $_SESSION['message'] = "Votre annonce a été enregistrée avec succès";
                header('Location: /');
                exit;
            }else{
                // Le formulaire est incomplet
                $_SESSION['erreur'] = !empty($_POST) ? "Le formulaire est incomplet" : '';
                $titre = isset($_POST['titre']) ? strip_tags($_POST['titre']) : '';
                $description = isset($_POST['description']) ? strip_tags($_POST['description']) : '';
            }


            $form = new Form;

            $form->debutForm()
                ->ajoutLabelFor('titre', 'Titre de l\'annonce :')
                ->ajoutInput('text', 'titre', [
                    'id' => 'titre',
                    'class' => 'form-control',
                    'value' => $titre
                ])
                ->ajoutLabelFor('description', 'Texte de l\'annonce')
                ->ajoutTextarea('description', $description, ['id' => 'description', 'class' => 'form-control'])
                ->ajoutBouton('Ajouter', ['class' => 'btn btn-primary'])
                ->finForm()
            ;

            $this->render('annonces/ajouter', ['form' => $form->create()]);
        }else{
            // L'utilisateur n'est pas connecté
            $_SESSION['erreur'] = "Vous devez être connecté(e) pour accéder à cette page";
            header('Location: /users/login');
            exit;
        }
    }

    /**
     * Modifier une annonce
     * @param int $id 
     * @return void 
     */
    public function modifier(int $id){
        // On vérifie si l'utilisateur est connecté
        if(isset($_SESSION['user']) && !empty($_SESSION['user']['id'])){
            // On va vérifier si l'annonce existe dans la base
            // On instancie notre modèle
            $annoncesModel = new AnnoncesModel;

            // On cherche l'annonce avec l'id $id
            $annonce = $annoncesModel->find($id);

            // Si l'annonce n'existe pas, on retourne à la liste des annonces
            if(!$annonce){
                http_response_code(404);
                $_SESSION['erreur'] = "L'annonce recherchée n'existe pas";
                header('Location: /annonces');
                exit;
            }

            // On vérifie si l'utilisateur est propriétaire de l'annonce ou admin
            if($annonce->users_id !== $_SESSION['user']['id']){
                if(!in_array('ROLE_ADMIN', $_SESSION['user']['roles'])){
                    $_SESSION['erreur'] = "Vous n'avez pas accès à cette page";
                    header('Location: /annonces');
                    exit;
                }
            }

            // On traite le formulaire
            if(Form::validate($_POST, ['titre', 'description'])){
                // On se protège contre les failles XSS
                $titre = strip_tags($_POST['titre']);
                $description = strip_tags($_POST['description']);

                // On stocke l'annonce
                $annonceModif = new AnnoncesModel;

                // On hydrate
                $annonceModif->setId($annonce->id)
                    ->setTitre($titre)
                    ->setDescription($description);

                // On met à jour l'annonce
                $annonceModif->update();

                // On redirige
                $_SESSION['message'] = "Votre annonce a été modifiée avec succès";
                header('Location: /');
                exit;
            }


            $form = new Form;

            $form->debutForm()
                ->ajoutLabelFor('titre', 'Titre de l\'annonce :')
                ->ajoutInput('text', 'titre', [
                    'id' => 'titre',
                    'class' => 'form-control',
                    'value' => $annonce->titre
                ])
                ->ajoutLabelFor('description', 'Texte de l\'annonce')
                ->ajoutTextarea('description', $annonce->description, [
                    'id' => 'description',
                    'class' => 'form-control'
                ])
                ->ajoutBouton('Modifier', ['class' => 'btn btn-primary'])
                ->finForm()
            ;

            // On envoie à la vue
            $this->render('annonces/modifier', ['form' => $form->create()]);

        }else{
            // L'utilisateur n'est pas connecté
            $_SESSION['erreur'] = "Vous devez être connecté(e) pour accéder à cette page";
            header('Location: /users/login');
            exit;
        }
    }
}