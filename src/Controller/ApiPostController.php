<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ApiPostController extends AbstractController
{
    /**
     * @Route("/api/post", name="api_post_index", methods={"GET"})
     * @param PostRepository $postRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(PostRepository $postRepository)
    {
        $posts = $postRepository->findAll();

//        Symfony simplifie ces deux etapes en utilisant le "serializer";
//        1ere Etape:
        // 'Normalisation': Processus permettant de transformer des objets en un tableau associatifs simples
//        Grace a 'Groups' on ne recuperera que: id, title, content et createdAt
//        $postsNormalises = $normalizer->normalize($posts, Null, ['groups' => 'post:read']);

//        2eme Etape:
        // 'Json_decode' permet de transformer un tableau ou un objet en json
//        $json = json_encode($postsNormalises);

//        Celle-ci fait les deux premieres a la fois
//        $json = $serializer->serialize($posts, 'json', ['groups' => 'post:read']);

//        Pour un bon affichage et traitement on utilise la classe Response du HttpFoundation;
//        qui prends en parametres le json, le statut et l'entete content-Type qui dira au navigateur
//        que l'on veut afficher purement du contenu json

//        $response = new Response($json, 200, [
//            "Content-Type" => "application/json"
//        ]);

//        Celui-ci fait la technique qui est l'affichage sous format json
//        $response = new JsonResponse($json, 200, [], true);

//        Celui-ci serialise et affiche, donc fait les deux dernieres a la fois
        $response = $this->json($posts, 200, [], ['groups' => 'post:read']);

        return $response;
    }

    /**
     * @Route("/api/post", name="api_post_store", methods={"POST"})
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function store(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
//        En cas de mauvais format de Json le try est necessaire
        try{
            $jsonDecode = $request->getContent();
            $post = $serializer->deserialize($jsonDecode, Post::class, 'json');

            $errors = $validator->validate($post);

            if(count($errors) > 0){
                return $this->json($errors, 400);
            }

            $post->setCreatedAt(new \DateTime());

            $entityManager->persist($post);
            $entityManager->flush();
        }catch (NotEncodableValueException $en){
                return $this->json([
                    'status' => 400,
                    'message' => $en->getMessage()
                ], 400);
        }

        return $this->json($post, 201, [], ['groups' => 'post:read']);
    }
}
