<?php

namespace Flower\ProjectBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Flower\ModelBundle\Entity\Project\Project;

class ProjectType extends AbstractType
{

    private $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', null, array('required' => false))
            ->add('assignee')
            ->add('dailyWorkingHours', null, array('required' => false))
            ->add('estimated', null, array('required' => false))
            ->add('finished', null, array('required' => false))
            ->add('enabled', null, array('required' => false))
            ->add('type', 'choice', array(
                'choices' => array(
                    Project::type_fixed => 'fixed',
                    Project::type_ongoing => 'ongoing',
                )
            ))
            ->add('account')
            ->add('clientViewable')
            ->add('status');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Project\Project',
            'translation_domain' => 'Project',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'project';
    }

}
