<?php
/**
 * by stephan scheide
 */

namespace App\Logging;


use Illuminate\Console\Command;

class CommandLogFacade implements LogFacade
{

    /**
     * @var Command
     */
    private $command;


    public static function createNew(Command $command)
    {
        $f = new CommandLogFacade();
        $f->command = $command;
        return $f;
    }

    public function info($message)
    {
        $this->command->info($message);
    }

    public function error($message)
    {
        $this->command->error($message);
    }

    public function exception(\Throwable $ex)
    {
        $this->command->error(LoggingClip::createExceptionOutput(0, $ex));
    }
}
