<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\News;
use AppBundle\Form\NewsType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BlogController extends Controller
{
    /**
     * @Route("/accueil", name="accueil")
     */
    public function AccueilAction(Request $request)
    {
      $form = $this->createForm(NewsType::class);

      $em = $this->getDoctrine()->getManager();

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        $post = $form->getData();
        $em->persist($post);
        $em->flush();
      }

      $response = $this->render('blog/accueil.html.twig', [
        'form' => $form->createView(),
      ]);
      return $response;
    }

    /**
     * @Route("list", name="list_articles")
     */
    public function listAction()
    {
      $em = $this->getDoctrine()->getManager();

      $articles = $em->getRepository('AppBundle:News')->findAll();

      $response = $this->render('blog/vuearticle.html.twig', [
        'articles' => $articles,
      ]);
      return $response;
    }

    /**
      * @Route("article/view/{id}", name="article_view_url")
      */
    public function viewUrlAction($id)
    {
    // récupérer le manager d'entité de doctrine
      $em = $this->getDoctrine()->getManager();
    // récupérer un article
      $article = $em->getRepository("AppBundle:News")->find($id);

      $response = $this->render('blog/articleviewid.html.twig', [
          'article' => $article,
      ]);
        return $response;
    }

    /**
     * @Route("/article/edit/{id}", name="article_edit")
     */
    public function articleEditAction(Request $request, $id)
    {
        // récupérer l'article à modifier
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository("AppBundle:News")->find($id);

        // créer un builder de formulaire associé à cet article
        $formBuilder = $this->get('form.factory')->createBuilder(
            NewsType::class, $article
        );

        // à partir du formBuilder, on génère le formulaire
        $form = $formBuilder->getForm();

        // récupérer les données envoyées pour hydrater l'objet
        $form->handleRequest($request);

        // si le formulaire a été soumis, alors enregistrer l'objet user
        // dont les propriétés ont été automatiquement settées
        // par le composant formulaire
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('accueil');
        }

        return $this->render('blog/insert.html.twig', [
            'form' => $form->createView(),

        ]);

    }

  /**
 * @Route("/article/remove/{id}", name="article_remove_id")
 */
public function removeIdAction($id)
{
    // récupérer le manager
    $em = $this->getDoctrine()->getManager();
    // récupérer un seul article depuis la base de données
    $article = $em->getRepository("AppBundle:News")->find($id);
    // supprimer un article
    if ($article != null) {
        $em->remove($article);
    // enregistrement en BDD
        $em->flush();
    // apres suppression rediriger le visiteur sur accueilview
        return $this->redirectToRoute('accueil');
    }

}
}
