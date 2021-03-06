<?php
/**
 * Copyright (c) Qobo Ltd. (https://www.qobo.biz)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Qobo Ltd. (https://www.qobo.biz)
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Qobo\Robo\Task\Build;

use Robo\Result;

class Base extends \Qobo\Robo\AbstractCmdTask
{
    /**
     * @var array $tasks List of all possible build tasks
     */
    protected $tasks = [
        'all' => [],
        'phpunit' => [
            'cmd'   => './vendor/bin/phpunit',
            'path'  => ['./tests'],
            'batch' => true,
            'out'  => 'build/test-coverage',
            'logs'  => 'build/test-results'
        ],
        'phpcs' => [
            'cmd'   => './vendor/bin/phpcs',
            'path'  => ['./tests', './src'],
            'batch' => true
        ],
        'pdepend' => [
            'cmd'   =>'./vendor/bin/pdepend --jdepend-xml=%%LOGS%%/jdepend.xml --jdepend-chart=%%OUT%%/dependecies.svg --overview-pyramid=%%OUT%%/overview-pyramid.svg %%PATH%%',
            'path'  => ['./src'],
            'batch' => false,
            'out'  => 'build/pdepend',
            'logs'  => 'build/test-results'
        ],
        'phploc' => [
            'cmd'   =>  './vendor/bin/phploc --count-tests --log-csv %%LOGS%%/phploc.csv --log-xml %%LOGS%%/phploc.xml %%PATH%%',
            'path'  => ['./src', './tests'],
            'batch' => true,
            'logs'  => 'build/test-results'
        ],
        'phpmd' => [
            'cmd'   => './vendor/bin/phpmd %%PATH%% text codesize,controversial,naming,unusedcode',
            'path'  => ['./src'],
            'batch' => false
        ],
        'phpmd-ci' => [
            'cmd'   => './vendor/bin/phpmd %%PATH%% xml codesize,controversial,naming,unusedcode --reportfile %%LOGS%%/phpmd.xml',
            'path'  => ['./src'],
            'batch' => false,
            'logs'  => 'build/test-results'
        ],
        'phpcpd' => [
            'cmd'   => './vendor/bin/phpcpd --log-pmd=%%LOGS%%/phpcpd.xml %%PATH%%',
            'path'  => ['./src'],
            'batch' => false,
            'logs'  => 'build/test-results'
        ],
    ];

    /**
     * @var array $dirKeys list of keys in data to use in dir cleanup
     *
     * WARNING: make sure no be careful with this, as buildClean will
     * delete all the content inside of the dirs with this keys.
     */
    protected $dirKeys = [
        'out',
        'logs'
    ];

    /**
     * @var string $taskKey Key of the current command
     */
    protected $taskKey = "all";

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (!isset($this->tasks[$this->taskKey])) {
            return Result::error($this, "Command key is not invalid", $this->data);
        }

        $this->data = (array_filter($this->data))
            ? array_merge(
                $this->tasks[$this->taskKey],
                array_filter($this->data, function ($item) {
                    return ($item === null) ? false : true;
                })
            )
            : $this->tasks[$this->taskKey];

        return parent::run();
    }
}
