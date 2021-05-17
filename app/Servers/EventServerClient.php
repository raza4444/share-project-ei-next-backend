<?php
/**
 * by stephan scheide
 */

namespace App\Servers;

/**
 * default connector to event service
 *
 * Class EventServerClient
 * @package Modules\Admin\Campaign\Servers
 */
class EventServerClient implements IEventServerClient
{

    public function nextFreeEventId($counterType)
    {

        $socket = $this->connectToServer();

        $in = "consume-$counterType\n";

        socket_write($socket, $in, strlen($in));
        $out = socket_read($socket, 2048);
        socket_close($socket);

        if ($out === false) {
            throw new \Exception('Konnte keine Daten vom Ereignisserver holen');
        } else {
            if (strpos($out, 'ok-') === 0) {
                $tmp = explode('-', $out);
                $id = $tmp[1] * 1;
                return $id;
            } else {
                throw new \Exception("Unbekannte Ausgabe <$out>");
            }
        }
    }

    public function nextFreeEventIdWithUserAssociation($counterType, $userId)
    {
        $socket = $this->connectToServer();

        $in = "consume2-$counterType-$userId\n";

        socket_write($socket, $in, strlen($in));
        $out = socket_read($socket, 2048);
        socket_close($socket);

        if ($out === false) {
            throw new \Exception('Konnte keine Daten vom Ereignisserver holen');
        } else {
            if (strpos($out, 'ok-') === 0) {
                $tmp = explode('-', $out);
                $id = $tmp[1] * 1;
                return $id;
            } else {
                throw new \Exception("Unbekannte Ausgabe <$out>");
            }
        }
    }

    public function forceReloadOfEvents()
    {
        $socket = $this->connectToServer();
        $in = "reload\n";
        socket_write($socket, $in, strlen($in));
        $out = socket_read($socket, 2048);
        socket_close($socket);
    }

    public function isServerAvailable()
    {
        try {
            $socket = $this->connectToServer();
            $in = "quit\n";
            socket_write($socket, $in, strlen($in));
            $out = socket_read($socket, 2048);
            socket_close($socket);
            return true;
        } catch (\Exception $e1) {
            // TogetherLog::log('event-locking-server-error', 'error connecting server: ' . $e1->getMessage());
            return false;
        }
    }

    public function markEventAsUseAbleAgain($eventId)
    {
        $in = 'reuse-' . $eventId . "\n";
        $this->connectAndWriteAndForget($in);
    }

    private function connectAndWriteAndForget($in)
    {
        $socket = $this->connectToServer();
        socket_write($socket, $in, strlen($in));
        $out = socket_read($socket, 2048);
        socket_close($socket);
    }


    private function connectToServer()
    {
        $address = '127.0.0.1';
        $port = 10000;

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            throw new EventServerConnectionException('Kann keinen Socket fuer den Event-Server-Client erstellen');
        }

        $result = socket_connect($socket, $address, $port);
        if ($result === false) {
            throw new EventServerConnectionException('Sperrserver laeuft nicht [mehr]. Abbruch');
        }

        return $socket;
    }

}