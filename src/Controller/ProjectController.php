<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Repository\ProjectRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    /**
     * @Route("/manager/liste", name="man_liste")
     */
    public function liste(ProjectRepository $projectRepository): Response
    {
        //Récupère la liste de tous les projects
        $projects = $projectRepository->findAll();

        return $this->render('project/liste.html.twig', [
            'projects' => $projects,
        ]);
    }


    /**
     * @Route("/manager/add", name="man_add")
     * fonction d'ajout d'un projet à la bdd
     */
    public function add(Request $request , EntityManagerInterface $entityManager): Response
    {
        if ($request->getMethod() == 'POST') {

            $project = new Project();
            $project->setName($request->request->get('name'));
            $project->setStartedAt(new DateTime('now', new DateTimeZone('Europe/Paris')));
            $project->setStatus("Nouveau");

            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('man_liste');
        }

        return $this->render('project/add.html.twig', [
        ]);
    }


    /**
     * @Route("/manager/management/{id}", name="man_management")
     * fonction pour gérer un projet (affiche les détails du projet)
     */
    public function management(Request $request, $id, ProjectRepository $projectRepository, EntityManagerInterface $entityManager): Response
    {

        $project = $projectRepository->findOneBy(array('id' => $id));

        if ($request->getMethod() == 'POST') {

            $status = $request->request->get('status');
            $project->setStatus($status);

            $entityManager->flush();

            return $this->render('project/management.html.twig', [
                'project' => $project,
            ]);
        }

        

        return $this->render('project/management.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * @Route("/manager/addTask/{id}", name="man_add_task")
     * ajouter une tache liée à un projet
     */
    public function addTask(Request $request, $id, ProjectRepository $projectRepository, EntityManagerInterface $entityManager): Response
    {      
        $project = $projectRepository->findOneBy(array('id' => $id));

        if ($request->getMethod() == 'POST') {

            $task = new Task();
            $task->setTitle($request->request->get('title'));
            $task->setDescription($request->request->get('description'));
            $task->setCreatedAt(new DateTime('now', new DateTimeZone('Europe/Paris')));
            $task->setProjectId($project);

            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('man_management', ['id' => $id]);
        }

        

        return $this->render('task/add.html.twig', [
            'project' => $project,
        ]);
    }
}
