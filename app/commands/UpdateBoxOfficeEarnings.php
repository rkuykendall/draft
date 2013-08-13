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
		// Get movies that are used in an active league and already released
		$movies = DB::table('movies')->where('movies.release', '<', new DateTime())
		            ->join('league_movie', 'movies.id', '=', 'league_movie.movie_id')
		            ->join('leagues', 'league_movie.league_id', '=', 'leagues.id')
		            ->where('leagues.end_date', '>', new DateTime()) // Inner join filters out all non-valid movies
		            ->groupBy('movies.id')
		            ->get(array('movies.id', DB::raw('COUNT(leagues.id) as UsedCount')));

		// Queue up
		$delay = 0;
		foreach ($movies as $movie) {
			$delay += rand(1, 5);
			Queue::later($delay, "UpdateEarnings", array("movie_id" => $movie->id));
		}

		// Update last update date
		Queue::later($delay, function($job) {
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
