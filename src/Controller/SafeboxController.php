<?php

namespace App\Controller;

use App\Entity\Safebox;
use App\Entity\SafeboxItem;
use App\Entity\Token;
use App\Form\SafeboxItemType;
use App\Form\SafeboxType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SafeboxController extends FOSRestController
{
    /**
     * @Rest\Post("/safebox")
     * @Rest\View(serializerGroups={"safebox"})
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
                throw new HttpException(422, 'Password not valid');
            }

            // Check if safebox already exists
            if ($this->getDoctrine()->getRepository(Safebox::class)->findBy(['name' => $safebox->getName()])) {
                throw new HttpException(409, 'Safebox already exists');
            }

            $safebox->setPassword(password_hash($safebox->getPlainPassword(), PASSWORD_DEFAULT));

            $this->getDoctrine()->getManager()->persist($safebox);
            $this->getDoctrine()->getManager()->flush();

            return View::create($safebox,Response::HTTP_CREATED);
        }

        return View::create($form);
    }

    /**
     * @Rest\Get("/safebox/{id}/open")
     * @Rest\View(serializerGroups={"token"})
     */
    public function openSafebox($id, Request $request)
    {
        /** @var Safebox $safebox */
        $safebox = $this->getDoctrine()->getRepository(Safebox::class)->find($id);

        if (!$safebox) {
            throw new HttpException(404, 'Requested safebox does not exist');
        }

        $password = str_replace('Bearer ', '', $request->headers->get('Authorization'));

        if (!password_verify($password, $safebox->getPassword())) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid password');
        }

        $expirationTime = $request->get('expirationTime') ? $request->get('expirationTime') : 180;
        $expirationDateTime = new \DateTime();
        $expirationDateTime->add(new \DateInterval('PT' . $expirationTime . 'S'));

        $token = new Token();
        $token->setExpirationTime($expirationDateTime);
        $token->generateToken();

        $safebox->setToken($token);

        $this->getDoctrine()->getManager()->persist($token);
        $this->getDoctrine()->getManager()->persist($safebox);
        $this->getDoctrine()->getManager()->flush();

        return View::create($token);
    }

    /**
     * @Rest\Post("/safebox/{safeboxId}")
     */
    public function addSafeboxItem($safeboxId, Request $request)
    {
        /** @var Safebox $safebox */
        $safebox = $this->getDoctrine()->getRepository(Safebox::class)->find($safeboxId);

        if(!$safebox) {
            throw new HttpException(404, 'Requested safebox does not exist');
        }

        $token = str_replace('Bearer ', '', $request->headers->get('Authorization'));
        if ($token != $safebox->getToken()->getToken()) {
            throw new HttpException(401, 'Specified token does not match');
        }

        $safeboxItem = new SafeboxItem();
        $form = $this->createForm(SafeboxItemType::class, $safeboxItem);
        $data = json_decode($request->getContent(), true);

        $form->submit($data);

        if ($form->isValid()) {
            /** @var Safebox $safebox */
            $safeboxItem = $form->getData();

            $this->getDoctrine()->getManager()->persist($safeboxItem);

            $safebox->addItem($safeboxItem);

            $this->getDoctrine()->getManager()->flush();

            return View::create($safebox,200);
        }

        return View::create($form, 422);
    }

    /**
     * @Rest\Get("/safebox/{safeboxId}")
     */
    public function getSafeboxContent($safeboxId, Request $request)
    {
        /** @var Safebox $safebox */
        $safebox = $this->getDoctrine()->getRepository(Safebox::class)->find($safeboxId);

        if(!$safebox) {
            throw new HttpException(404, 'Requested safebox does not exist');
        }

        $token = str_replace('Bearer ', '', $request->headers->get('Authorization'));
        if ($token != $safebox->getToken()->getToken()) {
            throw new HttpException(401, 'Specified token does not match');
        }

        $response = ['items' => []];
        foreach ($safebox->getItems() as $item) {
            $response['items'][] = $item->getContent();
        }

        return View::create($response, 200);
    }
}