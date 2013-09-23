<?php 

namespace Mimazoo\SoaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;


/**
 * One quick command for complete database update
 * 
 * @author mitja
 */
class DoctrineResetCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('doctrine:reset')
            ->setDescription('Refresh entities, drop and update databases, load fixtures')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$this->generateEntities($output);
		$this->shemaAction('drop', $output);
		$this->shemaAction('update', $output);
		$this->fixturesLoadAction($output);
    }
    
    protected function generateEntities(OutputInterface $output) {
    	
    	$command = $this->getApplication()->find('doctrine:generate:entities');
    	 
    	$arguments = array(
    			'command' => 'doctrine:generate:entities',
    			'name'    => 'Mimazoo',
    	);
    	 
    	$input = new ArrayInput($arguments);
    	$returnCode = $command->run($input, $output);
    }
    
    protected function shemaAction($action = 'drop', OutputInterface $output) {

    	$commandStr = 'doctrine:schema:' . $action;
    	
    	$command = $this->getApplication()->find($commandStr);
    
    	$arguments = array(
    			'command'    => $commandStr,
    			'--force'    => true,
    	);
    	
    
    	$input = new ArrayInput($arguments);
    	$returnCode = $command->run($input, $output);
    }
    
    protected function fixturesLoadAction(OutputInterface $output) {
    
    	$commandStr = 'doctrine:fixtures:load';
    	 
    	$command = $this->getApplication()->find($commandStr);
    
    	$arguments = array(
    			'command'    => $commandStr
    	);
    	
    	$input = new ArrayInput($arguments);
    	$returnCode = $command->run($input, $output);
    }
    
}