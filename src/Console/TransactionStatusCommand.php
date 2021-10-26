<?php

namespace Nabcellent\Kyanda\Console;

use Illuminate\Console\Command;
use Nabcellent\Kyanda\Repositories\Kyanda;

/**
 * Class StkStatus
 *
 * @package DrH\Mpesa\Commands
 */
class TransactionStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kyanda:query_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the status of all missing transactions.';
    /**
     * @var Kyanda
     */
    private Kyanda $kyanda;

    /**
     * Create a new command instance.
     *
     * @param Kyanda $repository
     */
    public function __construct(Kyanda $repository)
    {
        parent::__construct();
        $this->kyanda = $repository;
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $results = $this->kyanda->queryTransactionStatus();

        foreach ($results as $reference => $message) {
            $this->comment("$reference ---> $message");
        }
    }
}
