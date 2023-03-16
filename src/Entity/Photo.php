<?php
namespace App\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Photo extends AbstractController
{
    private $path;
    private $filename;
    private $comments;

    public function __construct($path, $filename, $comments)
    {
        $this->path = $filename;       
        $this->filename = $filename;
        $this->comments = $comments;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
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