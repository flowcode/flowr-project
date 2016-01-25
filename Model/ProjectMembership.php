<?php

namespace Flower\ProjectBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * ProjectMembership
 */
class ProjectMembership
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
     * @var integer
     *
     * @ORM\Column(name="member_role", type="string", length=255)
     */
    protected $memberRole;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Project\Project")
     * @JoinColumn(name="project_id", referencedColumnName="id")
     * */
    protected $project;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\User\User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     * */
    protected $user;


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
     * Set memberRole
     *
     * @param string $memberRole
     * @return \Flower\ModelBundle\Entity\Project\ProjectMembership
     */
    public function setMemberRole($memberRole)
    {
        $this->memberRole = $memberRole;

        return $this;
    }

    /**
     * Get memberRole
     *
     * @return string
     */
    public function getMemberRole()
    {
        return $this->memberRole;
    }


    /**
     * Set project
     *
     * @param \Flower\ModelBundle\Entity\Project\Project $project
     * @return \Flower\ModelBundle\Entity\Project\ProjectMembership
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
     * Set user
     *
     * @param \Flower\ModelBundle\Entity\User\User $user
     * @return \Flower\ModelBundle\Entity\Project\ProjectMembership
     */
    public function setUser(\Flower\ModelBundle\Entity\User\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Flower\ModelBundle\Entity\User\User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function __toString()
    {
        return $this->getName();
    }

}
