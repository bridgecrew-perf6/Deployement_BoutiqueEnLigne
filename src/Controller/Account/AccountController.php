<?php

namespace App\Controller\Account;

use App\Entity\Order;
use App\Entity\User;
use App\Form\EditProfileType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\AddressRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/account")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="account")
     */
    public function index(OrderRepository $repoOrder, AddressRepository $addressRepository): Response
    {
        $orders = $repoOrder->findBy(['isPaid' => true, 'user' => $this->getUser()], ['id' => 'DESC']);
        
        // $repoUser = $this->getUser()->getAddresses()->getValues();
        // dd($repoUser);
    
        return $this->render('account/index.html.twig',[
            'orders' => $orders,
            'addresses' => $addressRepository->findBy(['user' => $this->getUser()])
        ]);
    }


    /**
     * @Route("/profile/edit", name="account_profile_edit")
     */
    public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('account_message', 'Votre profil a été modifié avec succès');
            return $this->redirectToRoute('account');
        }

 

        return $this->renderForm('account/editProfile.html.twig', [
            'form' => $form,
        ]);
    }

    
    /**
     * @Route("/pass/edit", name="account_pass_edit")
     */
    public function editPass(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
    
        if($request->isMethod('POST')){
            $user = $this->getUser();
            //on vérifie si les deux mots de passe sont identiques
            if($request->request->get('pass') == $request->request->get('pass2')){
                $user->setPassword($userPasswordHasher->hashPassword($user, $request->get('pass')));
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('message', 'Mot de passe mis à jour avec succès');

                return $this->redirectToRoute('account');
            }else{
                $this->addFlash('error', 'Les deux mots de passe ne sont pas identiques');
            }
        }

        return $this->renderForm('account/editPass.html.twig');
    }

    /**
     * @Route("/order/{id}", name="account_order_details")
     */
    public function show(Order $order): Response
    {
        if(!$order || $order->getUser() !== $this->getUser()){
            return $this->redirectToRoute(("home"));
        }

        if(!$order->getIsPaid()){
            return $this->redirectToRoute(("account"));
        }
    
        return $this->render('account/detail_order.html.twig',[
            'order' => $order,
        ]);
    }
}
