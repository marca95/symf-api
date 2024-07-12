<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\SecurityAuthenticator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterController extends AbstractController
{
  #[Route(path: "/api/register", name: "api_register", methods: ['POST'])]
  public function register(ValidatorInterface $validator, SerializerInterface $serializer, Request $request, 
  UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): JsonResponse
  {

    if($this->getUser()){
      return new JsonResponse($serializer->serialize(['message' => 'Vous devez être déconnecté pour accéder à cette page'],'json'), 
      Response::HTTP_UNAUTHORIZED, [], true);
    }

    $newUser = $serializer->deserialize($request->getContent(), User::class, 'json');
    $error = $validator->validate($newUser);

    if($error->count() > 0){
      return new JsonResponse($serializer->serialize($error, 'json'), Response::HTTP_BAD_REQUEST, [], true);
    }
    $getPassword = $newUser->getPassword();
    // encode the plain password
      $newUser->setPassword(
            $userPasswordHasher->hashPassword(
            $newUser,
            $getPassword
        )
    );

      $entityManager->persist($newUser);
      $entityManager->flush();

      return new JsonResponse($serializer->serialize(['message' => 'Votre compte à été créé'], 'json'), 
      Response::HTTP_OK, ['accept' => 'application/json'], true);
  }
}