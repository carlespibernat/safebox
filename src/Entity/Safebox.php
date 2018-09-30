<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Safebox
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var Token
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Token")
     * @ORM\JoinColumn(name="token_id", referencedColumnName="id")
     */
    private $token;

    /**
     * @var SafeboxItem[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\SafeboxItem")
     * @ORM\JoinTable(name="safebox_safebox_item",
     *      joinColumns={@ORM\JoinColumn(name="safebox_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="safebox_item_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $items = [];

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * @return Token
     */
    public function getToken(): ?Token
    {
        return $this->token;
    }

    /**
     * @param Token $token
     */
    public function setToken(Token $token)
    {
        $this->token = $token;
    }

    /**
     * @return SafeboxItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param SafeboxItem[] $items
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }
}