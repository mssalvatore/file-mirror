<?php

require_once(__DIR__ . "/../vendor/autoload.php");

use \mssalvatore\FileMirror\Actions\AbstractActionFactory;
use \mssalvatore\FileMirror\Actions\RsyncActionFactory;
use \mssalvatore\FileMirror\Configuration\ConfigLoaderInterface;
use \mssalvatore\FileMirror\Configuration\FileConfigLoaderFactory;
use \mssalvatore\FileMirror\Monitor\AbstractFileMirror;
use \mssalvatore\FileMirror\Monitor\AbstractMonitorFactory;
use \mssalvatore\FileMirror\Monitor\AbstractWorkerFactory;
use \mssalvatore\FileMirror\Monitor\BasicFileMirrorFactory;
use \mssalvatore\FileMirror\Monitor\MonitorInterface;
use \mssalvatore\FileMirror\Monitor\INotifyMonitorFactory;
use \mssalvatore\FileMirror\Monitor\RegistrationRecord;
use \mssalvatore\FileMirror\Monitor\ShutdownSignalHandler;
use \mssalvatore\FileMirror\Monitor\WorkerFactory;

function buildFileConfigLoaderFactory($configFilePath)
{
	$fileConfigLoaderFactory = new FileConfigLoaderFactory();
	$fileConfigLoaderFactory->setConfigFilePath($configFilePath);

	return $fileConfigLoaderFactory;
}

function buildWorkerFactory(\stdClass $config, AbstractMonitorFactory $monitorFactory, AbstractActionFactory $actionFactory)
{
	$workerFactory = new WorkerFactory($config);
	$workerFactory->injectMonitorFactory($monitorFactory); $workerFactory->injectActionFactory($actionFactory);

	return $workerFactory;
}

function buildBasicFileMirrorFactory(\stdClass $config, AbstractWorkerFactory $workerFactory, MonitorInterface $configMonitor, ConfigLoaderInterface $configLoader)
{
	$basicFileMirrorFactory = new BasicFileMirrorFactory($config);
	$basicFileMirrorFactory->injectWorkerFactory($workerFactory);
	$basicFileMirrorFactory->injectConfigMonitor($configMonitor);
	$basicFileMirrorFactory->injectConfigLoader($configLoader);
	$basicFileMirrorFactory->injectShutdownSignalHandler(new ShutdownSignalHandler());

	return $basicFileMirrorFactory;
}

try {
	print posix_getpid() . "\n";

	$configFilePath = __DIR__ . '/../config/test.json';
	$fileConfigLoaderFactory = buildFileConfigLoaderFactory($configFilePath);

	$configLoader = $fileConfigLoaderFactory->buildConfigLoader();
	$config = $configLoader->loadConfig();

	$monitorFactory = new INotifyMonitorFactory($config);
	$actionFactory = new RsyncActionFactory($config);

	$workerFactory = buildWorkerFactory($config, $monitorFactory, $actionFactory);

	$configMonitor = $monitorFactory->buildMonitor(new RegistrationRecord($configFilePath));
	$mirrorFactory = buildBasicFileMirrorFactory($config, $workerFactory, $configMonitor, $configLoader);
	$mirror = $mirrorFactory->buildFileMirror();

	$mirror->run();
} catch (\Exception $ex) {
	print "Caught Exception: " . $ex->getMessage() . "\n";
}
exit;
