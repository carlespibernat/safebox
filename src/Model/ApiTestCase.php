<?php

namespace App\Model;

use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\DependencyInjection\ContainerInterface;


class ApiTestCase extends WebTestCase
{
    const DEFAULT_API_BASE_URI = 'http://host.docker.internal';

    /** @var  string */
    private $apiBaseUri;

    /** @var  Application $application */
    protected static $application;

    /** @var  EntityManager $entityManager */
    protected static $entityManager;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->configureBaseUri();

        ini_set('memory_limit', '256M');
    }

    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::runCommand('doctrine:database:drop --force');
        self::runCommand('doctrine:database:create');
        self::runCommand('doctrine:schema:create');

        $client = static::createClient();
        self::$entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * {@inheritDoc}
     */
    public static function tearDownAfterClass()
    {
        self::runCommand('doctrine:database:drop --force');

        parent::tearDownAfterClass();

        self::$entityManager->close();
        self::$entityManager = null;
    }

    /**
     * Runs a command
     *
     * @param string $command
     *
     * @return int
     */
    protected static function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    /**
     * Get application
     *
     * @return Application
     */
    protected static function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient();

            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

    /**
     * Performs a request to an api endpoint
     *
     * @param string $endpoint
     * @param string $method
     * @param string $body
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function request(string $endpoint, string $method = 'GET', string $body = '')
    {
        $client = new Client([
            'base_uri' => $this->apiBaseUri,
            'http_errors' => false
        ]);

        return $client->request($method, $endpoint, [
            'body' => $body,
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    /**
     * Configures api base uri
     */
    protected function configureBaseUri()
    {
        $this->apiBaseUri = self::DEFAULT_API_BASE_URI;
    }
}