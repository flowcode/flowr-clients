<?php

namespace Flower\ClientsBundle\Command;

use Ddeboer\DataImport\Reader\CsvReader;
use Flower\ModelBundle\Entity\Clients\Contact;
use Flower\ModelBundle\Entity\Marketing\ImportProcess;
use SplFileObject;
use Flower\MarketingBundle\Model\ContactListStatus;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of newPHPClass
 *
 * @author Juan Manuel Agüero <jaguero@flowcode.com.ar>
 */
class ImporterCommand extends ContainerAwareCommand
{

    private $entityManager;
    private $counter;
    private $entityNameContact;

    protected function configure()
    {
        $this
            ->setName('flower:import:account')
            ->setDescription('flower import command.')
            ->addArgument(
                'importProcess', InputArgument::REQUIRED, 'Import process id.'
            );
    }

    /**
     * The method process an saved in a particular folder and import all the contacts to
     * the specified contact list.
     * @author Francisco Memoli <fmemoli@flowcode.com.ar>
     * @date   2015-06-02
     * @param  InputInterface $input Recive as parameters the contact list id and the file name to precess.
     * @param  OutputInterface $output print some Output
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get("logger")->info(date("Y-m-d H:i:s") . " - preparing to import.");

        /* arguments */
        $importProcess_id = $input->getArgument('importProcess');

        $this->entityManager = $this->getContainer()->get("doctrine.orm.entity_manager");

        $importProcess = $this->entityManager->getRepository('FlowerModelBundle:Marketing\ImportProcess')->find($importProcess_id);

        $filename = $importProcess->getFilename();


        $tempDir = $this->getContainer()->get("kernel")->getRootDir() . "/../web/uploads/tmp/" . $filename;

        $errors = array();
        $file = new SplFileObject($tempDir);
        $reader = new CsvReader($file);
        $reader->setStrict(false);
        $reader->setHeaderRowNumber(0);

        $this->entityNameContact = $this->entityManager->getClassMetadata('FlowerModelBundle:Clients\Account')->getName();

        $coldef = json_decode($importProcess->getColdef());

        /* TODO: Tiene que estar configurado */
        $flushStep = 20;

        $this->disableLogging();

        $rowsToImport = count($reader);
        $output->writeln("about to import " . $rowsToImport . " rows.");
        $this->counter = 0;

        $output->writeln("importing...");
        $this->getContainer()->get("logger")->info("importing");
        $totalCount = 0;
        $successCount = 0;
        $failCount = 0;

        $conn = $this->getEM()->getConnection();

        $qInsertAccount = 'INSERT INTO account(name, businessName, phone, address, cuit, created, updated) VALUES(?, "", ?, ?, ?, ?, ?)';
        $qValidAccount = 'SELECT count(*) FROM account a WHERE a.name = :name';

        $stmtAccount = $conn->prepare($qInsertAccount);
        $stmtValidAccount = $conn->prepare($qValidAccount);


        /* account */
        $accountId = null;


        foreach ($reader as $index => $row) {

            $this->counter++;

            $isUnique = true;
            $stmtValidAccount->bindParam(":name", $row['name']);
            $stmtValidAccount->execute();
            if ($stmtValidAccount->fetchColumn() > 0) {
                $isUnique = false;
            }

            if ($isUnique) {
                $mappedRow = $this->getMappedRow($coldef, $row);
                $stmtAccount->execute($mappedRow);

                $successCount++;
            }


            /* batch control */
            if (($this->counter % $flushStep) == 0) {
                $importProcess->setStatus(ImportProcess::STATUS_IN_PROGRESS);
                $importProcess->setSuccessCount($successCount);
                $importProcess->setFailCount($failCount);
                $importProcess->setTotalCount($totalCount + 1);
            }

            $totalCount++;
        }
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->getContainer()->get("logger")->info("ERROR import: " . $error["message"]);
            }
        }

        $importProcess->setStatus(ImportProcess::STATUS_READY);
        $importProcess->setSuccessCount($successCount);
        $importProcess->setFailCount($failCount);
        $importProcess->setTotalCount($totalCount);

        /* notifications */
        $notifService = $this->getContainer()->get("flower.core.service.notification");
        //$notifService->notificateContactListImportFinished($contactList);

        /* finishing import */
        $this->finish();

        $this->getContainer()->get("logger")->info(date("Y-m-d H:i:s") . " - import done.");
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManagerInterface em
     */
    protected function getEM()
    {
        return $this->entityManager;
    }

    /**
     *
     * @param type $coldef
     * @param type $rawrow
     * @return string
     */
    private function getMappedRow($coldef, $rawrow)
    {
        $mappedRow = array();
        foreach ($coldef as $key => $value) {
            if (isset($rawrow[$value])) {
                array_push($mappedRow, $rawrow[$value]);
            } elseif (strlen($value) > 0) {
                array_push($mappedRow, $rawrow[$value]);
            } else {
                array_push($mappedRow, "");
            }
        }

        array_push($mappedRow, date("Y-m-d H:i:s"));
        array_push($mappedRow, date("Y-m-d H:i:s"));
        return $mappedRow;
    }

    /**
     * Disable Doctrine logging
     */
    protected function disableLogging()
    {
        $config = $this->entityManager->getConnection()->getConfiguration();
        $this->originalLogger = $config->getSQLLogger();
        $config->setSQLLogger(null);
    }

    /**
     * Re-enable Doctrine logging
     */
    protected function reEnableLogging()
    {
        $config = $this->entityManager->getConnection()->getConfiguration();
        $config->setSQLLogger($this->originalLogger);
    }

    /**
     * Re-enable Doctrine logging
     *
     * @return $this
     */
    public function finish()
    {
        $this->flushAndClear();
        $this->reEnableLogging();
    }

    /**
     * Flush and clear the entity manager
     */
    protected function flushAndClear()
    {
        $this->entityManager->flush();
        $this->entityManager->clear($this->entityNameContact);
    }

}
