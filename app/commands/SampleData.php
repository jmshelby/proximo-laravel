<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Badcow\LoremIpsum\Generator;
use GuzzleHttp\Client as GuzzleClient;


class SampleData extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'populate:cathy';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public function getBaseURL()
	{
		$host = $this->option('host');
		return "http://$host/";
	}

	public function getRandomLocation($start)
	{
		return $start . "." . mt_rand();
	}

	public function postRandomMessage()
	{
		$generator = new Generator;
		$generator->setParagraphMean(5);
		$randomMessage = $generator->getSentences(1);
		$randomMessage = reset($randomMessage);

		$lat = $this->getRandomLocation(40);
		$long = $this->getRandomLocation(126);

		$username = 'chatty_cathy';

		$this->postMessage($username, $lat, $long, $randomMessage);
	}

	public function postMessage($user, $lat, $long, $message)
	{
		$client = new GuzzleClient(['base_url' => $this->getBaseURL()]);
		$this->comment("Going to post message....");
		PHP_Timer::start();
		$response = $client->post('webservice/post-message',[
			'query' => [
				'username' => $user,
				'latitude' => $lat,
				'longitude' => $long,
				'content' => $message,
			],
		]);
		$timer = PHP_Timer::stop();
		$time = PHP_Timer::secondsToTimeString($timer);
		$this->info("Going to post message....Done: $time");

		$json = $response->json();
		$info = "{$json['status']}: {$json['response']['location']['latitude']}, {$json['response']['location']['longitude']} - \"{$json['response']['content']}\"";

		$this->comment("Returned: $info");
	}


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$hits = $this->option('hits');

		for($i=0; $i<$hits; $i++) {
			$this->postRandomMessage();
		}

		$this->info("Done posting messages");
		$this->comment("(Posted $hits total)");
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			//array('example', InputArgument::REQUIRED, 'An example argument.'),
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
			//array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
			array('hits', null, InputOption::VALUE_OPTIONAL, 'Amount of messages to post', '5'),
			array('host', null, InputOption::VALUE_OPTIONAL, 'Hostname of webservice to use', 'dubdub.jakegub.com'),
		);
	}

}
