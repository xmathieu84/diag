<?php


namespace App\Controller;


use App\Entity\Categories;
use App\Entity\ReonseForum;
use App\Entity\ThemeForum;
use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;
use App\Repository\CategoriesRepository;
use App\Repository\ReonseForumRepository;
use App\Repository\SujetRepository;
use App\Repository\ThemeForumRepository;
use App\Service\DefinirDate;
use App\Service\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminForumController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    use EntityManagerTrait,RequestTrait;

    /**
     * @param ThemeForumRepository $themeForumRepository
     * @Route("/administrateur/editerTheme",name="theme")
     * @return Response
     */
    public function editerTheme(ThemeForumRepository $themeForumRepository){
        $themes = $themeForumRepository->findAll();



        return $this->render('administrateur/editerTheme.html.twig',[
            'themes'=>$themes
        ]);
    }

    /**
     * @return JsonResponse
     * @Route ("/administrateur/ajouterTheme")
     */
    public function ajouterTheme():JsonResponse{
        $nom = $this->request->getContent();
        $theme = new ThemeForum();
        $theme->setNom($nom);
        $this->manager->persist($theme);
        $this->manager->flush();

        return new JsonResponse();

    }

    /**
     * @param $id
     * @param ThemeForumRepository $themeForumRepository
     * @return JsonResponse
     * @Route ("/administrateur/supprimerTheme/{id}")
     */
    public function supprimerTheme($id,ThemeForumRepository $themeForumRepository):JsonResponse{
        $theme = $themeForumRepository->findOneBy(['id'=>$id]);
        foreach ($theme->getCategories() as $category){
            $category->setTheme(null);
            $this->manager->persist($category);
        }
        $this->manager->remove($theme);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @param $id
     * @param ThemeForumRepository $themeForumRepository
     * @param null $type
     * @return Response
     * @Route ("/administrateur/consulterReponse/{id}/{type}",name="editeCategorie")
     */
    public function creerCat($id,ThemeForumRepository $themeForumRepository,CategoriesRepository $categoriesRepository, $type =null):Response{
        $theme = $themeForumRepository->findOneBy(['id'=>$id]);
        if ($type){
            $categories = $categoriesRepository->findBy(['theme'=>$theme,'statut'=>$type]);
        }
        else{
            $categories = $categoriesRepository->findBy(['theme'=>$theme]);
        }
        return $this->render('administrateur/editerCategorie.html.twig',[
            'categories'=>$categories,
            'theme'=>$theme
        ]);
    }

    /**
     * @param $id
     * @param ThemeForumRepository $themeForumRepository
     * @return JsonResponse
     * @Route ("/administrateur/creerCat/{id}")
     */
    public function ajouterCat($id,ThemeForumRepository $themeForumRepository):JsonResponse{
        $theme = $themeForumRepository->findOneBy(['id'=>$id]);
        $contenu = $this->request->getContent();
        $categorie = new Categories();
        $categorie->setTheme($theme)
            ->setNom($contenu);
        $this->manager->persist($categorie);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @param int $id
     * @param CategoriesRepository $categoriesRepository
     * @return JsonResponse
     * @Route("/administrateur/supprimerCat/{id}")
     */
    public function supprimerCat(int $id,CategoriesRepository $categoriesRepository):JsonResponse{
        $categorie = $categoriesRepository->findOneBy(['id'=>$id]);
        foreach ($categorie->getSujets() as $sujet){
            $sujet->setCategorie(null);
            $this->manager->persist($sujet);
            $this->manager->flush();
        }
        $this->manager->remove($categorie);
        $this->manager->flush();

        return new JsonResponse();

    }

    /**
     * @param SujetRepository $sujetRepository
     * @return Response
     * @Route ("/administrateur/listeReponse",name="listeReponse")
     */
    public function listeReponse(SujetRepository $sujetRepository){

        $sujets = $sujetRepository->findByEtatReponse();

        return $this->render('administrateur/listeReponse.html.twig',[
            'sujets'=>$sujets
        ]);
    }

    /**
     * @param int $id
     * @param CategoriesRepository $categoriesRepository
     * @param string $reponse
     * @return RedirectResponse
     * @Route("/administrateur/validerReponse/{id}/{reponse}",name="validerRep")
     */
    public function validerReponse(int $id,CategoriesRepository $categoriesRepository,string $reponse,Mail $mail){
        $categorie=  $categoriesRepository->find($id);
        $categorie->setStatut($reponse);
        $this->manager->persist($categorie);
        $this->manager->flush();
        $mail->mailPropAdmin($categorie->getAuteur(),$reponse);
        return $this->redirect($this->request->headers->get('referer'));
    }


}