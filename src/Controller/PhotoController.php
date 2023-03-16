<?php

namespace App\Controller;

use App\Entity\Photo;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PhotoController extends AbstractController
{
    public function index()
    {
        $photos = [];
        $files = scandir("../data/images");
        foreach ($files as $file) {
            if ($file === "." || $file === "..") {
                continue;
            }
            $path = "../data/images/" . $file;
            $comments = $this->readIPTC($file);
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

        $description = $this->readIPTC($filename);

        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupération des données du formulaire
            $description = $_POST['description'] ?? '';

            // Construction des données IPTC à partir des données du formulaire
            $iptc_new = [
                '2#120' => $description
            ];

            // Mise à jour des données IPTC du fichier

            $data = '';

            foreach($iptc_new as $tag => $string)
            {
                $tag = substr($tag, 2);
                $data .= $this->iptc_make_tag(2, $tag, $string);
            }

            $content = iptcembed($data, $path);

            file_put_contents($path, $content);      

            // Redirection vers la page d'accueil
            return $this->redirectToRoute('app_home');
        }

        // Affichage du formulaire
        return $this->render('photo/edit.html.twig', [
            'filename' => $filename,
            'description' => $description ?? '',
        ]);
    }

    public function readIPTC($file)
    {
        $path = "../data/images/" . $file;

        // Vérification de l'existence du fichier
        if (!file_exists($path)) {
            throw $this->createNotFoundException('Le fichier n\'existe pas');
        }

        // Récupération des données IPTC du fichier
        $iptc = iptcparse(file_get_contents($path));        
    
        if(isset($iptc["2#120"][0])){
            return $iptc["2#120"][0];
        }
        return "";

    }

    public function iptc_make_tag($rec, $data, $value)
    {
        $length = strlen($value);
        $retval = chr(0x1C) . chr($rec) . chr($data);
    
        if($length < 0x8000)
        {
            $retval .= chr($length >> 8) .  chr($length & 0xFF);
        }
        else
        {
            $retval .= chr(0x80) . 
                       chr(0x04) . 
                       chr(($length >> 24) & 0xFF) . 
                       chr(($length >> 16) & 0xFF) . 
                       chr(($length >> 8) & 0xFF) . 
                       chr($length & 0xFF);
        }
    
        return $retval . $value;
    }    
}