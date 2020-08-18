<?php

namespace App\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

final class CommandMakeNewContext extends Command
{
    protected static $defaultName = 'app:make-context';
    protected $count;
    private $params;
    private $log;
    public $exit;
    public $work;

    public function __construct(ParameterBagInterface $parameters, LoggerInterface $log)
    {
        $this->params       = $parameters;
        $this->logger       = $log;
        $this->work         = Uuid::uuid1();
        parent::__construct();
    }

    protected function configure()
    {
        $this
        ->addArgument('context', InputArgument::REQUIRED, 'Pls get in Context!!!')
        ->setDescription('This is a Bounded Context')
        ->setHelp('This command make a boilerplate to Bounded Context. You can also pass Context/Entity/nEntities');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $result1 =[
            ''
            ,'Context Creator'
            ,'=============='
            ,'This work starts with the ID: ' .$this->work->toString()
        ];
        $output->writeln($result1);
        
        $structure = explode('/',$input->getArgument('context'));
        
        $count = count($structure);
        try {
            
            $context            = $this->params->get('context_dir');
            $template           = $this->params->get('template_dir');
            $test_template      = $this->params->get('test_template_dir');
            $test               = $this->params->get('tests_dir');
            if ($count == 0) {
                throw 'An empty structure has been passed';
            }
        } catch (\Throwable $th) {
            
            $this->logger->error($th->getMessage(),['Work'=>$this->work->toString()]);
            $output->writeln([$th->getMessage()]);
        }
        
        $restricted = $context. '/.restricted';
        $filesystem = new Filesystem();
        try {
            if (!$filesystem->exists($context . '/' .$structure[0])) {
                try {
                    
                    $filesystem->mkdir($context . '/' .$structure[0]);
                    $output->writeln(['Main folder ' . $structure[0] . ' Created!']);
                    for ($i = 1; $i <=$count-1; $i++) {
                        $filesystem->mkdir($context . '/' . $structure[0] . '/' . $structure[$i]);
                        
                        $filesystem->mirror($template, $context . '/' . $structure[0] . '/' . $structure[$i]);
                        
                        $interfaceNew = $structure[$i].'RepositoryInterface.php';
                        $origin = $context . '/' . $structure[0] . '/' . $structure[$i] .'/Domain/Repository/NewRepositoryInterface.php';
                        $target = $context . '/' . $structure[0] . '/' . $structure[$i] .'/Domain/Repository/'.$interfaceNew;
                        $filesystem->rename($origin, $target);
                        
                        $appendContent = "namespace " . $this->params->get('namespaces') . "\\" . $structure[0] . "\\" . $structure[$i]."\\Domain\\Repository; \r\n";
                        $appendContent .= "interface ".$structure[$i]."RepositoryInterface \r\n";
                        $appendContent .= "{ \r\n";
                        $appendContent .= "} \r\n";
                        
                        try {
                            //$filesystem->chmod($interfaceNew, 0775);
                            $filesystem->appendToFile($target, $appendContent);
                        } catch (\Throwable $th) {
                            $this->logger->error($th->getMessage(),['Work'=>$this->work->toString()]);
                        }
                        
                        $output->writeln(['  -->'. $structure[$i] . ' Created!']);
                    }
                    
                } catch (\Throwable $th) {
                    $this->logger->error($th->getMessage(),['Work'=>$this->work->toString()]);
                }
                
            } else {
                
                if (!$filesystem->exists($restricted)) {
                    
                    for ($i = 1; $i <= $count -1; $i++) {
                        if (!$filesystem->exists($context . '/' . $structure[0] . '/' . $structure[$i])) {
                           
                            $filesystem->mkdir($context . '/' . $structure[0] . '/' . $structure[$i]);
                            $filesystem->mirror($template, $context . '/' . $structure[0] . '/' . $structure[$i]);
                            
                            $filesystem->mkdir($test . '/' . $structure[0] . '/' . $structure[$i]);
                            $filesystem->mirror($test_template, $test . '/' . $structure[0] . '/' . $structure[$i]);
                            
                            $interfaceNew = $structure[$i].'RepositoryInterface.php';
                            $origin = $context . '/' . $structure[0] . '/' . $structure[$i] .'/Domain/Repository/NewRepositoryInterface.php';
                            $target = $context . '/' . $structure[0] . '/' . $structure[$i] .'/Domain/Repository/'.$interfaceNew;
                            $filesystem->rename($origin, $target);
                            
                            $appendContent = "namespace " . $this->params->get('namespaces') . "\\" . $structure[0] . "\\" . $structure[$i]."\\Domain\\Repository; \r\n";
                            $appendContent .= "interface ".$structure[$i]."RepositoryInterface \r\n";
                            $appendContent .= "{ \r\n";
                            $appendContent .= "} \r\n";
                            
                            try {
                                //$filesystem->chmod($interfaceNew, 0775);
                                $filesystem->appendToFile($target, $appendContent);
                            } catch (\Throwable $th) {
                                $this->logger->error($th->getMessage(),['Work'=>$this->work->toString()]);
                            }
                            
                            $output->writeln(['  -->'. $structure[$i] . ' Created!']);
                       } 
                    }
                    $this->logger->info('Context '.$structure[0]. ' updated! And mimifiying in tests',['Work'=>$this->work->toString()]);
                    $output->writeln(['Context '.$structure[0]. ' updated! And mimifiying in tests','Work id: '. $this->work->toString()]);
                } else {
                    $output->writeln(['The directory '. $structure[0] .' is restricted']);
                }
            }
            
            if (!$filesystem->exists($test . '/' .$structure[0])) {
                try {
                    
                    $filesystem->mkdir($test . '/' .$structure[0]);
                    $output->writeln(['Main Test folder ' . $structure[0] . ' Created!']);
                    for ($i = 1; $i <=$count-1; $i++) {
                        $filesystem->mkdir($test . '/' . $structure[0] . '/' . $structure[$i]);
                        $filesystem->mirror($test_template, $test . '/' . $structure[0] . '/' . $structure[$i]);
                        $output->writeln(['  -->'. $structure[$i] . ' Created!']);
                    }
                    
                    $this->logger->info('Context '.$structure[0]. ' created! And mimifiying in tests',['Work'=>$this->work->toString()]);
                    $output->writeln(['Context '.$structure[0]. ' created! And mimifiying in tests','Work id: '. $this->work->toString()]);
                } catch (\Throwable $th) {
                    $this->logger->error($th->getMessage(),['Work'=>$this->work->toString()]);
                }
            }
            
        } catch (\Throwable $th) {
            $this->logger->error($th->getMessage(),['Work'=>$this->work->toString()]);
        }
        
        return Command::SUCCESS;
        
    }
}
