<?php

namespace Flower\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * EstimationItem
 *
 * @ORM\Table(name="estimation_item")
 * @ORM\Entity(repositoryClass="Flower\ModelBundle\Repository\EstimationItemRepository")
 */
class EstimationItem
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="optimistic", type="integer")
     */
    private $optimistic;

    /**
     * @var integer
     *
     * @ORM\Column(name="pesimistic", type="integer")
     */
    private $pesimistic;

    /**
     * @ManyToOne(targetEntity="Estimation", inversedBy="items")
     * @JoinColumn(name="estimation_id", referencedColumnName="id")
     *
     * */
    private $estimation;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return EstimationItem
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return EstimationItem
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set optimistic
     *
     * @param integer $optimistic
     * @return EstimationItem
     */
    public function setOptimistic($optimistic)
    {
        $this->optimistic = $optimistic;

        return $this;
    }

    /**
     * Get optimistic
     *
     * @return integer
     */
    public function getOptimistic()
    {
        return $this->optimistic;
    }

    /**
     * Set pesimistic
     *
     * @param integer $pesimistic
     * @return EstimationItem
     */
    public function setPesimistic($pesimistic)
    {
        $this->pesimistic = $pesimistic;

        return $this;
    }

    /**
     * Get pesimistic
     *
     * @return integer
     */
    public function getPesimistic()
    {
        return $this->pesimistic;
    }

    /**
     * Set estimation
     *
     * @param \Flower\ModelBundle\Entity\Estimation $estimation
     * @return EstimationItem
     */
    public function setEstimation(\Flower\ModelBundle\Entity\Estimation $estimation = null)
    {
        $this->estimation = $estimation;

        return $this;
    }

    /**
     * Get estimation
     *
     * @return \Flower\ModelBundle\Entity\Estimation 
     */
    public function getEstimation()
    {
        return $this->estimation;
    }
}
