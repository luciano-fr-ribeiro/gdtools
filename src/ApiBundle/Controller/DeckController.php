<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\Project;
use ApiBundle\Entity\Deck;
use ApiBundle\Form\Type\DeckType;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class DeckController extends FOSRestController
{
    /**
     * Retrieves all decks related to a project as a collection.
     *
     * @ApiDoc(
     *  description = "Gets a collection of Decks related to a Project",
     *  output = { "class" = "ApiBundle\Entity\Deck", "collection" = true },
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     * @ParamConverter("project", class="ApiBundle\Entity\Project")
     *
     * @param Project $project The project witch the desired Decks belongs to.
     * @return Response
     */
    public function getProjectDecksAction(Project $project)
    {
        $decks = $this
            ->get('api.request_service')
            ->find('ApiBundle:Deck', ['project' => $project]);


        return $this->handleView(
            $this->view($decks, Codes::HTTP_OK)
                ->setTemplate('ApiBundle:Default:show.html.twig')
        );
    }

    /**
     * Retrieves a single Deck.
     *
     * @ApiDoc(
     *  description = "Retrieves a Deck for a given id",
     *  output = "ApiBundle\Entity\Deck",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the Deck is not found"
     *  }
     * )
     * @ParamConverter("deck", class="ApiBundle\Entity\Deck")
     *
     * @param Deck $deck
     * @return Response
     */
    public function getDeckAction(Deck $deck)
    {
        return $this->handleView(
            $this->view($deck, Codes::HTTP_OK)
                ->setTemplate('ApiBundle:Default:show.html.twig')
        );
    }

    /**
     * Creates a new Deck.
     *
     * @ApiDoc(
     *  description = "Creates a new Deck",
     *  statusCodes = {
     *      201 = "Returned when a new Deck has been created",
     *      400 = "Returned when the submitted form has errors"
     *  }
     * )
     *
     * @param Request $request The data to create the new Deck.
     * @return Response
     */
    public function postDeckAction(Request $request)
    {
        $deck = new Deck();
        $form = $this->createForm(DeckType::class, $deck);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($deck);
            $em->flush();

            return $this->handleView(
                $this->routeRedirectView(
                    'api_get_deck',
                    ['deck' => $deck->getId()],
                    Codes::HTTP_CREATED
                )
            );
        }

        return $this->handleView($this->view($form));
    }

    /**
     * Erases a Deck.
     *
     * @ApiDoc(
     *  description = "Removes a Deck for a given id",
     *  statusCodes = {
     *      201 = "When the Deck has been removed",
     *      404 = "When the Deck has not been found"
     *  }
     * )
     * @ParamConverter("deck", class="ApiBundle\Entity\Deck")
     *
     * @param Deck $deck The Deck to be erased.
     * @return Response
     */
    public function deleteDeckAction(Deck $deck)
    {
        $deckId = $deck->getId();

        $em = $this->getDoctrine()->getManager();
        $em->remove($deck);
        $em->flush();

        return $this->handleView(
            $this->routeRedirectView(
                'api_get_deck',
                ['deck' => $deckId],
                Codes::HTTP_NO_CONTENT
            )
        );
    }

    /**
     * Updates a Deck.
     *
     * @ApiDoc(
     *  description = "Updates the data of a Deck for a given id with the submitted data",
     *  statusCodes = {
     *      201 = "When the project has been updated",
     *      400 = "When the submitted data is invalid",
     *      404 = "When the Deck has not been found"
     *  }
     * )
     * @ParamConverter("deck", class="ApiBundle\Entity\Deck")
     *
     * @param Deck $deck The deck to be updated.
     * @param Request $request Data to update the Deck.
     * @return Response
     */
    public function putDeckAction(Deck $deck, Request $request)
    {
        $form = $this->createForm(DeckType::class, $deck);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($deck);
            $em->flush();

            return $this->handleView(
                $this->routeRedirectView(
                    'api_get_deck',
                    ['deck' => $deck->getId()],
                    Codes::HTTP_NO_CONTENT
                )
            );
        }

        return $this->handleView($this->view($form));
    }
}
