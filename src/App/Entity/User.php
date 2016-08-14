<?php
declare(strict_types=1);
/**
 * /src/App/Entity/User.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace App\Entity;

use App\Doctrine\Behaviours as ORMBehaviors;
use App\Entity\Interfaces\EntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 *
 * @AssertCollection\UniqueEntity("email")
 * @AssertCollection\UniqueEntity("username")
 *
 * @ORM\Table(
 *      name="user",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="uq_username", columns={"username"}),
 *          @ORM\UniqueConstraint(name="uq_email", columns={"email"})
 *      },
 *      indexes={
 *          @ORM\Index(name="createdBy_id", columns={"createdBy_id"}),
 *          @ORM\Index(name="updatedBy_id", columns={"updatedBy_id"}),
 *          @ORM\Index(name="deletedBy_id", columns={"deletedBy_id"})
 *      }
 *  )
 * @ORM\Entity(
 *      repositoryClass="App\Repository\User"
 *  )
 *
 * @package App\Entity
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class User implements EntityInterface, UserInterface, \Serializable
{
    // Traits
    use ORMBehaviors\Blameable;
    use ORMBehaviors\Timestampable;

    /**
     * User id.
     *
     * @var string
     *
     * @JMS\Groups({
     *      "Default",
     *      "User",
     *      "Users",
     *      "UserGroups",
     *      "User.id",
     *      "User.userGroups",
     *      "UserGroup.users",
     *      "UserLogin.user",
     *  })
     *
     * @ORM\Column(
     *      name="id",
     *      type="guid",
     *      nullable=false
     *  )
     * @ORM\Id()
     */
    private $id;

    /**
     * User's username.
     *
     * @var string
     *
     * @JMS\Groups({
     *      "Default",
     *      "User",
     *      "Users",
     *      "User.username",
     *  })
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(min = 2, max = 255)
     *
     * @ORM\Column(
     *      name="username",
     *      type="string",
     *      length=255,
     *      nullable=false
     *  )
     */
    private $username;

    /**
     * User's firstname.d
     *
     * @var string
     *
     * @JMS\Groups({
     *      "Default",
     *      "User",
     *      "Users",
     *      "User.firstname",
     *  })
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(min = 2, max = 255)
     *
     * @ORM\Column(
     *      name="firstname",
     *      type="string",
     *      length=255,
     *      nullable=false
     *  )
     */
    private $firstname;

    /**
     * User's surname.
     *
     * @var string
     *
     * @JMS\Groups({
     *      "Default",
     *      "User",
     *      "Users",
     *      "User.surname",
     *  })
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(min = 2, max = 255)
     *
     * @ORM\Column(
     *      name="surname",
     *      type="string",
     *      length=255,
     *      nullable=false
     *  )
     */
    private $surname;

    /**
     * User's email.
     *
     * @var string
     *
     * @JMS\Groups({
     *      "Default",
     *      "User",
     *      "Users",
     *      "User.email",
     *  })
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Email()
     *
     * @ORM\Column(
     *      name="email",
     *      type="string",
     *      length=255,
     *      nullable=false
     *  )
     */
    private $email;

    /**
     * User's password (encrypted).
     *
     * @var string
     *
     * @JMS\Exclude
     *
     * @ORM\Column(
     *      name="password",
     *      type="string",
     *      length=255,
     *      nullable=false
     *  )
     */
    private $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var  string
     *
     * @JMS\Exclude
     */
    private $plainPassword;

    /**
     * Collection of user's user groups.
     *
     * @var ArrayCollection
     *
     * @JMS\Groups({
     *      "UserGroups",
     *      "User.userGroups",
     *  })
     *
     * @ORM\ManyToMany(
     *     targetEntity="UserGroup",
     *     inversedBy="users"
     *  )
     */
    private $userGroups;

    /**
     * User logins
     *
     * @var \App\Entity\UserLogin[]
     *
     * @JMS\Groups({
     *      "UserLogins",
     *      "User.userLogins",
     *  })
     *
     * @ORM\OneToMany(
     *      targetEntity="App\Entity\UserLogin",
     *      mappedBy="user",
     *  )
     */
    private $userLogins;

    /**
     * User constructor.
     *
     * return User
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();

        $this->userGroups = new ArrayCollection();
        $this->userLogins = new ArrayCollection();
    }

    /**
     * Getter for id.
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * Getter for username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Getter for firstname.
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Getter for surname.
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Getter for email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Getter for password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Getter for plain password.
     *
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Getter for roles.
     *
     * @return string[]
     */
    public function getRoles() : array
    {
        /**
         * Lambda iterator to get user group role information.
         *
         * @param   UserGroup   $userGroup
         *
         * @return  string
         */
        $iterator = function ($userGroup) {
            return $userGroup->getRole();
        };

        return array_map($iterator, $this->userGroups->toArray());
    }

    /**
     * Getter for user groups collection.
     *
     * @return Collection|UserGroup[]
     */
    public function getUserGroups() : Collection
    {
        return $this->userGroups;
    }

    /**
     * Getter for user logins collection.
     *
     * @return Collection|UserLogin[]
     */
    public function getUserLogins() : Collection
    {
        return $this->userLogins;
    }

    /**
     * Setter for username.
     *
     * @param   string  $username
     *
     * @return  User
     */
    public function setUsername(string $username) : User
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Setter for firstname.
     *
     * @param   string  $firstname
     *
     * @return  User
     */
    public function setFirstname(string $firstname) : User
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Setter for surname.
     *
     * @param   string  $surname
     *
     * @return  User
     */
    public function setSurname(string $surname) : User
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Setter for email.
     *
     * @param   string  $email
     *
     * @return  User
     */
    public function setEmail(string $email) : User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Setter for password.
     *
     * @param   callable    $encoder
     * @param   string      $plainPassword
     *
     * @return  User
     */
    public function setPassword(callable $encoder, string $plainPassword) : User
    {
        $this->password = $encoder($plainPassword);

        return $this;
    }

    /**
     * Setter for plain password.
     *
     * @param   string  $plainPassword
     *
     * @return  User
     */
    public function setPlainPassword(string $plainPassword) : User
    {
        if (!empty($plainPassword)) {
            $this->plainPassword = $plainPassword;

            // Change some mapped values so preUpdate will get called.
            $this->password = ''; // just blank it out
        }

        return $this;
    }

    /**
     * String representation of object
     *
     * @link    http://php.net/manual/en/serializable.serialize.php
     *
     * @return  string  the string representation of the object or null
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
        ]);
    }

    /**
     * Constructs the object
     *
     * @link    http://php.net/manual/en/serializable.unserialize.php
     *
     * @param   string  $serialized The string representation of the object.
     *
     * @return  void
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
        ) = unserialize($serialized);
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Method to get login data for JWT token.
     *
     * @return array
     */
    public function getLoginData() : array
    {
        return [
            'firstname' => $this->getFirstname(),
            'surname'   => $this->getSurname(),
            'email'     => $this->getEmail(),
        ];
    }

    /**
     * Method to get user checksum string.
     *
     * @return string
     */
    public function getChecksum() : string
    {
        $bits = [
            $this->getId(),
            $this->getPassword(),
        ];

        return hash('sha512', implode('', $bits));
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        $this->plainPassword = '';
    }

    /**
     * Method to attach new user group to user.
     *
     * @param   UserGroup   $userGroup
     *
     * @return  User
     */
    public function addUserGroup(UserGroup $userGroup) : User
    {
        if (!$this->userGroups->contains($userGroup)) {
            $this->userGroups->add($userGroup);
            $userGroup->addUser($this);
        }

        return $this;
    }

    /**
     * Method to remove specified user group from user.
     *
     * @param   UserGroup   $userGroup
     *
     * @return  User
     */
    public function removeUserGroup(UserGroup $userGroup) : User
    {
        if ($this->userGroups->contains($userGroup)) {
            $this->userGroups->removeElement($userGroup);
            $userGroup->removeUser($this);
        }

        return $this;
    }

    /**
     * Method to remove all many-to-many user group relations from current user.
     *
     * @return  User
     */
    public function clearUserGroups() : User
    {
        $this->userGroups->clear();

        return $this;
    }
}
