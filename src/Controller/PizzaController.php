<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Pizza;

class PizzaController extends AbstractController
{
  /**
   * @Route(path="/nouvelle-pizza", name="app_pizza_create")
   */
  public function create(Request $request): Response
  {
    $method = $request->getMethod();

    if ('POST' === $method) {
      $pizza = new Pizza();
      $pizza->setName($request->request->get('name'));
      $pizza->setDescription($request->request->get('description'));
      $pizza->setPrice((float)$request->request->get('price'));
      $pizza->setImage($request->request->get('image'));

      $manager = $this->getDoctrine()->getManager();
      $manager->persist($pizza);
      $manager->flush();

      return $this->redirectToRoute('app_pizza_display', [
        'id' => $pizza->getId()
      ]);
    }
    return $this->render('pizza/create.html.twig');
  }

  /**
   * @Route(path="/pizza/{id}", name="app_pizza_display")
   */
  public function display(int $id): Response
  {
    $repository = $this->getDoctrine()->getRepository(Pizza::class);
    $pizza = $repository->find($id);

    return $this->render('pizza/display.html.twig', [
      'pizza' => $pizza
    ]);
  }

  /**
   * @Route(path="/rechercher-pizza", name="app_pizza_search")
   */
  public function search(Request $request): Response
  {
    $name = $request->query->get('pizza');
    $repository = $this->getDoctrine()->getRepository(Pizza::class);
    $all = $repository->findAll();

    if (empty($name)) {
      $pizzas = $repository->findAll();
    } else {
      $pizzas = $repository->findBy(['name' => $name]);
    }

    return $this->render('pizza/search.html.twig', [
      "pizzas" => $pizzas,
      "all" => $all,
    ]);
  }

  /**
   * @Route(path="/modifier-pizza/{id}", name="app_pizza_edit")
   */
  public function edit(Request $request, int $id): Response
  {
    $method = $request->getMethod();
    $manager = $this->getDoctrine()->getManager();
    $pizza = $manager->getRepository(Pizza::class)->find($id);

    if ('POST' === $method && $request->request->get('valider') !== null) {
      $pizza->setName($request->request->get('name'));
      $pizza->setDescription($request->request->get('description'));
      $pizza->setPrice((float)$request->request->get('price'));
      $pizza->setImage($request->request->get('image'));
      $manager->flush();

      return $this->redirectToRoute('app_pizza_display', [
        'id' => $pizza->getId()
      ]);
    }

    if ('POST' === $method && $request->request->get('supprimer') !== null) {
      $manager->remove($pizza);
      $manager->flush();

      return $this->redirectToRoute('app_home_home');
    }

    return $this->render('pizza/edit.html.twig', [
      "pizza" => $pizza
    ]);
  }
}
