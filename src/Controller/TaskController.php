<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use App\Entity\Task;
use App\Entity\Assignment;
use App\Entity\Field;
use App\Form\Type\TaskType;
use App\Entity\Content;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TaskController extends Controller
{


    /**
     * page d'accueil des users => vue des tâches en cours et tâches à piocher
     * @Route("/tasks", name="tasks")
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index()
    {
        // get user id
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $user_id = $user->getId();

        // get repositories
        $repAssignment = $this->getDoctrine()->getRepository(Assignment::class);
        $repTask = $this->getDoctrine()->getRepository(Task::class);

        // get ongoing assigmnents
        $ongoingAssignments = $repAssignment->getOngoingAssignments($user_id);

        // get next task
        $nextTodoTasks = $repTask->getNextTodoTasks($user_id, 3);

        // nb remainded to do task
        $nbTodoTasks = $repTask->getNbTodoTasks($user_id);

        return $this->render('task/index.html.twig', [
            'nbTodoTasks' => $nbTodoTasks,
            'ongoingAssignments' => $ongoingAssignments,
            'nextTodoTasks' => $nextTodoTasks
        ]);

    }



    /**
     * assigne la tâche au user si pas déjà fait puis redirige vers la saisie
     * @Route("/task/pick/{id}", name="pick_task", requirements={"id"="\d+"})
     * @param $id
     * @param SessionInterface $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function pick($id, SessionInterface $session){

        // get user id
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $user_id = $user->getId();

        // task repository
        $em = $this->getDoctrine()->getManager();
        $repTask = $this->getDoctrine()->getRepository(Task::class);
        $repAssignment = $this->getDoctrine()->getRepository(Assignment::class);

        // get task
        $task = $repTask->find($id);

        // if task status = 0
        if($task->getStatus() == 0){
            // if not already assigned to user
            if(!$repAssignment->isAlreadyAssigned($id,$user_id)){
                // create new assignment
                $assignment = new Assignment();
                $assignment->setAssignedAt(new \DateTime());
                $assignment->setAssignedTo($user);
                $assignment->setTask($task);
                $task->addAssignment($assignment);

                // si on a atteind l'objectif d'assignement on passe ne status 1=en cours de saisie
                $nbAssignments = $repAssignment->getNbAssignmentForTask($task->getId());
                if($nbAssignments+1 >= $task->getNbAnswerNeeded()){
                    $task->setStatus(1);
                }

                // on stock en base
                $em->persist($assignment);
                $em->persist($task);
                $em->flush();

                // on renvoi à la saisie
                $session->getFlashBag()->add('success', 'entity.Assignment.flash.success');
                return $this->redirectToRoute('fill_assignment', ['id'=>$assignment->getId()]);
            }else{
                $session->getFlashBag()->add('danger', 'entity.Assignment.flash.already_assigned');
            }
        }else{
            $session->getFlashBag()->add('danger', 'entity.Assignment.flash.no_more_assignation');
        }

        // sinon on retourne aux tâches
        return $this->redirectToRoute('tasks');

    }



    /**
     * visualisation de la tâche
     * @Route("/task/{id}", name="show_task", requirements={"id"="\d+"})
     * @param $id
     * @return Response
     */
    public function show($id){

        $repTask = $this->getDoctrine()->getRepository(Task::class);

        // get task
        $task = $repTask->find($id);
        // get contents
        $contents = $task->getContents();
        // get user assigned to
        $assignments = $task->getAssignments();

        return $this->render('task/show.html.twig', array(
            "task"=>$task,
            "contents"=>$contents,
            "assignments"=>$assignments
        ));
    }



    /**
     * @Method("get")
     * @Route("/task/add", name="add_task")
     * @return Response
     */
    public function addAction(){

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        return $this->render('task/add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Method("post")
     * @Route("/task/add", name="create_task")
     * @return Response
     */
    public function createAction(Request $request){

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            // on stock le csv - todo: il faudrait vérifier si c'est bon format
            $csvName = uniqid() . '.csv';
            $csvFolder = $_SERVER['DOCUMENT_ROOT'] . '/../files/csv/';
            $csvContent = base64_decode($form['file']->getData());
            $fp = fopen($csvFolder.$csvName, 'w');
            fwrite($fp, $csvContent);
            fclose($fp);

            // on créé un content par ligne
            $rows = explode("\n",$csvContent);
            $i = 1;
            while($i < count($rows)){
                if($rows[$i] != ''){
                    $cols = explode(';',$rows[$i]);
                    $content = new Content();
                    $content->setTask($task);
                    $content->setIdSparkup($cols[0]);
                    $content->setMessage($cols[1]);
                    $em->persist($content);
                }
                $i++;
            }

            // on créé la tâche
            $task->setCreatedBy($user);
            $task->setCreatedAt(new \DateTime());

            $feelings = $form['fields']->getData();
            foreach ($feelings as $feeling) {
                $field = new Field();
                $field->setTask($task);
                $field->setFeeling($feeling);
                $em->persist($field);
            }

            // on stock en base
            $em->persist($task);
            $em->flush();

            // on retourne à l'accueil
            return $this->redirectToRoute('admin/tasks');

        }else{
            // on affiche les erreurs
            return $this->render('task/add.html.twig', array(
                'form' => $form->createView(),
            ));
        }
    }

}