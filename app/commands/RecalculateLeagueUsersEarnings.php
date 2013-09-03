<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RecalculateLeagueUsersEarnings extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'recalculate:league';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Recalculate player earnings for a league.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire() {
		$league = League::with(array(
			'players' => function ($query) {
				$users = $this->option('users');
				if(!empty($users)) {
					$query->whereIn('user_id', $users);
				}
			}
		))->findOrFail($this->argument('league'));

		$this->info('Pushing into queue '.count($league->players).' users');
		foreach ($league->players as $user) {
			Queue::push("UpdateUserEarnings", array(
				"user_id" => $user->id, "league_id" => $league->id, "since" => time()
			));
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('league', InputArgument::REQUIRED, 'League ID'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('users', 'U', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'User to update', array()),
		);
	}

}