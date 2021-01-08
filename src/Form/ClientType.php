<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Length;
class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',  EmailType::class, [
                'attr' => ['class' =>  'form-control'],
            ])
            ->add('entreprise', null,[
                'label' => 'entreprise',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('actif', null,[
                'label' => 'actif',
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('plainPassword',  PasswordType::class, [
                'required' => false,
                'label' => 'mot de passe',
                'attr' => ['class' =>  'form-control'],
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe dois faire au minimum 6 caractéres',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
