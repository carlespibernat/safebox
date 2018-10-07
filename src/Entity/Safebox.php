<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use GuzzleHttp\Client;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

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
     *
     * @Groups({"safebox"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    private $plainPassword;

    /**
     * @var Token
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Token", cascade={"persist"})
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
     *
     * Check if a valid plain password has been set
     *
     * @return bool
     */
    public function hasValidPlainPassword(): bool
    {
        // Check has at least 6 characters
        if (strlen($this->getPlainPassword()) < 6) {
            return false;
        }

        // Check is one of the top used passwords
        $client = new Client();
        $response = $client->get('https://raw.githubusercontent.com/mozilla/fxa-password-strength-checker/master/source_data/10_million_password_list_top_10000.txt');
        $fileContent = $response->getBody()->getContents();
        $words = explode("\n", $fileContent);
        if (in_array($this->getPlainPassword(), $words)) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
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
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param SafeboxItem $safeboxItem
     */
    public function addItem(SafeboxItem $safeboxItem): void
    {
        $this->items[] = $safeboxItem;
    }

    /**
     * @param SafeboxItem[] $items
     */
    public function setItems(array $items): void
    {
        $this->items = [];

        foreach ($items as $item) {
            $this->addItem($item);
        }
    }
}