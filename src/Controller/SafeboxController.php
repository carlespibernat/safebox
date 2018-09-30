<?php

namespace App\Controller;

use App\Entity\Safebox;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Rest\View(serializerGroups={"safebox"})
 */
class SafeboxController extends FOSRestController
{
    /**
     * @Rest\Post("/safebox")
     */
    public function postSafebox()
    {
        $safebox = new Safebox();
        $safebox->setId(1);

        return View::create($safebox,Response::HTTP_CREATED);
    }
}