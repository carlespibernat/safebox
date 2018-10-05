<?php

namespace App\Controller;

use App\Entity\Safebox;
use App\Form\SafeboxType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Rest\View(serializerGroups={"safebox"})
 */
class SafeboxController extends FOSRestController
{
    /**
     * @Rest\Post("/safebox")
     */
    public function postSafebox(Request $request)
    {
        $safebox = new Safebox();
        $form = $this->createForm(SafeboxType::class, $safebox);
        $data = json_decode($request->getContent(), true);

        $form->submit($data);

        if ($form->isValid()) {
            /** @var Safebox $safebox */
            $safebox = $form->getData();

            if (!$safebox->hasValidPlainPassword()) {
                throw new HttpException(Response::HTTP_BAD_REQUEST, 'Password not valid');
            }

            $safebox->setPassword(password_hash($safebox->getPlainPassword(), PASSWORD_DEFAULT));

            $this->getDoctrine()->getManager()->persist($safebox);
            $this->getDoctrine()->getManager()->flush();

            return View::create($safebox,Response::HTTP_CREATED);
        }

        return View::create($form);
    }
}