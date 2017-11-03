<?php

namespace mssalvatore\FileMirror\Actions;

class RsyncSshConfigTranslator
{
    public function translateSshConfiguration(\stdClass $server)
    {
        $ssh = array('host' => $server->host);
        $ssh['public_key'] = $server->publicKey;

        $ssh['username'] = $server->user;
        $ssh['port'] = $this->getPort($server);

        return $ssh;
    }

    protected function getPort(\stdClass $server)
    {
        if (property_exists($server, 'port')) {
            return $server->port;
        } else {
            return  22;
        }
    }
}
