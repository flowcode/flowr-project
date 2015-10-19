<?php

namespace Flower\ProjectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EstimationItemType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('name')
            ->add('description')
            ->add('optimistic')
            ->add('pesimistic')
            ->add('estimation')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Project\EstimationItem',
            'translation_domain' => 'EstimationItem',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'estimationitem';
    }
}
