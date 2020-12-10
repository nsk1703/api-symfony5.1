<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Post;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class PostPersister implements DataPersisterInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

//    Referencie la classe a persister et donne le moment d'intervention
    public function supports($data): bool
    {
//        Il dit: "Je ne veux travailler que si $data est une instance de Post
//        Maintenant l'on peut effectuer d'autres methodes si celle-ci est verifie
        return $data instanceof Post;
    }

    public function persist($data)
    {
//        1. Mettre une date de creation sur l'article
        $data->setCreatedAt(new \DateTime());

//        2. Demander a doctrine de persister les donnees
        $this->em->persist($data);
        $this->em->flush();
    }

    public function remove($data)
    {
//        1.Demande a doctrine de supprimer l'article
        $this->em->remove($data);
        $this->em->flush();
    }
}