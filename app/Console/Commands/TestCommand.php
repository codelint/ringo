<?php

namespace Codelint\Ringo\App\Console\Commands;

use Codelint\Ringo\Laravel\Facade\Cache;
use Codelint\Ringo\Laravel\Facade\Ringo;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test {case=mail}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->addOption('uid', 'u', InputOption::VALUE_OPTIONAL, '', 'gzhang');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $case = $this->argument('case');
        Ringo::info('hello world');

        switch($case)
        {
            case 'mail':
                Ringo::mail('hello world');
                break;
            case 'corp':
                Ringo::weCorp('hello world');
                break;
            case 'chat':
                $uid = $this->option('uid');
                Ringo::weChat($uid, 'hello world', ['_summary' => 'wtf', 'title' => 'abc', 'abc' => 'bdc']);
                break;
            case 'cache':
                Cache::put('a', [
                    'b' => 1,
                    'arr' => [1, 2, 3],
                    'dict' => [
                        'a' => 1,
                        'b' => '123'
                    ]
                ]);
                $d = Cache::get('a');

                $this->info(json_encode($d));
        }

        return Command::SUCCESS;
    }
}
