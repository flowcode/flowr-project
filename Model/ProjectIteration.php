<?php

namespace Flower\ProjectBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * ProjectIteration
 *
 */
class ProjectIteration
{

    const status_pending = 0;
    const status_active = 1;
    const status_done = 2;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Project\Project", inversedBy="iterations")
     * @JoinColumn(name="project_id", referencedColumnName="id")
     * */
    protected $project;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startDate", type="datetime", nullable=true))
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dueDate", type="datetime", nullable=true))
     */
    protected $dueDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    protected $status;

    /**
     * @OneToMany(targetEntity="Flower\ModelBundle\Entity\Board\Task", mappedBy="projectIteration")
     * */
    protected $tasks;

    /**
     * @OneToMany(targetEntity="\Flower\ModelBundle\Entity\Board\TaskFilter", mappedBy="projectIteration")
     */
    protected $taskFilters;


    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->taskFilters = new ArrayCollection();
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
     * Set project
     *
     * @param \Flower\ModelBundle\Entity\Project\Project $project
     * @return ProjectIteration
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
     * Add tasks
     *
     * @param \Flower\ModelBundle\Entity\Board\Task $tasks
     */
    public function addTask(\Flower\ModelBundle\Entity\Board\Task $tasks)
    {
        $this->tasks[] = $tasks;
    }

    /**
     * Remove tasks
     *
     * @param \Flower\ModelBundle\Entity\Board\Task $tasks
     */
    public function removeTask(\Flower\ModelBundle\Entity\Board\Task $tasks)
    {
        $this->tasks->removeElement($tasks);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * @param mixed $dueDate
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getTaskFilters()
    {
        return $this->taskFilters;
    }

    /**
     * @param mixed $taskFilters
     */
    public function setTaskFilters($taskFilters)
    {
        $this->taskFilters = $taskFilters;
    }


}
