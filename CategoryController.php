<?php
namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category/{id}', name: 'category')]
    public function showCategory($id, ProductRepository $productRepository, CategoryRepository $categoryRepository, SessionInterface $session, Request $request): Response
    {
        // Récupérer la catégorie à partir de son ID
        $category = $categoryRepository->find($id);

        // Vérifier si la catégorie existe
        if (!$category) {
            throw $this->createNotFoundException('La catégorie n\'existe pas');
        }

        // Récupérer les produits de la catégorie à partir du ProductRepository
        $products = $productRepository->findBy(['category' => $category]);

        // Gérer l'ajout au panier
        if ($request->isMethod('POST')) {
            $productId = $request->request->get('product_id');
            $product = $productRepository->find($productId);

            if (!$product) {
                throw $this->createNotFoundException('Le produit n\'existe pas');
            }

            // Ajouter le produit au panier
            $cart = $session->get('cart', []);
            $cart[] = $product;
            $session->set('cart', $cart);

            // Rediriger vers une autre page ou afficher un message de succès
            return $this->redirectToRoute('category', ['id' => $category->getId()]);
        }

        // Rendre le template avec les produits de la catégorie
        return $this->render('category/show.html.twig', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
