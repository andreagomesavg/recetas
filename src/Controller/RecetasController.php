<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


use App\Entity\Receta;
use App\Repository\RecetaRepository;
use Doctrine\ORM\EntityManagerInterface;

class RecetasController extends AbstractController
{
    #[Route('/recetas', name: 'app_recetas', methods:["GET"])]
    public function listado(RecetaRepository $repo): JsonResponse
    {
        $listado = $repo->findAll();
        return $this->json($listado);
    }

    #[Route("/recetas", methods:["POST"])]
    public function crearReceta(EntityManagerInterface $emi): JsonResponse
    {
        $receta = new Receta();
        $receta->setNombre("Mi primera receta");
        $receta->setTexto("asi se cocina bien");
        $emi->persist($receta);
        $emi->flush();
        return $this-> json("Nueva receta añadida. ".$receta->getId(), Response::HTTP_CREATED);
    }
    #[Route("/recetas/{idReceta}", methods: ["GET" ])]
    public function buscarReceta(RecetaRepository $repo, int $idReceta): JsonResponse{
        $receta = $repo->find($idReceta);
        if($receta == null){
            return $this->json("Receta no encontrada", Response::HTTP_NOT_FOUND);
        }
        return $this->json($receta);
    }

    #[Route("/recetas/{idReceta}", methods: ["PATCH"])]
    public function actualizar(EntityManagerInterface $emi, RecetaRepository $repo, int $idReceta): JsonResponse {
        //buscar
        $receta = $repo -> find ($idReceta);
        if ($receta == null) {
            return $this->json("Receta no encontrada", Response::HTTP_NOT_FOUND);
        }
        //actualizar
        $receta->setNombre("Nombre modificado");
        //guardar
        $emi->flush();
        //devolver respuesta
        return $this->json("Receta actualizada", Response::HTTP_OK);
    }
    #[Route("/recetas/{idReceta}", methods: ["DELETE" ])]
    public function eliminarReceta(EntityManagerInterface $emi, RecetaRepository $repo, int $idReceta):JsonResponse{
        $receta = $repo->find($idReceta);
        if($receta==null){
            return $this->json("Receta no encontrada", Response::HTTP_OK);
        }
        $emi-> remove($receta);
        $emi-> flush();

        return $this->json(["Se ha eliminado la receta ".$idReceta]);
    }
}
