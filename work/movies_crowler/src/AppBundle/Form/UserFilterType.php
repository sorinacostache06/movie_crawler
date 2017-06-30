<?php
namespace AppBundle\Form;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Config\Definition\Exception\Exception;
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
class UserFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', SearchType::class, [
            'required' => false,
            'label' => false,
        ]);
        $builder->add('username', SearchType::class, [
            'required' => false,
            'label' => false,
        ]);

        $builder->add('joinDate', DateTimeType::class, [
            'required' => false,
            'label' => false,
            'widget' => 'single_text',
            'attr' => [
                'class' => 'js-datepicker'
            ]
        ]);

        $builder->add('email', SearchType::class, [
            'required' => false,
            'label' => false,
        ]);

        $builder->add('enabled', ChoiceType::class, [
            'required' => false,
            'label' => false,
            'data' => 'All',
            'choices' => [
                'All' => 'All',
                'user_management.form.enabled' => 1,
                'user_management.form.disabled' => 0,
            ],
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
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'GET',
        ]);
    }

    public function getName()
    {
        return 'user_filter';
    }
}