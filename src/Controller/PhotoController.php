<?php
/*
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PhotoController extends AbstractController
{
    #[Route('/photo', name: 'app_photo')]
    public function index(): Response
    {
        return $this->render('photo/index.html.twig', [
            'controller_name' => 'PhotoController',
        ]);
    }
}*/

namespace App\Controller;

use App\Entity\Photo;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PhotoController extends AbstractController
{
    public function index()
    {
        // Récupération de la liste des photos
       /* $photos = glob('../data/images/*.jpg');

        // Affichage de la liste des photos
        return $this->render('photo/list.html.twig', [
            'photos' => $photos,
        ]);*/

        /*$photos = [];
        $files = scandir('../data/images');
        foreach ($files as $file) {
            if (is_file('../data/images/'.$file)) {
                $photo = new Photo();
                $photo->setFilename($file);
                $photos[] = $photo;
            }
        }*/
        $photos = [];
        $files = scandir("../data/images");
        foreach ($files as $file) {
            if ($file === "." || $file === "..") {
                continue;
            }
            $path = "../data/images/" . $file;
            $comments = $this->readIPTC($path);
            $photo = new Photo($path, $file, $comments);
            $photos[] = $photo;
        }        
    
        return $this->render('photo/list.html.twig', [
            'photos' => $photos,
        ]);        
    }

    public function edit($filename)
    {
        // Chemin complet vers le fichier image
        $path = '../data/images/' . $filename;

        // Vérification de l'existence du fichier
        if (!file_exists($path)) {
            throw $this->createNotFoundException('Le fichier n\'existe pas');
        }

        // Récupération des données IPTC du fichier
        $iptc = iptcparse(file_get_contents($path));

        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupération des données du formulaire
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $keywords = $_POST['keywords'] ?? '';

            // Construction des données IPTC à partir des données du formulaire
            $iptc_new = [
                '2#005' => $title,
                '2#120' => $description,
                '2#025' => $keywords,
            ];

            // Mise à jour des données IPTC du fichier
            $data = iptcembed(iptc_make_tag_array($iptc_new), $path);
            file_put_contents($path, $data);

            // Redirection vers la page d'accueil
            return $this->redirectToRoute('app_home');
        }

        // Affichage du formulaire
        return $this->render('photo/edit.html.twig', [
            'filename' => $filename,
            'title' => $iptc['2#005'] ?? '',
            'description' => $iptc['2#120'] ?? '',
            'keywords' => $iptc['2#025'] ?? '',
        ]);
    }

    public function readIPTC($filename)
    {
        $info = @getimagesize($filename);
        //$iptc = iptcparse($info["APP13"]);
        if (array_key_exists('APP13', $info)) {
            $iptc = iptcparse($info["APP13"]);
        } else {
            $iptc = false;
        }
    
        if (!$iptc) {
            return false;
        }
    
        $iptc_data = array(
            'title' => '',
            'description' => '',
            'keywords' => '',
            'subject' => '',
            'city' => '',
            'state' => '',
            'country' => '',
            'author' => '',
            'author_title' => '',
            'caption' => '',
            'copyright' => '',
            'headline' => '',
            'source' => '',
            'photo_date' => '',
            'photo_time' => ''
        );
    
        if (isset($iptc["2#005"][0])) {
            $iptc_data['title'] = $iptc["2#005"][0];
        }
        if (isset($iptc["2#120"][0])) {
            $iptc_data['description'] = $iptc["2#120"][0];
        }
        if (isset($iptc["2#025"][0])) {
            $iptc_data['keywords'] = $iptc["2#025"][0];
        }
        if (isset($iptc["2#105"][0])) {
            $iptc_data['subject'] = $iptc["2#105"][0];
        }
        if (isset($iptc["2#090"][0])) {
            $iptc_data['city'] = $iptc["2#090"][0];
        }
        if (isset($iptc["2#095"][0])) {
            $iptc_data['state'] = $iptc["2#095"][0];
        }
        if (isset($iptc["2#101"][0])) {
            $iptc_data['country'] = $iptc["2#101"][0];
        }
        if (isset($iptc["2#080"][0])) {
            $iptc_data['author'] = $iptc["2#080"][0];
        }
        if (isset($iptc["2#085"][0])) {
            $iptc_data['author_title'] = $iptc["2#085"][0];
        }
        if (isset($iptc["2#120"][0])) {
            $iptc_data['caption'] = $iptc["2#120"][0];
        }
        if (isset($iptc["2#116"][0])) {
            $iptc_data['copyright'] = $iptc["2#116"][0];
        }
        if (isset($iptc["2#105"][0])) {
            $iptc_data['headline'] = $iptc["2#105"][0];
        }
        if (isset($iptc["2#115"][0])) {
            $iptc_data['source'] = $iptc["2#115"][0];
        }
        if (isset($iptc["2#055"][0])) {
            $iptc_data['photo_date'] = $iptc["2#055"][0];
        }
        if (isset($iptc["2#060"][0])) {
            $iptc_data['photo_time'] = $iptc["2#060"][0];
        }
    
        return $iptc_data;
    }   
}