<?php

namespace App\Http\Controller;

use App\Http\Controller\AbstractController;
use App\Http\Form\AccountEditType;
use App\Http\Form\RegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'account_index')]
    public function register(): Response
    {
        return $this->render('account/index.html.twig');
    }
    
    #[Route('/account/edit', name: 'account_edit')]
    public function edit(Request $request): Response
    {
        $form = $this->createForm(AccountEditType::class, $this->getUser());
        $form->handleRequest($request);
    
        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->flashErrors($form);
            } else {
                $this->addFlash(
                    'success',
                    "Votre modification a bien été enregistrée, vous pouvez revenir sur vos changements tant qu'ils n'ont pas été validés"
                );
            }
        }
    
        return $this->render('account/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    #[Route('/account/referral', name: 'account_referral')]
    public function referral(): Response
    {
        return $this->render('account/referral.html.twig');
    }
}
