<?php

namespace App\Controller;

use App\Entity\CmsPhoto;
use App\Entity\Languages;
use App\Form\LanguagesType;
use App\Repository\CmsPhotoRepository;
use App\Repository\LanguagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/languages')]
class LanguagesController extends AbstractController
{
    #[Route('/index', name: 'languages_index', methods: ['GET'])]
    public function index(LanguagesRepository $languagesRepository): Response
    {
        return $this->render('languages/index.html.twig', [
            'languages' => $languagesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'languages_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LanguagesRepository $languagesRepository): Response
    {
        $language = new Languages();
        $form = $this->createForm(LanguagesType::class, $language);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->isSubmitted() && $form->isValid()) {
                $languagesRepository->add($language, true);
                $icon = $form->get('icon')->getData();
                if ($icon) {
                    $safeFilename = $language->getAbbreviation();
                    $newFilename = $safeFilename . '.' . $icon->guessExtension();
                    try {
                        $icon->move(
                            $this->getParameter('icon_directory'),
                            $newFilename
                        );
                        $language->setIcon($newFilename);
                    } catch (FileException $e) {
                        die('Import failed');
                    }
                }
            }
            $languagesRepository->add($language, true);

            return $this->redirectToRoute('languages_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('languages/new.html.twig', [
            'language' => $language,
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'languages_show', methods: ['GET'])]
    public function show(Languages $language): Response
    {
        return $this->render('languages/show.html.twig', [
            'language' => $language,
        ]);
    }

    #[Route('/edit/{id}', name: 'languages_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Languages $language, LanguagesRepository $languagesRepository): Response
    {
        $form = $this->createForm(LanguagesType::class, $language);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->isSubmitted() && $form->isValid()) {
                $languagesRepository->add($language, true);
                $icon = $form->get('icon')->getData();
                if ($icon) {
                    $safeFilename = $language->getAbbreviation();
                    $newFilename = $safeFilename . '.' . $icon->guessExtension();
                    try {
                        $icon->move(
                            $this->getParameter('icon_directory'),
                            $newFilename
                        );
                        $language->setIcon($newFilename);
                    } catch (FileException $e) {
                        die('Import failed');
                    }
                }
            }
            $languagesRepository->add($language, true);

            return $this->redirectToRoute('languages_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('languages/edit.html.twig', [
            'language' => $language,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'languages_delete', methods: ['POST'])]
    public function delete(Request $request, Languages $language, LanguagesRepository $languagesRepository, EntityManagerInterface $entityManager): Response
    {
        $file_name = $language->getIcon();
        if ($file_name) {
            $file = $this->getParameter('icon_directory') . $file_name;
            if (file_exists($file)) {
                unlink($file);
            }
            $language->setIcon('');
            $entityManager->flush();

        }

        if ($this->isCsrfTokenValid('delete' . $language->getId(), $request->request->get('_token'))) {
            $languagesRepository->remove($language, true);
        }

        return $this->redirectToRoute('languages_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/delete_language_icon/{id}", name="language_icon_delete", methods={"POST", "GET"})
     */
    public function deleteLanguageIcon(int $id, Request $request, Languages $language, EntityManagerInterface $entityManager)
    {
        $referer = $request->headers->get('referer');
        $file_name = $language->getIcon();
        if ($file_name) {
            $file = $this->getParameter('icon_directory') . $file_name;
            if (file_exists($file)) {
                unlink($file);
            }
            $language->setIcon('');
            $entityManager->flush();

        }
        return $this->redirect($referer);
    }
    /**
     * @Route("/set/default/language/{id}", name="select_default_language")
     */
    public function setDefaultLanguage(Request $request, Languages $languages)
    {
        $session = $request->getSession();
        $session->set('selected_language', $languages->getLanguage());
        return $this->redirect($request->headers->get('referer'));
    }




}
