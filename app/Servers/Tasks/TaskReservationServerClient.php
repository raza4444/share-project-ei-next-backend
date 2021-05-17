<?php
/**
 * by stephan scheide
 */

namespace App\Servers\Tasks;


use App\Servers\EventServerConnectionException;

class TaskReservationServerClient
{

    /**
     * returns the next id for given filter
     * if none is found, 0 is returned
     *
     * @param TaskFilter $filter
     * @return float|int
     * @throws EventServerConnectionException
     */
    public function nextIdFor(TaskFilter $filter)
    {
        $socket = $this->connectToServer();

        $in = "consume-" . $filter->counterTaskId . '-' . $filter->userId . "\n";

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

    public function countOpen(TaskFilter $filter)
    {
        $socket = $this->connectToServer();

        $in = "count-" . $filter->counterTaskId . '-' . $filter->userId . "\n";

        socket_write($socket, $in, strlen($in));
        $out = socket_read($socket, 2048);
        socket_close($socket);

        if ($out === false) {
            throw new \Exception('Konnte keine Daten vom Ereignisserver holen');
        } else {
            if (strpos($out, 'ok-') === 0) {
                $tmp = explode('-', $out);
                $count = $tmp[1] * 1;
                return $count;
            } else {
                throw new \Exception("Unbekannte Ausgabe <$out>");
            }
        }
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
            return false;
        }
    }

    private function connectToServer()
    {
        $address = '127.0.0.1';
        $port = 10001;

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
