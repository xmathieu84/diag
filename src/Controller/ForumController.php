<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\ReonseForum;
use App\Entity\Sujet;
use App\Helper\EntityManagerTrait;
use App\Helper\PaginatorTrait;
use App\Helper\RequestTrait;
use App\Helper\SujetRepoTrait;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\CategoriesRepository;
use App\Repository\ReonseForumRepository;
use App\Repository\ThemeForumRepository;
use App\Service\choixTemplate;
use App\Service\DefinirDate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ForumController extends AbstractController
{
    use RequestTrait, SujetRepoTrait, PaginatorTrait, EntityManagerTrait;

    /**
     * @param choixTemplate $choixTemplate
     * @param ThemeForumRepository $themeForumRepository
     * @return Response
     * @Route("/forum", name="forum")
     * @Security("is_granted('ROLE_SALARIE')")
     */
    public function accueilForum(choixTemplate $choixTemplate,ThemeForumRepository $themeForumRepository)
    {
        $themes  = $themeForumRepository->findAll();
        $template = $choixTemplate->templateCg($this->getUser());

        return $this->render('forum/index.html.twig', [
            'themes' => $themes,
            'template'=>$template
        ]);
    }

    /**
     * @Route("/forum/categorie-{id}",name="categorie")
     * @Security("is_granted('ROLE_SALARIE')")
     * @param CategoriesRepository $categoriesRepository
     * @param int $id
     * @param choixTemplate $choixTemplate
     * @return Response
     */
    public function sujetForum(ThemeForumRepository $themeForumRepository, int $id,choixTemplate $choixTemplate,CategoriesRepository $categoriesRepository)
    {
        $theme = $themeForumRepository->find($id);
        if ($this->isGranted('ROLE_SALARIE')){
            $cible = 'otd';
        }
        elseif ($this->isGranted('ROLE_GRANDCOMPTE')|| $this->isGranted('ROLE_INSTITUTION')){
            $cible = 'gcInsti';
        }
        $template = $choixTemplate->templateCg($this->getUser());
        $categorie = $this->paginator->paginate($categoriesRepository->findBy(['theme'=>$theme,'cible'=>$cible,'statut'=>"publié"]), $this->request->query->getInt('page', 1), 8);

        return $this->render('forum/categorie.html.twig', [
            'categories' => $categorie,
            'template'=>$template,
            'theme'=>$theme
        ]);
    }


    /**
     * @Route("/creerSujet/{id}")
     * @Security("is_granted('ROLE_SALARIE')")
     * @param DefinirDate $definirDate
     * @param $id
     * @param ThemeForumRepository $themeForumRepository
     * @param choixTemplate $choixTemplate
     * @return JsonResponse
     * @throws Exception
     */
    public function creerSujet(DefinirDate $definirDate,$id, ThemeForumRepository $themeForumRepository, choixTemplate $choixTemplate)
    {

        $content =json_decode($this->request->getContent());

        if ($this->isGranted('ROLE_GRANDCOMPTE')||$this->isGranted('ROLE_INSTITUTION')){
            $cible = 'gcInsti';
        }elseif ($this->isGranted("ROLE_SALARIE")){
            $cible = "otd";
        }
        $message = $content->message;
        $titre = $content->titre;
        $theme = $themeForumRepository->findOneBy(['id' => $id]);
        $categorie  = new Categories();
        $categorie->setContenu($message)
            ->setDate($definirDate->aujourdhui())
            ->setTheme($theme)
            ->setCible($cible)
            ->setAuteur($this->getUser()->getEmail())
            ->setNom($titre);
        $this->manager->persist($categorie);
        $this->manager->flush();
        return (new JsonResponse())->setData($message);
    }


}
