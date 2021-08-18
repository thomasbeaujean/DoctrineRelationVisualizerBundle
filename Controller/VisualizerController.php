<?php

namespace tbn\DoctrineRelationVisualizerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use tbn\DoctrineRelationVisualizerBundle\Services\EntityService;

#[Route('/_visualizer')]
class VisualizerController extends AbstractController
{
    public function __construct(private EntityService $entityService)
    {

    }

    #[Route('/', name:"visualizer_base")]
    public function indexAction()
    {
        //get the list of manager names
        $managerName = $this->getParameter('doctrine.default_entity_manager');

        return $this->redirect($this->generateUrl('visualizer_manager', array('managerName' => $managerName)));
    }

    #[Route('/manager/{managerName}', name:"visualizer_manager")]
    public function managerAction(string $managerName)
    {
        //get the list of manager names
        $managers = $this->getParameter('doctrine.entity_managers');

        $managerNames = array();

        foreach ($managers as $managerNameIndex => $manager) {
            $managerNames[] = $managerNameIndex;
        }

        return $this->render('@DoctrineRelationVisualizer/Visualizer/manager.html.twig', array('managerName' => $managerName, 'managerNames' => $managerNames));
    }

    #[Route('/save/{connectionName}')]
    public function saveAction(Request $request, ?string $connectionName = null)
    {
        $jsonEntities = $request->request->get('entities');

        $entities = json_decode($jsonEntities, true);

        $this->entityService->saveEntitiesPositions($entities, $connectionName);

        $response = new Response();
        $response->setContent(json_encode(array()));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route('/data/{connectionName}')]
    public function getDataAction(string $connectionName)
    {
        $entities = $this->entityService->getEntities($connectionName);

        $displayColumns = $this->getParameter('tbn.entity_relation_visualizer.display_columns');
        $areaWidth = $this->getParameter('tbn.entity_relation_visualizer.area_width');
        $areaHeight = $this->getParameter('tbn.entity_relation_visualizer.area_height');

        $response = new Response();
        $response->setContent(json_encode(array(
            'entities' => $entities,
            'displayColumns' => $displayColumns,
            'areaWidth' => $areaWidth,
            'areaHeight' => $areaHeight)));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
