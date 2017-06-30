<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;

class MovieFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title',  SearchType::class, [
            'label' => false,
            'required' => false
        ]);

        $builder->add('year',  SearchType::class, [
            'label' => false,
            'required' => false
        ]);

        $builder->add('rating',  SearchType::class, [
            'label' => false,
            'required' => false
        ]);

        $builder->add('genre',  SearchType::class, [
            'label' => false,
            'required' => false
        ]);

        $builder->add('actors',  SearchType::class, [
            'label' => false,
            'required' => false
        ]);

        $builder->add('directors',  SearchType::class, [
            'label' => false,
            'required' => false
        ]);

        $builder->add('results', ChoiceType::class, [
            'required' => false,
            'label' => 'forms.result_per_page',
            'data' => '10',
            'choices' => [
                '10' => '10',
                '20' => '20',
                '50' => '50',
                '100' => '100',
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'GET'
        ]);
    }
}