<?php

namespace Flower\ProjectBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Flower\ModelBundle\Entity\Project\Project;

class ProjectMembershipType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('user')
                ->add('project', null, array('required' => false))
                ->add('memberRole', 'choice', array(
                    'choices' => array(
                        'admin' => 'Admin',
                        'product_owner' => 'Product Owner',
                        'technical_leader' => 'Technical Leader',
                        'developer' => 'Developer',
                        'tester' => 'Tester',
                    )
                ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Project\ProjectMembership',
            'translation_domain' => 'Project',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'project_membership';
    }

}
