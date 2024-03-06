<?php

namespace Tbn\DoctrineRelationVisualizerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Tbn\DoctrineRelationVisualizerBundle\Services\EntityService;
use Tbn\DoctrineRelationVisualizerBundle\Services\Persister;

#[Route('/_visualizer')]
class VisualizerController extends AbstractController
{
    public function __construct(
        private EntityService $entityService,
        private Persister $persister,
        private SerializerInterface $serializer,
        private string $defaultEntityManagerName,
        private $entityManagers,
        private $displayColumns,
        private $areaWidth,
        private $areaHeight,
    ) {
    }

    #[Route('/', name:"visualizer_base")]
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('visualizer_manager', array('managerName' => $this->defaultEntityManagerName)));
    }

    #[Route('/manager/{managerName}', name:"visualizer_manager")]
    public function managerAction(string $managerName)
    {
        $managerNames = array();

        foreach ($this->entityManagers as $managerNameIndex => $manager) {
            $managerNames[] = $managerNameIndex;
        }

        return $this->render('@DoctrineRelationVisualizer/Visualizer/manager.html.twig', array('managerName' => $managerName, 'managerNames' => $managerNames));
    }

    #[Route('/save/{connectionName}')]
    public function saveAction(Request $request, ?string $connectionName = null)
    {
        $jsonEntities = $request->request->get('entities');

        $entities = json_decode($jsonEntities, true);

        $this->persister->save($entities, $connectionName);

        $response = new Response();
        $response->setContent(json_encode(array()));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route('/data/{connectionName}')]
    public function getDataAction(string $connectionName)
    {
        $entitiesPositions = $this->persister->load($connectionName);
        $entities = $this->entityService->getEntities($entitiesPositions, $connectionName);

        $jsonContent = $this->serializer->serialize([
            'entities' => $entities,
            'displayColumns' => $this->displayColumns,
            'areaWidth' => $this->areaWidth,
            'areaHeight' => $this->areaHeight,
        ], 'json');

        $response = new Response();
        $response->setContent($jsonContent);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
