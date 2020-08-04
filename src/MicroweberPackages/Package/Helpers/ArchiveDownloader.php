<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MicroweberPackages\Package\Helpers;

use Composer\Package\PackageInterface;
use Composer\Util\RemoteFilesystem as RemoteFilesystem;
use Symfony\Component\Finder\Finder;
use Composer\IO\IOInterface;


/**
 * Base downloader for archives
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author François Pluchino <francois.pluchino@opendisplay.com>
 */
abstract class ArchiveDownloader extends FileDownloader
{
    /**
     * {@inheritDoc}
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     */


    protected $retry_count = 3;

    public function download(PackageInterface $package, $path, $output = true)
    {
        $temporaryDir = $this->config->get('vendor-dir') . '/composer/' . $package->getDistSha1Checksum() . '/';
      //  $temporaryDir = $this->config->get('vendor-dir') . '/c/' . substr($package->getDistSha1Checksum(), -5). '/';



        while ($this->retry_count--) {

            $fileName = $this->getFileName($package, $path);
            if (is_file($fileName)) {

                $this->io->writeError('DownLoading from cache', false);


            } else {
                $fileName = parent::download($package, $path, $output);

            }


            if ($output) {
                $this->io->writeError(' Extracting archive', false, IOInterface::VERBOSE);
            }

            try {
                $this->filesystem->ensureDirectoryExists($temporaryDir);

                try {


                    $this->extract($fileName, $temporaryDir);


                } catch (\Exception $e) {

                    // remove cache if the file was corrupted
                    parent::clearLastCacheWrite($package);
                    throw $e;
                }

                $this->filesystem->unlink($fileName);

                $contentDir = $this->getFolderContent($temporaryDir);

                // only one dir in the archive, extract its contents out of it
                if (1 === count($contentDir) && is_dir(reset($contentDir))) {
                    $contentDir = $this->getFolderContent((string)reset($contentDir));
                }

                // move files back out of the temp dir
                foreach ($contentDir as $file) {
                    $file = (string)$file;
                    $this->filesystem->rename($file, $path . '/' . basename($file));
                }

                $this->filesystem->removeDirectory($temporaryDir);
                if ($this->filesystem->isDirEmpty($this->config->get('vendor-dir') . '/composer/')) {
                    $this->filesystem->removeDirectory($this->config->get('vendor-dir') . '/composer/');
                }
                if ($this->filesystem->isDirEmpty($this->config->get('vendor-dir'))) {
                    $this->filesystem->removeDirectory($this->config->get('vendor-dir'));
                }
            } catch (\Exception $e) {
                // clean up
                $this->filesystem->removeDirectory($path);
                $this->filesystem->removeDirectory($temporaryDir);

                // retry downloading if we have an invalid zip file
                if ($this->retry_count && $e instanceof \UnexpectedValueException && class_exists('ZipArchive') && $e->getCode() === \ZipArchive::ER_NOZIP) {
                    $this->io->writeError('');
                    if ($this->io->isDebug()) {
                        $this->io->writeError('    Invalid zip file (' . $e->getMessage() . '), retrying...');
                    } else {
                        $this->io->writeError('    Invalid zip file, retrying...');
                    }
                    usleep(500000);
                    continue;
                }

                throw $e;
            }

            break;
        }
    }


    /**
     * Extract file to directory
     *
     * @param string $file Extracted file
     * @param string $path Directory
     *
     * @throws \UnexpectedValueException If can not extract downloaded file to path
     */
    abstract protected function extract($file, $path);

    /**
     * Returns the folder content, excluding dotfiles
     *
     * @param  string $dir Directory
     * @return \SplFileInfo[]
     */
    private function getFolderContent($dir)
    {
        $finder = Finder::create()
            ->ignoreVCS(false)
            ->ignoreDotFiles(false)
            ->notName('.DS_Store')
            ->depth(0)
            ->in($dir);

        return iterator_to_array($finder);
    }
}