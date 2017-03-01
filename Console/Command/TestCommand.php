<?php
namespace Macosxvn\ImageCDN\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TestCommand
 */
class TestCommand extends Command {
    /**
     * Folder name argument
     */
    const FOLDERNAME_ARGUMENT = 'folder-name';

    const CREATE_NOT_EXIST = 'create-not-exist';

    const DEFAULT_FOLDERNAME = 'image_cdn';

    const CLIENT_SECRET_PATH = __DIR__ . "/../../etc/client_secret.json";

    const APPLICATION_NAME = 'Googole Driver as Image CDN';

    const CREDENTIALS_PATH = '/.credentials/google_driver_api.json';

    const SCOPES = [\Google_Service_Drive::DRIVE];


    /**
     * @var OutputInterface
     */
    protected $_output = null;

    /**
     * @var bool
     */
    protected $_createNotExist = false;

    /**
     * {@inheritdoc}
     */
    protected function configure() {
        $this->setName('macosxvn:imageCDN')
            ->setDescription('Test Google Driver API')
            ->setDefinition([
                new InputArgument(
                    self::FOLDERNAME_ARGUMENT,
                    InputArgument::OPTIONAL,
                    'image_cdn'
                ),
                new InputOption(
                    self::CREATE_NOT_EXIST,
                    '-c',
                    InputOption::VALUE_NONE,
                    'Create folder if not exist'
                ),
            ]);
        parent::configure();
    }

    protected function initialize(InputInterface $input, OutputInterface $output) {
        $this->_output = $output;
        return $this;
    }

    protected function _getServiceClient() {
        $client = new \Google_Client();
        $client->setApplicationName(self::APPLICATION_NAME);
        $client->setScopes(self::SCOPES);
        $client->setAuthConfig(self::CLIENT_SECRET_PATH);
        $client->setAccessType('offline');

        // Load previously authorized credentials from a file.
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /* @var \Magento\Framework\App\Filesystem\DirectoryList $dir */
        $dir = $objectManager->get('Magento\Framework\App\Filesystem\DirectoryList');
        $credentialsPath = $dir->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR) . self::CREDENTIALS_PATH;
        if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            $this->_output->writeln(sprintf("Open the following link in your browser:\n%s", $authUrl));
            $this->_output->write('Enter verification code: ');
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

            // Store the credentials to disk.
            if(!file_exists(dirname($credentialsPath))) {
                mkdir(dirname($credentialsPath), 0700, true);
            }
            file_put_contents($credentialsPath, json_encode($accessToken));
            printf("Credentials saved to %s\n", $credentialsPath);
        }
        $client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
        }
        return $client;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $client = $this->_getServiceClient();
        $service = new \Google_Service_Drive($client);

        // get folder id
        $optParams = array(
            'q'    => "name='image_cdn' and mimeType = 'application/vnd.google-apps.folder'",
            'pageSize' => 1,
            'pageToken' => null,
            'fields' => 'nextPageToken, files(id, name)'
        );
        $results = $service->files->listFiles($optParams);

        if (count($results->getFiles()) == 0) {
            $output->writeln("No files found.");
        } else {
            $output->writeln("Files:");
            $folderId = "";
            foreach ($results->getFiles() as $file) {
                $folderId = $file->getId();
            }

            // get folder id
            $pageToken = null;
            do {
                $optParams = array(
                    'q'    => "'{$folderId}' in parents",
                    'pageSize' => 10,
                    'pageToken' => $pageToken,
                    'fields' => 'nextPageToken, files(id, name)'
                );
                $response = $service->files->listFiles($optParams);
                foreach ($response->getFiles() as $file) {
                    $output->writeln(sprintf("%s (%s)", $file->getName(), $file->getId()));
                }
            } while($pageToken != null);
        }
    }
}