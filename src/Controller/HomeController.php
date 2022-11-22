<?php

namespace App\Controller;

use App\Repository\CmsCopyRepository;
use App\Repository\CmsPhotoRepository;
use App\Repository\StaticTextRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(StaticTextRepository $staticTextRepository, CmsCopyRepository $cmsCopyRepository, CmsPhotoRepository $cmsPhotoRepository): Response
    {
        return $this->render('user-templates/home.html.twig', [
            'photos' => $cmsPhotoRepository->findAll(),
            'homepage1' => $cmsPhotoRepository->findOneBy([
                'name' => 'HomePage1'
            ]),
            'specialising1' => $cmsPhotoRepository->findOneBy([
                'name' => 'Specialising1'
            ]),
            'specialising2' => $cmsPhotoRepository->findOneBy([
                'name' => 'Specialising2'
            ]),
            'specialising3' => $cmsPhotoRepository->findOneBy([
                'name' => 'Specialising3'
            ]),

        ]);
    }

    /**
     * @Route("/aboutSN", name="aboutSN", methods={"GET"})
     */
    public function aboutSN(StaticTextRepository $staticTextRepository): Response
    {
        return $this->render('home/aboutSN.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


    /**
     * @Route("/webdesign", name="webdesign", methods={"GET"})
     */
    public function webDesign(StaticTextRepository $staticTextRepository): Response
    {
        return $this->render('template_parts/webdesign.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


    /**
     * @Route("/homeaddress", name="/homeaddress", methods={"GET"})
     */
    public function homeAddress(StaticTextRepository $staticTextRepository): Response
    {
        return $this->render('template_parts/homeaddress.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


}
