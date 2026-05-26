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

    #[Route('/new/', name: 'new', methods: 'GET')]
    public function newIndex(EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $model = $em->getRepository(Modelo::class)->findOneBy([]);
        $brand = $em->getRepository(Marca::class)->findOneBy([]);

        return $this->render('new/index.html.twig', ['model' => $model, 'brand' => $brand]);
    }

    #[Route('/new/{type}', name: 'newItemGet', methods: 'GET')]
    public function GET(EntityManagerInterface $em, Request $request, string $type)
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
                $brands = $em->getRepository(Marca::class)->findAll();
                $returnParams['brands'] = $brands;
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

        switch ($type) { // setup parameters & filter out incorrect urls
            case 'brand':
                $returnParams["type"] = 'brand';
                $params['url'] = [
                    'necessary' => false,
                    'length' => 250
                ];
                break;
            case 'model':
                $returnParams["type"] = 'model';
                $params['brand'] = [
                    'necessary' => true,
                    'default' => 'errorDefault'
                ];
                break;
            default:
                // user is fiddling with url maliciously, send them home
                return $this->redirectToRoute('home');
        }

        $args = [];
        foreach ($params as $k => $v) {
            $paramet = strip_tags(strtolower(trim($request->request->get($k))));

            if (!$paramet || strlen($paramet) <= 0) {
                return $this->errorOut($returnParams, "The $k field is missing content.");
            }
            if (isset($v['length']) && strlen($paramet) > $v['length']) {
                return $this->errorOut($returnParams, "The $k field is over " . $v['length'] . " characters .");
            }
            if (isset($v['default']) && $v['default'] == $paramet) {
                return $this->errorOut($returnParams, "Choose an option for the $k field.");
            }

            $args[$k] = $paramet;
        }

        // check if the name already exists

        /* BEGIN photo */
        $photo = $request->files->get('pic');

        // if photo is null, no photo; if error is different than 0 it error'd out.
        if ($photo != null) {
            if (!$photo->isValid()) {
                if (in_array($photo->getError(), [1, 2])) { //photo is too big for current config
                    return $this->errorOut($returnParams, "Selected photo is too big. Please select a lighter photo.");
                }
                return $this->errorOut($returnParams);
            }
            //check if uploaded file matches mime type image/*, regardless of case
            try {
                if (!preg_match("/^image\/.+$/i", $photo->getMimeType())) {
                    return $this->errorOut($returnParams, "Uploaded file is not a photo.");
                }
            } catch (LogicException) {
                // Se necesitan extensiones para adivinar el mime de ciertos archivos: error si no se sabe
                // SI ESTÁS AQUÍ PORQUE FALLA ESTO TIENES QUE DESCOMENTAR O AÑADIR "extension=fileinfo" en php.ini
                return $this->errorOut($returnParams, "CONFIGERR: Internal error.");
            }
        } else {
            return $this->errorOut($returnParams, "The photo field is missing content");
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
                $newObject->setnombreMarca(strtoupper($args['name']));
                $newObject->seturlMarca($args['url']);
                break;
            case 'model':
                // Lookup brand to assign to object
                $selectedBrand = $entityManager->getRepository(Marca::class)->findOneBy(['idMarca' => $args['brand']]);
                if (is_null($selectedBrand)) {
                    return $this->redirectToRoute('home');
                }

                $newObject = new Modelo();
                $newObject->setnombreModelo($args['name']);
                $newObject->setMarca($selectedBrand);
                break;
            default:
                // this shouldn't trigger but the linter is SCREAMING at me
                return $this->redirectToRoute('home');
        }
        $entityManager->persist($newObject);
        $entityManager->flush();

        $id = $newObject->getId();

        if ($photo) {
            try {
                if (!preg_match("/^image\/.+$/i", $photo->getMimeType())) {
                    return $this->redirectToRoute('newPost', ['error' => "The uploaded file was not an image."]);
                }
                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/assets/images/' . $returnParams['type'] . 's/';

                $photoExt = $photo->guessClientExtension();
                $newName = sprintf('%s_%s_%s.%s', $returnParams['type'], $id, time(), $photoExt);
                $photo->move($uploadDir, $newName);

                $newObject->setImage($newName);
            } catch (FileException) {
                $entityManager->remove($newObject);
                $entityManager->flush();
                return $this->errorOut($returnParams);
            }
        }

        $entityManager->flush();
        switch ($type) {
            case 'brand':
                return $this->redirectToRoute('marca', ['name' => $newObject->getnombreMarca()]);
            case 'model':
                return $this->redirectToRoute('marca', ['name' => $newObject->getMarca()->getnombreMarca(), 'id' => $newObject->getId()]);
        }
    }

    private function errorOut(array $returnParams, string $error = "Someting went wrong. We're trying hard to fix it!")
    {
        return $this->redirectToRoute('newItemGet', ['type' => $returnParams['type'], 'error' => $error]);
    }
}
