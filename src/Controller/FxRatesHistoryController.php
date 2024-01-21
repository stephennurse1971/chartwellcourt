<?php

namespace App\Controller;

use App\Entity\FxRatesHistory;
use App\Form\FxRatesHistoryType;
use App\Repository\FxRatesHistoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/fx/rates/history")
 */
class FxRatesHistoryController extends AbstractController
{
    /**
     * @Route("/", name="fx_rates_history_index", methods={"GET"})
     */
    public function index(FxRatesHistoryRepository $fxRatesHistoryRepository): Response
    {
        return $this->render('fx_rates_history/index.html.twig', [
            'fx_rates_histories' => $fxRatesHistoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="fx_rates_history_new", methods={"GET", "POST"})
     */
    public function new(Request $request, FxRatesHistoryRepository $fxRatesHistoryRepository): Response
    {
        $fxRatesHistory = new FxRatesHistory();
        $form = $this->createForm(FxRatesHistoryType::class, $fxRatesHistory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $fxRatesHistoryRepository->add($fxRatesHistory);
            return $this->redirectToRoute('fx_rates_history_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('fx_rates_history/new.html.twig', [
            'fx_rates_history' => $fxRatesHistory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="fx_rates_history_show", methods={"GET"})
     */
    public function show(FxRatesHistory $fxRatesHistory): Response
    {
        return $this->render('fx_rates_history/show.html.twig', [
            'fx_rates_history' => $fxRatesHistory,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="fx_rates_history_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, FxRatesHistory $fxRatesHistory, FxRatesHistoryRepository $fxRatesHistoryRepository): Response
    {
        $form = $this->createForm(FxRatesHistoryType::class, $fxRatesHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fxRatesHistoryRepository->add($fxRatesHistory);
            return $this->redirectToRoute('fx_rates_history_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fx_rates_history/edit.html.twig', [
            'fx_rates_history' => $fxRatesHistory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="fx_rates_history_delete", methods={"POST"})
     */
    public function delete(Request $request, FxRatesHistory $fxRatesHistory, FxRatesHistoryRepository $fxRatesHistoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fxRatesHistory->getId(), $request->request->get('_token'))) {
            $fxRatesHistoryRepository->remove($fxRatesHistory);
        }

        return $this->redirectToRoute('fx_rates_history_index', [], Response::HTTP_SEE_OTHER);
    }
}
