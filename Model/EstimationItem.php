<?php

namespace Flower\ProjectBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * EstimationItem
 *
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
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    protected $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="optimistic", type="integer")
     */
    protected $optimistic;

    /**
     * @var integer
     *
     * @ORM\Column(name="pesimistic", type="integer")
     */
    protected $pesimistic;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Project\Estimation", inversedBy="items")
     * @JoinColumn(name="estimation_id", referencedColumnName="id")
     *
     * */
    protected $estimation;


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
     * @param \Flower\ModelBundle\Entity\Project\Estimation $estimation
     * @return EstimationItem
     */
    public function setEstimation(\Flower\ModelBundle\Entity\Project\Estimation $estimation = null)
    {
        $this->estimation = $estimation;

        return $this;
    }

    /**
     * Get estimation
     *
     * @return \Flower\ModelBundle\Entity\Project\Estimation 
     */
    public function getEstimation()
    {
        return $this->estimation;
    }
}
