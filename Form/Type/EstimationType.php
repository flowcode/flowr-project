<?php

namespace Flower\ProjectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EstimationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name')
            ->add('ratioAdmin')
            ->add('ratioTesting')
            ->add('dailyWorkingHours')
            ->add('opportunity')
            ->add('account')
            ->add('project')
            ->add('clientViewable')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Project\Estimation',
            'translation_domain' => 'Estimation',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'estimation';
    }
}
