<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Feeling;

class AnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){

        $answer = $options['data'];
        $field = $answer->getField();
        $feeling = $field->getFeeling();

        $builder
            ->add('assignment', IntegerType::class, [
                'attr' => ['style' => 'display: none;'],
                'data' => $answer->getAssignment()->getId(),
                'label' => false,
                'mapped' => false
            ])
            ->add('content', IntegerType::class, [
                'attr' => ['style' => 'display: none;'],
                'data' => $answer->getContent()->getId(),
                'label' => false,
                'mapped' => false
            ])
            ->add('field', IntegerType::class, [
                'attr' => ['style' => 'display: none;'],
                'data' => $field->getId(),
                'label' => false,
                'mapped' => false
            ]);

        // champs valeur selon format field
        if($feeling->getFormat() == 1){
            $builder->add('value', ChoiceType::class, [
                'choices'  => [
                    'form.empty' => '',
                    'form.yes' => 1,
                    'form.no' => 0
                ],
                'label' => false
            ]);
        }elseif($feeling->getFormat() == 2){
            $builder->add('value', IntegerType::class, [
                'label' => false,
                'attr' => ['min' => 0]
            ]);
        }elseif($feeling->getFormat() == 3){
            $builder->add('value', FeelingType::class, [
                'label' => false,
                'attr' => ['min' => 0, 'max' => 100]
            ]);
        }

        // submit
        $builder->add('save', SubmitType::class);

    }

}