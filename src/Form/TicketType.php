<?php

namespace App\Form;

use App\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                "label" => 'Titre du ticket',
                'attr' => ['class' =>  'form-control'],
            ])
            ->add('content',TextType::class, [
                "label" => 'Contenue du ticket',
                'attr' => ['class' =>  'form-control'],
            ])
            ->add('etat' , ChoiceType::class, array(
                'label' => 'Etat',
                'attr' => ['class' => 'form-control'],
                'choices' => 
                [
                    'en cours' => 'encours',
                    'a faire' => 'afaire',
                    'clos' => 'clos',
                    'archiver' => 'archiver'
                ],
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                )
            );   
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
