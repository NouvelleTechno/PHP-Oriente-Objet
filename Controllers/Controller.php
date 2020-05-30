<?php
namespace App\Controllers;

abstract class Controller
{
    public function render(string $fichier, array $donnees = [], string $template = 'default')
    {
        // On extrait le contenu de $donnees
        extract($donnees);

        // On démarre le buffer de sortie
        ob_start();
        // A partir de ce point toute sortie est conservée en mémoire

        // On crée le chemin vers la vue
        require_once ROOT.'/Views/'.$fichier.'.php';

        // Transfère le buffer dans $contenu
        $contenu = ob_get_clean();

        // Template de page
        require_once ROOT.'/Views/'.$template.'.php';
    }
}
