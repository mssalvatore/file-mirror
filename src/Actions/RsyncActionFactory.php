<?php

namespace mssalvatore\FileMirror\Actions;

use \AFM\Rsync\Rsync;

class RsyncActionFactory extends AbstractActionFactory
{
    protected $rsyncClients;
    protected $rsyncSshConfigTranslator;

    public function __construct(\stdClass $config)
    {
        parent::__construct($config);

        $this->rsyncSshConfigTranslator = new RsyncSshConfigTranslator();

        $this->buildRsyncClients();
    }

    public function buildAction($serverName, $source, $destination)
    {
        return new RsyncAction($this->rsyncClients[$serverName], $source, $destination);
    }

    protected function buildRsyncClients()
    {
        $this->rsyncClients = array();
        foreach ($this->config->servers as $server)
        {
            $ssh = $this->rsyncSshConfigTranslator->translateSshConfiguration($server);
            $rsyncConfig = array(
                'delete_from_target' => false, 
                'ssh' => $ssh,
                'archive' => true,
                'verbose' => true
            );
            $this->rsyncClients[$server->serverName] = new Rsync($rsyncConfig);
        }
    }
}
