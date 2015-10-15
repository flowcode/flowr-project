<?php

namespace Flower\ModelBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\Groups;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="Flower\ModelBundle\Repository\ProjectRepository")
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
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"search", "public_api"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     * @Groups({"search", "public_api"})
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Groups({"search", "public_api"})
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="estimated_hours", type="float", nullable=true)
     * @Groups({"public_api"})
     */
    private $estimated;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Clients\Account")
     * @JoinColumn(name="account_id", referencedColumnName="id")
     * @Groups({"public_api"})
     * */
    private $account;

    /**
     * @ManyToOne(targetEntity="ProjectStatus")
     * @JoinColumn(name="status_id", referencedColumnName="id")
     * @Groups({"public_api"})
     * */
    private $status;

    /**
     * @OneToMany(targetEntity="DocPage", mappedBy="project")
     * @Groups({"public_api"})
     * */
    private $docPages;

    /**
     * @OneToMany(targetEntity="Board", mappedBy="project")
     * */
    private $boards;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="finished", type="datetime", nullable=true)
     * @Groups({"search"})
     */
    private $finished;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     * @Groups({"search"})
     */
    private $enabled;

    function __construct()
    {
        $this->enabled = true;
        $this->docPages = new ArrayCollection();
        $this->boards = new ArrayCollection();
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
     * @param ProjectStatus $status
     * @return Project
     */
    public function setStatus(ProjectStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return ProjectStatus
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
     * @param DocPage $docPages
     * @return Project
     */
    public function addDocPage(DocPage $docPages)
    {
        $this->docPages[] = $docPages;

        return $this;
    }

    /**
     * Remove docPages
     *
     * @param DocPage $docPages
     */
    public function removeDocPage(DocPage $docPages)
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
    public function addBoard(Board $boards)
    {
        $this->boards[] = $boards;

        return $this;
    }

    /**
     * Remove boards
     *
     * @param Board $boards
     */
    public function removeBoard(Board $boards)
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
}
