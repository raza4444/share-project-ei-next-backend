<?php
/**
 * by stephan scheide
 */

namespace App\Servers;

abstract class SingleServer
{

    public $port = 10000;

    public $address = '127.0.0.1';

    /**
     * @var resource
     */
    protected $sock;

    public function run()
    {
        if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
            throw new \Exception("socket_create() fehlgeschlagen: Grund: " . socket_strerror(socket_last_error()) . "\n");
        }

        if (socket_bind($sock, $this->address, $this->port) === false) {
            throw new \Exception("socket_bind() fehlgeschlagen: Grund: " . socket_strerror(socket_last_error($sock)) . "\n");
        }

        if (socket_listen($sock, 5) === false) {
            throw new \Exception("socket_listen() fehlgeschlagen: Grund: " . socket_strerror(socket_last_error($sock)) . "\n");
        }

        $this->sock = $sock;
        $this->accept();
        socket_close($sock);
    }

    public abstract function accept();

    protected function debug($any)
    {
        echo date('Y-m-d H:i:s') . ' ' . $any . "\n";
    }

}
