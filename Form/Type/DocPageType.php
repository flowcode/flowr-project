<?php

namespace Flower\ProjectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DocPageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('title')
            ->add('content', null, array('attr' => array('class' => 'textarea-wysihtml5')))
            ->add('project')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Project\DocPage',
            'translation_domain' => 'DocPage',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'docpage';
    }
}
