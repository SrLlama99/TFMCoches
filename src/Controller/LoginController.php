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
        return $this->render('login.html.twig', ['last_username' => $lastUsername, 'loginError' => $error, 'registerError' => null, 'checked' => false, 'checked2' => false]);
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
            return $this->render('login.html.twig',["registerError"=>1, "loginError" => null, 'checked' => true, 'checked2' => false]);
        }else if($correoExistente){
            return $this->render('login.html.twig',["registerError"=>2, "loginError" => null, 'checked' => true, 'checked2' => false]);
        }else if($usuExistente){
            return $this->render('login.html.twig',["registerError"=>3, "loginError" => null, 'checked' => true, 'checked2' => false]);
        }else{
            $codeVerify = rand(100000,999999);
            $request->getSession()->set("codigoVerificacion", $codeVerify);
            $request->getSession()->set("correoCrear", $correo);
            $request->getSession()->set("usuarioCrear", $usuName);
            $request->getSession()->set("pass", $pass1);

            $email = (new TemplatedEmail())
                ->from(new Address('no-reply@VIVII.com', 'VIVII'))
                ->to($correo)
                ->subject('prueba de correo con Twig')
                ->htmlTemplate('email/correoCrear.html.twig')
                ->context([
                'nombre' => $usuName, 
                'valiCode' => $codeVerify
                ])
                ->embedFromPath(
                    $this->getParameter('kernel.project_dir') . '/public/images/logo.png',
                    'carsimg'
                );
            $mailer->send($email);

            return $this->render('crearCuenta.html.twig',["loginError"=>0]);
        }
    }

    #[Route('/procesaCuentaNueva', name:'procesaCuentaFinal')]
    public function procesa2(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher){
        if($request->request->get("codeValidation") == $request->getSession()->get("codigoVerificacion")){
            $usuName = $request->getSession()->get("usuarioCrear");
            $correo = $request->getSession()->get("correoCrear");
            $pass = $request->getSession()->get("pass");
            $user = new Users;

            $plaintextPassword = $pass;
            
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );

            $user->setUserName($usuName);
            $user->setUserMail($correo);
            $user->setUserPassword($hashedPassword);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->render('login.html.twig', ["registerError"=>5, "loginError" => null, 'checked' => true, 'checked2' => false]);
        }else{
             return $this->render('login.html.twig', ["registerError"=>4, "loginError" => null, 'checked' => true, 'checked2' => false]);
        }
    }

    #[Route('/procesaCambiarPass1', name:'procesaPass1')]
    public function procesa1(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer){
        $correoRecu = $request->request->get('usuMail');
        $request->getSession()->set("correoCambiar", $correoRecu);

        $eq = $entityManager->getRepository(Users::class);
        $correo = $eq->findOneBy(["UserMail" => $correoRecu]);

        if($correo){
            $codeVerify = rand(100000,999999);
            $request->getSession()->set("codeVerify", $codeVerify);
            $request->getSession()->set("correoCambiar", $correoRecu);

            $email = (new TemplatedEmail())
                ->from(new Address('no-reply@VIVII.com', 'VIVII'))
                ->to($correoRecu)
                ->subject('prueba de correo con Twig')
                ->htmlTemplate('email/correoRecuperar.html.twig')
                ->context([
                'nombre' => $correoRecu, 
                'valiCode' => $codeVerify
                ])
                ->embedFromPath(
                    $this->getParameter('kernel.project_dir') . '/public/images/logo.png',
                    'carsimg'
                );
            $mailer->send($email);

            return $this->render('cambiarPass/cambiarPass1.html.twig', ["registerError" => 0]);
        }else{
            return $this->render('login.html.twig',["registerError"=>1, "loginError" => null, 'checked2' => true, 'checked' => false]);
        }
    }
	
    #[Route('/codeVery', name:'codeVery')]
    public function veriCode(Request $request){
        $valiCode = $request->getSession()->get("codeVerify");
        $recoverValiCode = $request->request->get('recoverValiNumber');

        if($valiCode == $recoverValiCode){
            return $this->render('cambiarPass/cambiarPass2.html.twig', ["registerError"=>0]);
        }else{
            return $this->render('cambiarPass/cambiarPass1.html.twig', ["registerError"=>1]);
        }
    }

    #[Route('cambiarPassProcesa3', name:'procesaPass2')]
    public function cambiarPass(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher){
        $pass1 = $request->request->get('newPass');
        $pass2 = $request->request->get('newPass2');
        $correoSesion = $request->getSession()->get("correoCambiar");

        if($pass1 == $pass2){
            $eq = $entityManager->getRepository(Users::class);
            $usuario = $eq->findOneBy(['UserMail'=>$correoSesion]);
            $plaintextPassword = $pass1;

            $hashedPassword = $passwordHasher->hashPassword(
                $usuario,
                $plaintextPassword
            );
            if($usuario){
                $usuario->setUserPassword($hashedPassword);
                $entityManager->flush();
            }

            return $this->render('login.html.twig', ["registerError"=>2, "loginError" => null, 'checked' => false, 'checked2' => true]);
        }else{
            return $this->render('cambiarPass/cambiarPass2.html.twig', ["registerError"=>1]);
        }
    }

}
