<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Token
{
    const MAX_FAILED_ATTEMPTS = 3;

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
     */
    private $expirationTime;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $expirationTimeEncrypted;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $failedAttempts = 0;

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

    /**
     * @return string
     */
    public function getExpirationTimeEncrypted(): ?string
    {
        return $this->expirationTimeEncrypted;
    }

    /**
     * @param string $expirationTimeEncrypted
     */
    public function setExpirationTimeEncrypted(string $expirationTimeEncrypted): void
    {
        $this->expirationTimeEncrypted = $expirationTimeEncrypted;
    }

    /**
     * Check if token is valid
     *
     * @param string $token
     *
     * @return bool
     */
    public function isValid(string $token): bool
    {
        if (
            !$this->getExpirationTime() ||
            $this->getExpirationTime() < new \DateTime() ||
            $token != $this->getToken()
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return int
     */
    public function getFailedAttempts(): int
    {
        return $this->failedAttempts;
    }

    /**
     * Increases failed attempts
     */
    public function increaseFailedAttempts(): void
    {
        $this->failedAttempts++;
    }

    /**
     * @param int $failedAttempts
     */
    public function setFailedAttempts(int $failedAttempts): void
    {
        $this->failedAttempts = $failedAttempts;
    }
}