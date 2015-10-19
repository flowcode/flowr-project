<?php

namespace Flower\ProjectBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Estimation
 *
 */
class Estimation
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
     * @var integer
     *
     * @ORM\Column(name="ratioAdmin", type="integer")
     */
    protected $ratioAdmin;

    /**
     * @var integer
     *
     * @ORM\Column(name="ratioTesting", type="integer")
     */
    protected $ratioTesting;

    /**
     * @var integer
     *
     * @ORM\Column(name="dailyWorkingHours", type="integer")
     */
    protected $dailyWorkingHours;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Clients\Opportunity")
     * @JoinColumn(name="opportunity_id", referencedColumnName="id")
     *
     * */
    protected $opportunity;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Clients\Account")
     * @JoinColumn(name="account_id", referencedColumnName="id")
     *
     * */
    protected $account;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Project\Project")
     * @JoinColumn(name="project_id", referencedColumnName="id")
     * */
    protected $project;

    /**
     * @OneToMany(targetEntity="\Flower\ModelBundle\Entity\Project\EstimationItem", mappedBy="estimation")
     * */
    protected $items;

    function __construct()
    {
        $this->items = new ArrayCollection();
    }

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
     * @return Estimation
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
     * Set ratioAdmin
     *
     * @param integer $ratioAdmin
     * @return Estimation
     */
    public function setRatioAdmin($ratioAdmin)
    {
        $this->ratioAdmin = $ratioAdmin;

        return $this;
    }

    /**
     * Get ratioAdmin
     *
     * @return integer
     */
    public function getRatioAdmin()
    {
        return $this->ratioAdmin;
    }

    /**
     * Set ratioTesting
     *
     * @param integer $ratioTesting
     * @return Estimation
     */
    public function setRatioTesting($ratioTesting)
    {
        $this->ratioTesting = $ratioTesting;

        return $this;
    }

    /**
     * Get ratioTesting
     *
     * @return integer
     */
    public function getRatioTesting()
    {
        return $this->ratioTesting;
    }

    /**
     * Set dailyWorkingHours
     *
     * @param integer $dailyWorkingHours
     * @return Estimation
     */
    public function setDailyWorkingHours($dailyWorkingHours)
    {
        $this->dailyWorkingHours = $dailyWorkingHours;

        return $this;
    }

    /**
     * Get dailyWorkingHours
     *
     * @return integer
     */
    public function getDailyWorkingHours()
    {
        return $this->dailyWorkingHours;
    }

    /**
     * Set account
     *
     * @param \Flower\ModelBundle\Entity\Clients\Account $account
     * @return Estimation
     */
    public function setAccount(\Flower\ModelBundle\Entity\Clients\Account $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return \Flower\ModelBundle\Entity\Clients\Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set project
     *
     * @param \Flower\ModelBundle\Entity\Project\Project $project
     * @return Estimation
     */
    public function setProject(\Flower\ModelBundle\Entity\Project\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Flower\ModelBundle\Entity\Project\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Add items
     *
     * @param \Flower\ModelBundle\Entity\Project\EstimationItem $items
     * @return Estimation
     */
    public function addItem(\Flower\ModelBundle\Entity\Project\EstimationItem $items)
    {
        $this->items[] = $items;

        return $this;
    }

    /**
     * Remove items
     *
     * @param \Flower\ModelBundle\Entity\Project\EstimationItem $items
     */
    public function removeItem(\Flower\ModelBundle\Entity\Project\EstimationItem $items)
    {
        $this->items->removeElement($items);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Set opportunity
     *
     * @param \Flower\ModelBundle\Entity\Clients\Opportunity $opportunity
     * @return Estimation
     */
    public function setOpportunity(\Flower\ModelBundle\Entity\Clients\Opportunity $opportunity = null)
    {
        $this->opportunity = $opportunity;

        return $this;
    }

    /**
     * Get opportunity
     *
     * @return \Flower\ModelBundle\Entity\Clients\Opportunity 
     */
    public function getOpportunity()
    {
        return $this->opportunity;
    }
}
