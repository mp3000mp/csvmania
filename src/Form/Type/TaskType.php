<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Feeling;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){

        $builder
            ->add('name', TextType::class, [
                'label' => 'entity.Task.name'
            ])
            ->add('priority', ChoiceType::class, [
                'choices'  => [
                    'entity.Task.priority1' => 1,
                    'entity.Task.priority2' => 2,
                    'entity.Task.priority3' => 3,
                ],
                'data' => 2,
                'label' => 'entity.Task.priority'
            ])
            /*
            ->add('language', ChoiceType::class, [
                'choices'  => [
                    'menu.language.en' => 'en',
                    'menu.language.es' => 'es',
                    'menu.language.fr' => 'fr',
                ],
                'data' => 'en',
                'label' => 'menu.language.label'
            ])*/
            ->add('language', HiddenType::class, [
                'data'=>'en'
            ]) // pour l'instant on gère que les inputs en anglais
            ->add('deadline', DateType::class, [
                'label' => 'entity.Task.deadline',
                'data' => new \DateTime(date('Y-m-d',mktime(0,0,0,date('m'),date('d')+14,date('Y'))))
            ])
            ->add('nb_answer_needed', IntegerType::class, [
                'attr' => ['min' => 1],
                'label' => 'entity.Task.nb_answer_needed',
                'data' => 1
            ])
            ->add('file', CsvType::class, [
                'label' => 'dnd_file.file',
                'mapped' => false
            ])
            ->add('fields', FieldsType::class, [
                'class' => Feeling::class,
                'choice_label' => function ($f) {
                    return $f->getLabel();
                },
                'multiple' => true,
                'label' => 'entity.Field.labels',
                'mapped' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => 'entity.Task.add'
            ]);

    }

}