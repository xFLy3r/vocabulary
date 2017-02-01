<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Word;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Word controller.
 *
 */
class WordController extends Controller
{
    /**
     * Lists all word entities.
     *
     * @Route("/{_locale}/{page}", name="homepage", requirements={"page": "\d+"})
     * @Route("/{page}", requirements={"page": "\d+"})
     * @Method("GET")
     */
    public function indexAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

       // $words = $em->getRepository('AppBundle:Word')->findAll();

       // $pagination = $this->get('knp_paginator')->paginate($words,
          //  $request->query->getInt('page', 1),
            //5);
        return $this->render('AppBundle:word:index.html.twig', array(
            'words' => null,
        ));
    }

    /**
     * Creates a new word entity.
     *
     * @Route("/new", name="word_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $word = new Word();
        $form = $this->createForm('AppBundle\Form\WordType', $word);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($word);
            $em->flush($word);

            return $this->redirectToRoute('word_show', array('id' => $word->getId()));
        }

        return $this->render('word/new.html.twig', array(
            'word' => $word,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a word entity.
     *
     * @Route("/word/{id}", name="word_show")
     * @Method("GET")
     */
    public function showAction(Word $word)
    {
        $deleteForm = $this->createDeleteForm($word);

        return $this->render('word/show.html.twig', array(
            'word' => $word,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing word entity.
     *
     * @Route("/word/{id}/edit", name="word_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Word $word)
    {
        $deleteForm = $this->createDeleteForm($word);
        $editForm = $this->createForm('AppBundle\Form\WordType', $word);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('word_edit', array('id' => $word->getId()));
        }

        return $this->render('word/edit.html.twig', array(
            'word' => $word,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a word entity.
     *
     * @Route("/word/{id}", name="word_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Word $word)
    {
        $form = $this->createDeleteForm($word);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($word);
            $em->flush($word);
        }

        return $this->redirectToRoute('word_index');
    }

    /**
     * Creates a form to delete a word entity.
     *
     * @param Word $word The word entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Word $word)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('word_delete', array('id' => $word->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
