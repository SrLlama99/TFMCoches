<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Modelo;

use function PHPUnit\Framework\isEmpty;

final class SearchController extends AbstractController
{
    //If this route path is changed, change the "virtual" route as well
    #[Route('/search/{query}', name: 'search')]
    public function search(EntityManagerInterface $em, string $query)
    {
        $error = ['ok' => false, 'errorReason' => 'There has been an error in your request, we\'re working hard to fix it!'];
        if (is_null($query)) {
            return $this->json($error);
        }
        $sanQuery = trim($query);

        $modelosEM = $em->getRepository(Modelo::class);
        $modelos = $modelosEM->findBySimilarity($sanQuery);

        if (is_null($modelos) | count($modelos) <= 0) {
            $error['errorReason'] = "No models found for your search.";
            return $this->json($error);
        }

        $data = [];
        foreach ($modelos as $modelo) {
            $data[] = [
                'id' => $modelo->getModeloId(),
                'nombre' => $modelo->getmodeloNombre(),
                'marca' => $modelo->getMarca()->getnombreMarca()
            ];
        }

        return $this->json([
            'ok' => true,
            'data' => $data
        ]);
    }

    /**
     * This ""virtual"" route is made solely because
     * I needed a way to resolve search -> /search without actually searching anything or passing args
     * to make the frontend hit a correct endpoint dynamically
     */
    #[Route('/search', name: 'search_virpath')]
    public function searchVirPath() {
        return $this->redirect('home'); // why would a normal client even be here?
    }
}
