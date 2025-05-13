<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\twigFunction;

require_once __DIR__ . '/vendor/autoload.php';


// Configuration du chargeur de templates
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/src/views'); // Chemin

// Création de l'environnement Twig
$twig = new \Twig\Environment($loader, [
    'cache' => false,
    'debug' => true
]);

$twig->addExtension(new \Twig\Extension\DebugExtension());

// Définir la fonction 'path' pour Twig
$function = new Twig\TwigFunction('path', function ($name) {
    $routes = [
        'connexion' => '?uri=connexion',
        'accueil' => '?uri=/',
        'annonce' => '?uri=annonce',
        'profil_pilote' => '?uri=profil_pilote',
        'profil_etudiant' => '?uri=profil_etudiant',
        'postuler' => '?uri=postuler',
        'recherche' => '?uri=recherche',
        'creation_entreprise' => '?uri=creation_entreprise',
        'creation_offre' => '?uri=creation_offre',
        'modifier_offre' => '?uri=modifier_offre',
        'supprimer_offre' => '?uri=supprimer_offre',
        'statistiques' => '?uri=statistiques',
        'mot_de_passe_oublie' => '?uri=mot_de_passe_oublie',
        'deconnexion' => '?uri=deconnexion'
    ];
    return $routes[$name] ?? '/'; // Retourne l'URL correspondante ou la racine par défaut
});

// Ajouter la fonction 'path' à l'environnement Twig
$twig->addFunction($function);

