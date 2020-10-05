<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use App\Entity\Answer;
use App\Entity\Field;
use App\Entity\Content;
use App\Entity\Assignment;
use App\Entity\Task;

use App\Form\Type\AnswerType;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AnswerController extends Controller
{



    /**
     * @Method("post")
     * @Route("/answer/new", name="create_answer")
     * @return Response
     */
    function createAction(Request $request, SessionInterface $session){

        $em = $this->getDoctrine()->getManager();
        $repAnswer = $this->getDoctrine()->getRepository(Answer::class);
        $repField = $this->getDoctrine()->getRepository(Field::class);
        $repTask = $this->getDoctrine()->getRepository(Task::class);
        $repContent = $this->getDoctrine()->getRepository(Content::class);
        $repAssignment = $this->getDoctrine()->getRepository(Assignment::class);

        // load parents
        $assignment = $repAssignment->find($request->request->get('answer')['assignment']);
        $content = $repContent->find($request->request->get('answer')['content']);
        $field = $repField->find($request->request->get('answer')['field']);
        $task = $assignment->getTask();

        // init answer
        $answer = new Answer();
        $answer->setAssignment($assignment);
        $answer->setContent($content);
        $answer->setField($field);

        // check si le champs a bien été paramétré
        if(!$repTask->hasToBeAnswered($task->getId(),$field->getId())){
            $session->getFlashBag()->add('danger', 'entity.Answer.flash.bad_field');
            return $this->redirectToRoute('fill_assignment', ['id' => $assignment->getId()]);
        }

        // check if not already answered
        if($repAnswer->isAlreadyAnswered($assignment->getId(), $content->getId(), $field->getId())){
            $session->getFlashBag()->add('danger', 'entity.Answer.flash.already_answered');
            return $this->redirectToRoute('fill_assignment', ['id' => $assignment->getId()]);
        }

        // check feeling format
        if(!in_array($field->getFeeling()->getFormat(), [1,2,3])){
            $session->getFlashBag()->add('danger', 'entity.Assignment.flash.bad_format');
            return $this->redirectToRoute('fill_assignment', ['id' => $assignment->getId()]);
        }

        $entry = $request->request->get('answer')['value'];
        // si saisie vide
        if($entry === ''){
            $session->getFlashBag()->add('danger', 'entity.Answer.flash.mandatory');
            return $this->redirectToRoute('fill_assignment', ['id' => $assignment->getId()]);
        }

        // si ok on créé le form (pour validators)
        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // on rempli l'entity
            $answer->setUpdatedAt(new \DateTime());
            $answer->setValue($entry);

            // si tout est saisie au niveau de l'assignation on la passe en statut 1
            $nbAnswers = $repAssignment->getAssignmentProgressionDone($assignment->getId());
            if($nbAnswers['nb_answers']+1 == $nbAnswers['nb_contents']*$nbAnswers['nb_fields']){
                $assignment->setStatus(1);
                // si tout est saisie au niveau de la tâche on la passe en statut 2
                $nbAnswers = $repTask->getTaskProgressionDone($task->getId());
                if($nbAnswers['nb_answers']+1 == $nbAnswers['nb_contents']*$nbAnswers['nb_fields']*$nbAnswers['nb_assignments']){
                    $task->setStatus(2);
                    $em->persist($task);
                }

                // on sotck et on va à l'accueil
                $em->persist($assignment);
                $em->persist($answer);
                $em->flush();
                $session->getFlashBag()->add('success', 'entity.Answer.flash.assignment_done');
                return $this->redirectToRoute('tasks');
            }

            // on stock en base
            $em->persist($answer);
            $em->flush();

            // on va à la saisie suivante
            $session->getFlashBag()->add('success', 'entity.Answer.flash.entry_saved');
            return $this->redirectToRoute('fill_assignment', ['id' => $assignment->getId()]);

        }else{
            // on affiche les erreurs
            $session->getFlashBag()->add('danger', 'error');
            return $this->redirectToRoute('fill_assignment', ['id' => $assignment->getId()]);
        }

    }



}
