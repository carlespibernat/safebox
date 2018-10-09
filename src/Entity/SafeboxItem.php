<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class SafeboxItem
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $contentEncrypted;

    /**
     * @return mixed
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContentEncrypted(): string
    {
        return $this->contentEncrypted;
    }

    /**
     * @param string $contentEncrypted
     */
    public function setContentEncrypted(string $contentEncrypted)
    {
        $this->contentEncrypted = $contentEncrypted;
    }


}