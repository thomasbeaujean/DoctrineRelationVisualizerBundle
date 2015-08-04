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
        return array();
    }

    /**
     * @Route("/save")
     * @Template()
     */
    public function saveAction(Request $request)
    {
        $jsonEntities = $request->request->get('entities');

        $entities = json_decode($jsonEntities, true);

        $this->get('tbn.entity_relation_visualizer.entity_service')->saveEntitiesPositions($entities);

        $response = new Response();
        $response->setContent(json_encode(array()));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/data")
     */
    public function getDataAction()
    {
        $entities = $this->get('tbn.entity_relation_visualizer.entity_service')->getEntities();

        $response = new Response();
        $response->setContent(json_encode(array('entities' => $entities)));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
