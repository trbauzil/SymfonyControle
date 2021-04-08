<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/liste", name="api_projects_list")
     */
    public function listProjects(Request $request, ProjectRepository $projectRepository)
    {
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 20);
        $projects = $projectRepository->getAllProjects($offset, $limit);
        
        // format JSON
        return $this->json([
            'count' => $projectRepository->countProjects(),
            'data' => $projects
        ]);
    }


    /**
     * @Route("/api/products/{id}", name="api_task_by_project_id")
     */
    public function userById(TaskRepository $taskRepository, $id)
    {
        //Récupère les tasks liées à un project id 
        $tasks = $taskRepository->getByProjectId($id);
        $nbTasks = count($tasks);

        if ($tasks === null) {
            throw new NotFoundHttpException();
        }
        // format JSON
        return $this->json([
            'count' => $nbTasks,
            'data' => $tasks
        ]);
    }

}
