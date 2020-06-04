<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


//importamos el modelo para usar el formulario
use App\Entity\User;
use App\Form\RegisterType;

class UserController extends AbstractController
{
    
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        // Crear el formulario
        $user = new User();
        // $user = new App\Entity\User();
        $form = $this->createForm(RegisterType::class, $user);

        // Rellenar los objetos con los datos del form
        //unir el dato que viene de la request con el objeto que esta en el formulario
        $form->handleRequest($request);

        // comprobar si el formulario se ha enviado
        if($form->isSubmitted() && $form->isValid()){
            // Modificando el objeto para guardarlo
            $user->setRol('ROLE_USER');
            // $date_now = (new \DateTime())->format('d-m-Y H:i:s');
            $user->setCreatedAt(new \DateTime('now'));

            // cifrando la contraseña
            
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);

            // Guardar usuario
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('tasks');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function login(AuthenticationUtils $authenticationUtils) {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', array(
            'error' => $error,
            'last_username' => $lastUsername
        ));
    }
}
