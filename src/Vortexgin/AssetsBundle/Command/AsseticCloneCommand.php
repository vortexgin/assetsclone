<?php

namespace Vortexgin\AssetsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Vortexgin\AssetsBundle\Util\S3;
use Vortexgin\AssetsBundle\Util\File;

class AsseticCloneCommand extends ContainerAwareCommand
{

    private $container;

    protected function configure()
    {
        $this->setName('assetic:clone')
            ->setDescription('Clone Assets into CDN');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->container = $this->getContainer();
            $this->container->enterScope('request');
            $this->container->set('request', new Request(), 'request');

            $finder = new Finder();
            $s3 = new S3($this->container->getParameter('s3.access_key'), $this->container->getParameter('s3.secret_key'), $this->container->getParameter('s3.host'));
            $webPath = $this->container->getParameter('kernel.root_dir').'/../web/';

            $finder->files()
                ->in($webPath)
                ->size('>0')
                ->notName('.htaccess')
                ->notName('app.php')
                ->notName('app_dev.php')
                ->notName('robots.txt');
            foreach ($finder as $file) {
                $mime = File::get_mime_type($file->getRelativePathname());

                $output->writeln(sprintf("<info>%s</info>", 'Cloning '.$file->getRelativePathname().' from '.$file->getRealPath().' with mime type '.$mime));
                //$s3->deleteObject($this->container->getParameter('s3.bucket_assets'), urlencode($file->getRelativePathname()));
                $s3->putObjectFile($file->getRealPath(), $this->container->getParameter('s3.bucket_assets'), urlencode($file->getRelativePathname()), S3::ACL_PUBLIC_READ, array(), $mime);
            }
            return true;
        } catch (\Exception $e) {
            $output->writeln(sprintf("<error>%s</error>", $e->getMessage()));
            $this->container->get('logger')->error($e->getMessage());
            $this->container->get('logger')->error($e->getTraceAsString());
        }
    }
}
