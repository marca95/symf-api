<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{

  #[Route(path: "/api/login", name: "api_login", methods: ['POST'])]
  public function ApiLogin(){
    
    $user = $this->getUser();

    $userData = [
      'email' => $user->getEmail(),
      'first_name' => $user->getFirstName(),
      'last_name' => $user->getLastName(), 
    ];

    return $this->json($userData);
  }
}