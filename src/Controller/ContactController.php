<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\EmailModel;
use App\Entity\User;
use App\Form\ContactType;
use App\Services\EmailSender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/contact")
 */
class ContactController extends AbstractController
{
    
    /**
     * @Route("/", name="contact_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, EmailSender $emailsender): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contact);
            $entityManager->flush();


            // Envoi de l'email
            $user = (new User())
                    ->setEmail('f.tahiri@codeur.online')
                    ->setFirstname('BoutiqueEnLigne')
                    ->setLastname('Shopping');

            $email = (new EmailModel())
                    ->setTitle("Hello ".$user->getFullName()) 
                    ->setSubject("New contact from your website")
                    ->setContent("<br>From : ".$contact->getEmail()
                                ."<br> Name : ".$contact->getName()
                                ."<br> Subject : ".$contact->getSubject()
                                ."<br><br>".$contact->getContent());
                                
                               
            $emailsender->sendEmailNotificationByMailJet($user, $email);
         
            $contact = new Contact();
            
            
            $form = $this->createForm(ContactType::class, $contact);
            $this->addFlash('contact_success', 'Your message has been sent. An advisor will answer you very quickly !');
        }

        if($form->isSubmitted() && !$form->isValid()){
            $this->addFlash('contact_error', 'The form contains errors.Please correct and try again. !');
        }

        return $this->render('contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }

}
