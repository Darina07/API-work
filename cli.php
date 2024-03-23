<?php

namespace Erc\Cli;

use Erc\Api\Models\ImpersonatePermissions;
use Erc\Cli\Cli;
use Exception;
use hphio\cli\AvailableCommands;
use hphio\cli\ExitCommand;
use hphio\cli\ShowHelp;
use League\CLImate\CLImate;

include('bootstrap/bootstrap.php');
if(!function_exists('getConfigValues')) throw new Exception("config.php does not contain getConfigValues(). You must upgrade config.php to use this system.");

$config_values = getConfigValues();
$container = getContainer($config_values);
$container->add('argv', $argv);
$container->add(CLImate::class);
$container->add(AvailableCommands::class);
$container->add(ShowHelp::class)->addArgument($container);
$container->add(ExitCommand::class)->addArgument($container);
$container->add(DatabaseAbstractor::class)->addArgument($container);
$container->add(UserAdd::class)->addArgument($container);
$container->add(UserDelete::class)->addArgument($container);
$container->add(UserTokenNew::class)->addArgument($container);
$container->add(UserSearch::class)->addArgument($container);
$container->add(UserImpersonateDeputize::class)->addArgument($container);
$container->add(UserImpersonateRevoke::class)->addArgument($container);
$container->add(UserImpersonateShow::class)->addArgument($container);
$container->add(EntityTypeCreate::class)->addArgument($container);
$container->add(EntityTypeList::class)->addArgument($container);
$container->add(EntityTypeDelete::class)->addArgument($container);
$container->add(EntityCreate::class)->addArgument($container);
$container->add(EntityShow::class)->addArgument($container);
$container->add(EntityDelete::class)->addArgument($container);
$container->add(PodCreate::class)->addArgument($container);
$container->add(PodsShow::class)->addArgument($container);
$container->add(DatabaseBackupNow::class)->addArgument($container);
$container->add(DatabaseBackupsList::class)->addArgument($container);
$container->add(DatabaseBackupClean::class)->addArgument($container);
$container->add(DatabaseBackupRestore::class)->addArgument($container);
$container->add(DatabaseBackupPrune::class)->addArgument($container);
$container->add(ClientNew::class)->addArgument($container);
$container->add(ClientShow::class)->addArgument($container);
$container->add(ClientDelete::class)->addArgument($container);
$container->add(ConfigShow::class)->addArgument($container);
$container->add(UsersPasswordReset::class)->addArgument($container);
$container->add(PackageGenerate::class)->addArgument($container);
$container->add(MaintenanceModeOn::class)->addArgument($container);
$container->add(MaintenanceModeOff::class)->addArgument($container);

$cli = new Cli($container);
$cli->run();
