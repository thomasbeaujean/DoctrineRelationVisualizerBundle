<?php

namespace tbn\DoctrineRelationVisualizerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use tbn\DoctrineRelationVisualizerBundle\Entity\Entity;
use tbn\DoctrineRelationVisualizerBundle\Entity\AssociationEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Yaml;

/**
 * @Route("/visualizer")
 */
class VisualizerController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        //get the list of manager names
        $managerName = $this->container->getParameter('doctrine.default_entity_manager');

        return $this->redirect($this->generateUrl('visualizer_manager', array('managerName' => $managerName)));
    }

    /**
     * @Route("/manager/{managerName}",name="visualizer_manager")
     * @Template()
     */
    public function managerAction($managerName)
    {
        //get the list of manager names
        $managers = $this->container->getParameter('doctrine.entity_managers');

        $managerNames = array();

        foreach ($managers as $managerNameIndex => $manager) {
            $managerNames[] = $managerNameIndex;
        }

        return array('managerName' => $managerName, 'managerNames' => $managerNames);
    }

    /**
     * @Route("/save/{connectionName}")
     * @Template()
     */
    public function saveAction(Request $request, $connectionName = null)
    {
        $jsonEntities = $request->request->get('entities');

        $entities = json_decode($jsonEntities, true);

        $this->get('tbn.entity_relation_visualizer.entity_service')->saveEntitiesPositions($entities, $connectionName);

        $response = new Response();
        $response->setContent(json_encode(array()));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/data/{connectionName}")
     */
    public function getDataAction($connectionName)
    {
        $entities = $this->get('tbn.entity_relation_visualizer.entity_service')->getEntities($connectionName);

        $response = new Response();
        $response->setContent(json_encode(array('entities' => $entities)));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
