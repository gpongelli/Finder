<?php

class IndexMethod extends JObject
{
	private $_log = null;
	private $_time = null;
	private $_qtime = null;

	public function run($limit)
	{
		// initialize the time value
		$this->_time = microtime(true);

		// import library dependencies
		require_once(JPATH_ADMINISTRATOR.'/components/com_finder/helpers/indexer/indexer.php');
		jimport('joomla.html.parameter');
		jimport('joomla.application.component.helper');

		// fool the system into thinking we are running as JSite with Finder as the active component
		JFactory::getApplication('site');
		$_SERVER['HTTP_HOST'] = 'domain.com';
		define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR.'/components/com_finder');

		// Disable caching.
		$config = &JFactory::getConfig();
		$config->setValue('caching', 0);
		$config->setValue('cache_handler', 'file');

		// Reset the indexer state.
		FinderIndexer::resetState();

		// Import the finder plugins.
		JPluginHelper::importPlugin('finder');

		// Starting Indexer.
		$this->_log("Starting Indexer.\n", true);

		// Trigger the onStartIndex event.
		JDispatcher::getInstance()->trigger('onStartIndex');

		// Remove the script time limit.
		@set_time_limit(0);

		// Get the indexer state.
		$state = FinderIndexer::getState();

		// Setting up plugins.
		$this->_log("Setting up Finder plugins.\n", true);

		// Trigger the onBeforeIndex event.
		JDispatcher::getInstance()->trigger('onBeforeIndex');

		// Startup reporting.
		$this->_log("Setup {$state->totalItems} items in ".round(microtime(true) - $this->_time, 3)." seconds.\n\n", true);

		// Get the number of batches.
		$t = (int)$state->totalItems;
		$c = (int)ceil($t / $state->batchSize);
		$c = $c === 0 ? 1 : $c;

		// Process the batches.
		for ($i = 0; $i < $c; $i++)
		{
			// Set the batch start time.
			$this->_qtime = microtime(true);

			// Reset the batch offset.
			$state->batchOffset = 0;

			// Trigger the onBuildIndex event.
			JDispatcher::getInstance()->trigger('onBuildIndex');

			// Batch reporting.
			$this->_log(" * Processed batch ".($i+1)." in ".round(microtime(true) - $this->_qtime, 3)." seconds.\n", true);
		}

		// Total reporting.
		$this->_log("\nTotal Processing Time: ".round(microtime(true) - $this->_time, 3)." seconds.\n", true);

		// Reset the indexer state.
		FinderIndexer::resetState();
	}

	private function _log($message, $echo=false)
	{
		if ($echo) {
			echo $message;
		}
		$this->_log += $message;
	}
}