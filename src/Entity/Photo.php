<?php
namespace App\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Photo extends AbstractController
{
    private $filename;
    private $comments;

    public function __construct($filename, $comments)
    {
        $this->filename = $filename;
        $this->comments = $comments;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function setComments($comments)
    {
        $this->comments = $comments;
    }

}