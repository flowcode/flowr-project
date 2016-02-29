<?php

namespace Flower\ProjectBundle\Model;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\OneToMany;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\Groups;

/**
 * Project
 *
 */
class Project
{

    const type_fixed = 'fixed';
    const type_ongoing = 'ongoing';

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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"search", "public_api"})
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     * @Groups({"search", "public_api"})
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Groups({"search", "public_api"})
     */
    protected $description;

    /**
     * @var float
     *
     * @ORM\Column(name="estimated_hours", type="float", nullable=true)
     * @Groups({"public_api"})
     */
    protected $estimated;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Clients\Account")
     * @JoinColumn(name="account_id", referencedColumnName="id")
     * @Groups({"public_api"})
     * */
    protected $account;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Project\ProjectStatus")
     * @JoinColumn(name="status_id", referencedColumnName="id")
     * @Groups({"public_api"})
     * */
    protected $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="dailyWorkingHours", type="integer", nullable=true)
     */
    protected $dailyWorkingHours;

    /**
     * @OneToMany(targetEntity="\Flower\ModelBundle\Entity\Project\DocPage", mappedBy="project")
     * @Groups({"public_api"})
     * */
    protected $docPages;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\User\User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     * */
    protected $assignee;

    /**
     * @OneToMany(targetEntity="\Flower\ModelBundle\Entity\Project\ProjectMembership", mappedBy="project")
     * @Groups({"public_api"})
     * */
    protected $members;

    /**
     * @ManyToMany(targetEntity="\Flower\ModelBundle\Entity\Board\Board")
     * @JoinTable(name="project_boards",
     *      joinColumns={@JoinColumn(name="project_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="board_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    protected $boards;

    /**
     * @OneToMany(targetEntity="\Flower\ModelBundle\Entity\Planner\Event", mappedBy="project")
     */
    private $events;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    protected $updated;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="finished", type="datetime", nullable=true)
     * @Groups({"search"})
     */
    protected $finished;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     * @Groups({"search"})
     */
    protected $enabled;

    function __construct()
    {
        $this->enabled = true;
        $this->docPages = new ArrayCollection();
        $this->members = new ArrayCollection();
        $this->boards = new ArrayCollection();
        $this->events = new ArrayCollection();
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
     * @return Project
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
     * @return Project
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
     * Set created
     *
     * @param DateTime $created
     * @return Project
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param DateTime $updated
     * @return Project
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set finished
     *
     * @param DateTime $finished
     * @return Project
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;

        return $this;
    }

    /**
     * Get finished
     *
     * @return DateTime
     */
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     * Set account
     *
     * @param \Flower\ModelBundle\Entity\Clients\Account $account
     * @return Project
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
     * Set status
     *
     * @param \Flower\ModelBundle\Entity\Project\ProjectStatus $status
     * @return Project
     */
    public function setStatus(\Flower\ModelBundle\Entity\Project\ProjectStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \Flower\ModelBundle\Entity\Project\ProjectStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set estimated
     *
     * @param float $estimated
     * @return Project
     */
    public function setEstimated($estimated)
    {
        $this->estimated = $estimated;

        return $this;
    }

    /**
     * Get estimated
     *
     * @return float
     */
    public function getEstimated()
    {
        return $this->estimated;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Project
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }


    /**
     * Add docPages
     *
     * @param \Flower\ModelBundle\Entity\Project\DocPage $docPages
     * @return Project
     */
    public function addDocPage(\Flower\ModelBundle\Entity\Project\DocPage $docPages)
    {
        $this->docPages[] = $docPages;

        return $this;
    }

    /**
     * Remove docPages
     *
     * @param \Flower\ModelBundle\Entity\Project\DocPage $docPages
     */
    public function removeDocPage(\Flower\ModelBundle\Entity\Project\DocPage $docPages)
    {
        $this->docPages->removeElement($docPages);
    }

    /**
     * Get docPages
     *
     * @return Collection
     */
    public function getDocPages()
    {
        return $this->docPages;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Project
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add boards
     *
     * @param Board $boards
     * @return Project
     */
    public function addBoard(\Flower\ModelBundle\Entity\Board\Board $boards)
    {
        $this->boards[] = $boards;

        return $this;
    }

    /**
     * Remove boards
     *
     * @param \Flower\ModelBundle\Entity\Board\Board $boards
     */
    public function removeBoard(\Flower\ModelBundle\Entity\Board\Board $boards)
    {
        $this->boards->removeElement($boards);
    }

    /**
     * Get boards
     *
     * @return Collection
     */
    public function getBoards()
    {
        return $this->boards;
    }


    /**
     * Add projectMembership
     *
     * @param \Flower\ModelBundle\Entity\Project\ProjectMembership $projectMembership
     * @return Project
     */
    public function addProjectMembership(\Flower\ModelBundle\Entity\Project\ProjectMembership $projectMembership)
    {
        $this->members[] = $projectMembership;

        return $this;
    }

    /**
     * Remove docPages
     *
     * @param \Flower\ModelBundle\Entity\Project\ProjectMembership $projectMembership
     */
    public function removeProjectMembership(\Flower\ModelBundle\Entity\Project\ProjectMembership $projectMembership)
    {
        $this->members->removeElement($projectMembership);
    }

    /**
     * Get members
     *
     * @return Collection
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * Set assignee
     *
     * @param \Flower\ModelBundle\Entity\User\User $assignee
     * @return Project
     */
    public function setAssignee(\Flower\ModelBundle\Entity\User\User $assignee = null)
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * Get assignee
     *
     * @return \Flower\ModelBundle\Entity\User\User
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * Set dailyWorkingHours
     *
     * @param integer $dailyWorkingHours
     * @return Project
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
     * @return mixed
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param mixed $events
     */
    public function setEvents($events)
    {
        $this->events = $events;
    }



}
