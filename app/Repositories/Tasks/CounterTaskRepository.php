<?php
/**
 * by stephan scheide
 */

namespace App\Repositories\Tasks;


use App\Entities\Tasks\CounterTask;
use App\Repositories\AbstractRepository;

class CounterTaskRepository extends AbstractRepository
{

    public const TASK_KLICKER = 'klicker';

    public const TASK_ENTWURF = 'entwurf';

    public const TASK_DESIGNKONTROLLE = 'designkontroller';

    public const TASK_MEINEI = 'meinei';

    public static $defaultTaskAttributes = [
        self::TASK_KLICKER => ['name' => 'klicker', 'title' => 'Klicker'],
        self::TASK_ENTWURF => ['name' => 'entwurf', 'title' => 'Entwürfler'],
        self::TASK_DESIGNKONTROLLE => ['name' => 'designkontroller', 'title' => 'Designkontroller'],
        self::TASK_MEINEI => ['name' => 'meinei', 'title' => 'Mein Ei']
    ];

    public function __construct()
    {
        parent::__construct(CounterTask::class);
    }

    /**
     * @return CounterTask[]
     */
    public function findAndEnsureDefaults()
    {
        $keys = array_keys(self::$defaultTaskAttributes);
        $result = [];
        foreach ($keys as $key) {
            $result[] = $this->findAndEnsureByName($key);
        }
        return $result;
    }

    /**
     * @param $name
     * @return CounterTask[]
     */
    public function findAndEnsureByName($name)
    {
        return $this->query()
            ->where('name', '=', $name)
            ->firstOrCreate(self::$defaultTaskAttributes[$name]);
    }

}
