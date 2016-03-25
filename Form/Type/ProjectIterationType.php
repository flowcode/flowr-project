<?php

namespace Flower\ProjectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectIterationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('project')
            ->add('status', 'choice', array(
                'choices' => array(
                    0 => "pending",
                    1 => "active",
                    2 => "done",
                ),
            ))
            ->add('startDate', 'collot_datetime', array('required' => false,
                'pickerOptions' =>
                    array('format' => 'dd/mm/yyyy  hh:ii',
                        'autoclose' => true,
                        'todayBtn' => true,
                        'todayHighlight' => true,
                        'keyboardNavigation' => true,
                        'language' => 'en',
                    )))
            ->add('dueDate', 'collot_datetime', array('required' => false,
                'pickerOptions' =>
                    array('format' => 'dd/mm/yyyy  hh:ii',
                        'autoclose' => true,
                        'todayBtn' => true,
                        'todayHighlight' => true,
                        'keyboardNavigation' => true,
                        'language' => 'en',
                    )));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Project\ProjectIteration',
            'translation_domain' => 'ProjectIteration',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ProjectIteration';
    }
}
