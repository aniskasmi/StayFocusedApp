<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('auth_login');
        }
        return $this->render('pages/home.html.twig');
    }
}
