<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use App\Entity\Answer;
use App\Entity\Assignment;
use App\Entity\Content;
use App\Entity\Field;
use App\Entity\Feeling;

use App\Form\Type\AnswerType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AssignmentController extends Controller
{





    /**
     * page de saisie tâche
     * @Method("get")
     * @Route("/assignment/fill/{id}", name="fill_assignment", requirements={"id"="\d+"})
     * @param $id
     * @param SessionInterface $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function fill($id, SessionInterface $session){

        // task repository
        $repContent = $this->getDoctrine()->getRepository(Content::class);

        // get next entry
        $nextContent = $repContent->getNextEntry($id);

        if(count($nextContent) == 0){
            // no more entry => go to tasks
            $session->getFlashBag()->add('danger', 'entity.Assignment.flash.no_more_entry');
            return $this->redirectToRoute('tasks');
        }else{
            $nextContent = $nextContent[0];
            $assignment = $nextContent->getTask()->getAssignments()[0];
            $field = $nextContent->getTask()->getFields()[0];
            $feeling = $nextContent->getTask()->getFields()[0]->getFeeling();

            // si nouveau format on met un message et on se casse
            if(!in_array($feeling->getFormat(), [1,2,3])){
                $session->getFlashBag()->add('danger', 'entity.Assignment.flash.bad_format');
                return $this->redirectToRoute('tasks');
            }

            // on génère le formulaire
            $answer = new Answer();
            $answer->setAssignment($assignment);
            $answer->setField($field);
            $answer->setContent($nextContent);
            $answer->setAssignment($assignment);
            $form = $this->createForm(AnswerType::class, $answer, [
                'action' => $this->generateUrl('create_answer')
            ]);

            /*
            $formBuilder = $this->createFormBuilder($answer)
                ->setAction($this->generateUrl('create_answer'))
                ->add('assignment', IntegerType::class, [
                    'attr' => ['style' => 'display: none;'],
                    'data' => $assignment->getId(),
                    'label' => false
                ])
                ->add('content', IntegerType::class, [
                    'attr' => ['style' => 'display: none;'],
                    'data' => $nextContent->getId(),
                    'label' => false
                ])
                ->add('field', IntegerType::class, [
                    'attr' => ['style' => 'display: none;'],
                    'data' => $field->getId(),
                    'label' => false
                ]);

            // champs valeur selon format field
            if($feeling->getFormat() == 1){
                $formBuilder->add('value', ChoiceType::class, [
                    'choices'  => [
                        'Yes' => 1,
                        'No' => 0
                    ],
                    'label' => false
                ]);
            }elseif($feeling->getFormat() == 2){
                $formBuilder->add('value', IntegerType::class, [
                    'label' => false,
                    'attr' => ['min' => 0]
                ]);
            }elseif($feeling->getFormat() == 3){
                $formBuilder->add('value', FeelingType::class, [
                    'label' => false,
                    'attr' => ['min' => 0, 'max' => 100]
                ]);
            }else{
                // nouveau format ?
                $session->getFlashBag()->add('danger', 'entity.Assignment.flash.bad_format');
                return $this->redirectToRoute('tasks');
            }

            // submit
            $formBuilder->add('save', SubmitType::class);*/
            //$form = $formBuilder->getForm();

            // on affiche le formulaire
            return $this->render('assignment/fill.html.twig', [
                'form' => $form->createView(),
                'content'=>$nextContent,
                'feeling'=>$feeling
            ]);
        }
    }




}
