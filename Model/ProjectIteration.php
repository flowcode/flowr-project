<?php

namespace Flower\ProjectBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use JMS\Serializer\Annotation\Groups;

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
     * @Groups({"search", "public_api"})
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
     * @Groups({"search", "public_api"})
     */
    protected $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startDate", type="datetime", nullable=true))
     * @Groups({"search", "public_api"})
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dueDate", type="datetime", nullable=true))
     * @Groups({"search", "public_api"})
     */
    protected $dueDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     * @Groups({"search", "public_api"})
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

    /**
     * @var boolean
     *
     * @ORM\Column(name="client_viewable", type="boolean")
     */
    protected $clientViewable;


    public function __construct()
    {
        $this->clientViewable = false;
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

    /**
     * @return boolean
     */
    public function isClientViewable()
    {
        return $this->clientViewable;
    }

    /**
     * @param boolean $clientViewable
     */
    public function setClientViewable($clientViewable)
    {
        $this->clientViewable = $clientViewable;
    }




}
