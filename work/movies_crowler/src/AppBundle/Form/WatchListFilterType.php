<?php
/**
 * Created by PhpStorm.
 * User: sorina
 * Date: 25.06.2017
 * Time: 16:13
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WatchListFilterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', SearchType::class, [
            'required' => false,
            'label' => false,
        ]);
        $builder->add('rating', SearchType::class, [
            'required' => false,
            'label' => false,
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