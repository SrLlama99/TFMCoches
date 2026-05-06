<?php

namespace App\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Users;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


final class LoginController extends AbstractController
{
    #[Route('/', name:'app_index')]
    public function si():Response
    {
        return $this->redirectToRoute('app_login');
    }
    
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils)
    {
         // Comprueba si hubo algún error
         $error = $authenticationUtils->getLastAuthenticationError();

        // Recupera el último nombre de usuario que se probó
         $lastUsername = $authenticationUtils->getLastUsername();

        // Renderizar el formulario de login
        return $this->render('login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/crearcuentaProcesa', name:'precesaCuentaNueva')]
    public function procesa(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, UserPasswordHasherInterface $passwordHasher){
        $usuName = $request->request->get('usuName');
        $correo = $request->request->get('usuGmail');
        $pass1 = $request->request->get('pass1');
        $pass2 = $request->request->get('pass2');
        // $fecha = $request->request->get('birthDate');
        
        $eq = $entityManager->getRepository(Users::class);
        $correoExistente = $eq->findBy(['UserMail' => $correo]);
        $usuExistente = $eq->findBy(['UserName' => $usuName]);

        if($pass1 != $pass2){
            return $this->render('login.html.twig',["error"=>1]);
        }else if($correoExistente){
            return $this->render('login.html.twig',["error"=>2]);
        }else if($usuExistente){
            return $this->render('login.html.twig',["error"=>3]);
        }else{
            // $codeVerify = rand(100000,999999);
            // $request->getSession()->set("codigoVerificacion", $codeVerify);
            $request->getSession()->set("correoCrear", $correo);
            $request->getSession()->set("usuarioCrear", $usuName);
            $request->getSession()->set("pass", $pass1);
            // $request->getSession()->set("date", $fecha);

            // $email = (new TemplatedEmail())
            //     ->from(new Address('no-reply@VIVII.com', 'VIVII'))
            //     ->to($correo)
            //     ->subject('prueba de correo con Twig')
            //     ->htmlTemplate('email/correoCrear.html.twig')
            //     ->context([
            //     'nombre' => $usuName, 
            //     'valiCode' => $codeVerify
            //     ]);
            // $mailer->send($email);

            $user = new Users;

            $plaintextPassword = $pass1;
            
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );

            $user->setUserName($usuName);
            $user->setUserMail($correo);
            $user->setUserPassword($hashedPassword);
            // $user->setAdmin(false);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->render('login.html.twig',["error"=>5]);
        }
    }
	
}
