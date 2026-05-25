<?php
// TODO: keep developing
namespace App\Controller;

use App\Entity\Marca;
use App\Entity\Modelo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class NewRepoItemController extends AbstractController
{
    #[Route('/new/{type}', name: 'newItemGet', methods: 'GET')]
    public function GET(Request $request, string $type)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $returnParams = [];
        $type = strtolower(trim($type));
        switch ($type) {
            case 'brand':
                $returnParams["type"] = 'brand';
                break;
            case 'model':
                $returnParams["type"] = 'model';
                break;
            default:
                // user is fiddling with url maliciously, send them home
                return $this->redirectToRoute('home');
        }

        $error = $request->query->get("error");
        if ($error) {
            $returnParams['error'] = $error;
            return $this->render('new/new.html.twig', $returnParams);
        };
        return $this->render('new/new.html.twig', $returnParams);
    }

    #[Route('/new/{type}', name: 'newItemPost', methods: 'POST')]
    public function POST(Request $request, EntityManagerInterface $entityManager, string $type)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $returnParams = [];
        $type = strtolower(trim($type));

        $params = [ //photo is checked separately
            'name' => [
                'necessary' => true,
                'length' => 45,
            ]
        ];

        switch ($type) {
            case 'brand':
                $returnParams["type"] = 'brand';
                $params['url'] = [
                    'necessary' => false,
                    'length' => 250
                ];
                break;
            case 'model':
                $returnParams["type"] = 'model';
                break;
            default:
                // user is fiddling with url maliciously, send them home
                return $this->redirectToRoute('home');
        }

        $args = [];
        foreach ($params as $k => $v) {
            $paramet = strip_tags(strtolower(trim($request->request->get($k))));

            if (!$paramet || strlen($paramet) <= 0) {
                return $this->getErrorArray($type, "A necessary field is missing content.");
            }
            if (strlen($paramet) > $v['length']) {
                return $this->getErrorArray($type, "A necessary field has too much content.");
            }

            $args[$k] = $paramet;
        }

        /* BEGIN photo */
        $photo = $request->files->get('photo');
        /* An uploaded file looks like this: (https://github.com/symfony/symfony/blob/7.2/src/Symfony/Component/HttpFoundation/File/UploadedFile.php)
            object(Symfony\Component\HttpFoundation\File\UploadedFile)#18 (7) {
                ["originalName":"Symfony\Component\HttpFoundation\File\UploadedFile":private]=> string(9) "ayuda.png"
                ["mimeType":"Symfony\Component\HttpFoundation\File\UploadedFile":private]=> string(9) "image/png"
                ["error":"Symfony\Component\HttpFoundation\File\UploadedFile":private]=> int(0)
                ["originalPath":"Symfony\Component\HttpFoundation\File\UploadedFile":private]=> string(9) "ayuda.png"
                ["test":"Symfony\Component\HttpFoundation\File\UploadedFile":private]=> bool(false)
                ["pathName":"SplFileInfo":private]=> string(45) "tempPathRedactedForPrivacyReasons\php6371.tmp"
                ["fileName":"SplFileInfo":private]=> string(11) "php6371.tmp"
            }
        */

        // if photo is null, no photo; if error is different than 0 it error'd out.
        if ($photo != null) {
            if (!$photo->isValid()) {
                if (in_array($photo->getError(), [1, 2])) { //photo is too big for current config
                    return $this->getErrorArray($type, "Selected photo is too big. Please select a lighter photo.");
                }
                return $this->getErrorArray($type);
            }
            //check if uploaded file matches mime type image/*, regardless of case
            try {
                if (!preg_match("/^image\/.+$/i", $photo->getMimeType())) {
                    return $this->getErrorArray($type, "El archivo subido no es una foto.");
                }
            } catch (LogicException) {
                // Se necesitan extensiones para adivinar el mime de ciertos archivos: error si no se sabe
                // SI ESTÁS AQUÍ PORQUE FALLA ESTO TIENES QUE DESCOMENTAR O AÑADIR "extension=fileinfo" en php.ini
                return $this->getErrorArray($type, "Error CONFIGERR: Ha habido un error interno.");
            }
        }
        /* END photo */

        /**
         * Here we used to insert the content into the DB directly.
         * Because we're now saving the path in the DB instead (and using an ORM), we have to:
         *  1. create new entity
         *  2. flush (?)
         *  3. getlastID
         *  4. move files to paths with the newObjectID
         *  5. mutate original new object (setPhoto and setFile)
         *  6. flush
         */
        switch ($returnParams['type']) {
            case 'brand':
                $newObject = new Marca();
                $newObject->setnombreMarca($args['name']);
                break;
            case 'model':
                $newObject = new Modelo();
                $newObject->setnombreModelo($args['name']);
                break;
            default:
                // this shouldn't trigger but the linter is SCREAMING at me
                return $this->redirectToRoute('home');
        }
        $entityManager->persist($newObject);
        $entityManager->flush();

        $id = $newObject->getId();

        try {
            if ($photo) {
                $photoExt = $photo->guessExtension();
                if ($photoExt == null) {
                    throw new FileException();
                }

                $photo->move($this->getParameter('kernel.project_dir') . "/assets/image/" . $returnParams['type'] . "/", $id . "." . $photoExt);
                $newObject->setImage($photoExt);
                // FIXME: please.
            }
        } catch (FileException) {
            $entityManager->detach($newObject);
            $entityManager->flush();

            return $this->getErrorArray($type);
        }

        $entityManager->flush();
        return $this->redirectToRoute('singlePostView', ['id' => $id]);
    }

    private function getErrorArray(string $type, string $error = "Someting went wrong. We're trying hard to fix it!")
    {
        return $this->redirectToRoute('newItemGet', ['type' => $type, 'error' => $error]);
    }
}
