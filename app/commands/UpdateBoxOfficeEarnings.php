<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateBoxOfficeEarnings extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'updatejob';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Run an update to pull the current BOEs from all default movies.';

	/**
	 * The datetime at the start of command
	 *
	 * @var string
	 */
	protected $timestamp;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$timestamp = new DateTime();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		// Get movies that are released
		// TODO: Check only for active leagues
		$movies = DB::table('movies')->where('release', '<', new DateTime())->get(array('id'));

		foreach ($movies as $movie) {
			Queue::push("UpdateEarnings", array("movie_id" => $movie->id));
		}

		Queue::push(function($job) {
			Cache::forever("last_update", Carbon::now());

			$job->delete();
		});
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(

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

		);
	}

}
