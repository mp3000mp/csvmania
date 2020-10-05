<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Task;

class AdminController extends Controller
{

    /**
     * @Route("/admin", name="admin")
     * @return Response
     */
    public function index()
    {
        return $this->redirectToRoute('admin/tasks');
    }

    /**
     * @Route("/admin/tasks", name="admin/tasks")
     * @return Response
     */
    public function tasks()
    {

        $icones = [
            'text-info',
            'text-warning',
            'text-success'
        ];

        // Tasks repository
        $repTask = $this->getDoctrine()->getRepository(Task::class);
        $dashboardList = $repTask->getDashboard(10);

        dump($dashboardList);

        return $this->render('admin/index.html.twig', array(
            'icones' => $icones,
            'dashboardList' => $dashboardList
        ));
    }

}