<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Token
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
     *
     * @Groups({"token"})
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $expirationTime;

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
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token)
    {
        $this->token= $token;
    }

    /**
     * Auto generate a token
     */
    public function generateToken(): void
    {
        $this->setToken(md5(random_bytes(10)));
    }

    /**
     * @return \DateTime
     */
    public function getExpirationTime(): ?\DateTime
    {
        return $this->expirationTime;
    }

    /**
     * @param \DateTime $expirationTime
     */
    public function setExpirationTime(\DateTime $expirationTime)
    {
        $this->expirationTime = $expirationTime;
    }
}