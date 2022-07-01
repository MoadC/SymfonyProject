<?php

namespace App\Form;

use App\Entity\Developer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class DeveloperType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('FullName')
            ->add('AboutMe')
            ->add('Email',EmailType::class)
            ->add('Adress')
            ->add('Speciality',ChoiceType::class, [
                    'choices'  => [
                        'Front End' => 'Front End',
                        'Back End' => 'Back End',
                        'Full-Stack' => 'FullStack',],
                        ])
            ->add('BirthDate',DateType::class, [ 'widget' => 'single_text',])
            ->add('YearsExp')
            ->add('WorkStyle',ChoiceType::class, [
                    'choices'  => [
                        'Remote' => 'Remote',
                        'In Office' => 'In Office',
                        'Hybrid' => 'Hybrid',],
                        ])
            ->add('languages')
            ->add('File', FileType::class, [
                'mapped' => false,
                'data_class'=>null,
                'required'=> false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/*',
                            'image/jpeg',
                            'image/jpg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Developer::class,
        ]);
    }
}
