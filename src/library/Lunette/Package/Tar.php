<?php
/**
 * Lunette Platform
 * 
 * Lunette is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 *
 * Lunette is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Lunette. If not, see <http://www.gnu.org/licenses/>.
 * 
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 * @version $Id$
 */
/**
 * Lunette archive reader
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
class Lunette_Package_Tar
{
    /**
     * @var string Name of the archive
     */
    protected $_name;

    /**
     * @var resource
     */
    protected $_file;

    /**
     * Creates a new tar archive reader
     *
     * @param string $name  The name of the tar archive to read
     */
    public function __construct($name)
    {
        $this->_name = $name;
        $this->_open();
    }

    /**
     * Destructor
     *
     */
    public function __destruct()
    {
        $this->_close();
    }

    /**
     * Extracts the archive
     * 
     * You can have the beginning of the filename removed by specifying the
     * $removePath parameter.  If the archive filename is
     * <code>example/test/folder/file.txt</code> and you specify $removePath to
     * be <code>example/test</code>, that part will be removed.
     * 
     * All directories are recursively created.
     * 
     * Existing files will be overwritten (if existing files are not writable,
     * an exception will be thrown.)  An exception will be thrown if extracting
     * a file and a directory with the same name already exists, or extracting
     * a directory and a file with the same name already exists.  An exception
     * will be thrown if the file's size on the filesystem doesn't match the
     * size listed in the archive.
     *
     * @param string $path The path of the directory where the files/dir need to by extracted.
     * @param string $removePath  Part of the memorized path that can be removed if present at the beginning of the file/dir path.
     * @throws Lunette_Package_Tar_Exception if an error occurs
     */
    public function extract($path, $removePath = null)
    {
        $contents = $this->_extract($path, false, array(), $removePath);
    }

    /**
     * Extracts one file from the archive
     * 
     * @return string The file contents
     * @throws Lunette_Package_Tar_Exception if an error occurs
     */
    public function extractFile($filename)
    {
        $fileContents = null;
        
        while (strlen($data = $this->_readBlock()) != 0) {
            $header = $this->_readHeader($data);
            
            if ($header['filename'] == '') {
                continue;
            }
            if ($header['typeflag'] == 'L') {
                $header = $this->_readLongHeader($header);
            }
            if ($header['filename'] == $filename) {
                if ($header['typeflag'] == "5") {
                    require_once 'Lunette/Package/Tar/Exception.php';
                    throw new Lunette_Package_Tar_Exception('Cannot extract a directory: ' . $header['filename']);
                } else {
                    $n = floor($header['size'] / 512);
                    for ($i=0; $i<$n; $i++) {
                        $fileContents .= $this->_readBlock();
                    }
                    if (($header['size'] % 512) != 0) {
                        $content = $this->_readBlock();
                        $fileContents .= substr($content, 0,
                            $header['size'] % 512);
                    }
                    break;
                }
            } else {
                $this->_jumpBlock(ceil($header['size'] / 512));
            }
        }

        return $fileContents;
    }    

    /**
     * Extracts a list of files from the archive
     * 
     * If indicated the $removePath can be used in the same way as it is
     * used in the extract() method.
     * 
     * @param array $fileList An array of filenames and directory names
     * @param string $path The path where the will be extracted
     * @param string $removePath Will be removed from the beginning of files
     * @see extract()
     */
    public function extractList(array $fileList, $path='', $removePath='')
    {
        $this->_extract($path, false, $fileList, $removePath);
    }
        
    /**
     * Lists the contents of the archive
     *
     * The returned array contains arrays with the header information for each
     * file.  The following header fields are available:
     * 
     * - filename
     * - checksum
     * - typeflag
     * - mode
     * - uid
     * - gid
     * - size
     * - mtime
     * 
     * @return array
     */
    public function ls()
    {
        return $this->_extract(null, true);
    }
    
    /**
     * Extracts (or lists) the files in the archive
     *
     * @param string $path The path to extract the archive files
     * @param boolean $onlyList True to not extract the files, false to extract
     * @param array $fileList
     * @param string $removePath
     * @return array Containing the filenames extracted or listed
     */
    private function _extract( $path, $onlyList = false, array $fileList = array(), $removePath = null)
    {
        $extractAll = !$onlyList && !count($fileList);
        
        $path = $this->_normalizePath($path, false);
        if ($path == '' || (substr($path, 0, 1) != '/'
            && substr($path, 0, 3) != "../" && !strpos($path, ':'))) {
            $path = "./" . $path;
        }
        
        $removePath = $this->_normalizePath($removePath);
        if (($removePath != '') && (substr($removePath, -1) != '/')) {
            $removePath .= '/';
        }
        $removePathSize = strlen($removePath);
        
        clearstatcache();
        
        $listDetail = array();
        while (strlen($data = $this->_readBlock()) != 0) {
            $header = $this->_readHeader($data);
            
            if ($header['filename'] == '') {
                continue;
            }
            if ($header['typeflag'] == 'L') {
                $header = $this->_readLongHeader($header);
            }
            
            $extractFile = (!$extractAll && count($fileList)) ?
                $this->_canExtractFile($header, $fileList) : true;

            if ($extractFile && !$onlyList) {
                if ($removePath != '' && substr($header['filename'], 0, $removePathSize) == $removePath) {
                    $header['filename'] = substr($header['filename'], $removePathSize);
                }
                if ($path != './' && $path != '/') {
                    while (substr($path, -1) == '/') {
                        $path = substr($path, 0, strlen($path) - 1);
                    }
                    if (substr($header['filename'], 0, 1) == '/') {
                        $header['filename'] = $path . $header['filename'];
                    } else { 
                        $header['filename'] = $path . '/' . $header['filename'];
                    }
                }
                if (file_exists($header['filename'])) {
                    if (@is_dir($header['filename']) && $header['typeflag'] == '') {
                        require_once 'Lunette/Package/Tar/Exception.php';
                        throw new Lunette_Package_Tar_Exception('File already exists as a directory: ' . $header['filename']);
                    }
                    if ($this->_exists($header['filename']) && $header['typeflag'] == "5") {
                        require_once 'Lunette/Package/Tar/Exception.php';
                        throw new Lunette_Package_Tar_Exception('Directory already exists as a file: ' . $header['filename']);
                    }
                    if (!is_writeable($header['filename'])) {
                        require_once 'Lunette/Package/Tar/Exception.php';
                        throw new Lunette_Package_Tar_Exception('Cannot overwrite existing file: ' . $header['filename']);
                    }
                } else {
                    $this->_createDirectory($header['typeflag'] == "5" ?
                        $header['filename'] : dirname($header['filename']));
                }

                if ($header['typeflag'] == "5") {
                    if (!@file_exists($header['filename']) && !@mkdir($header['filename'], 0777)) {
                        require_once 'Lunette/Package/Tar/Exception.php';
                        throw new Lunette_Package_Tar_Exception('Unable to create directory: ' . $header['filename']);
                    }
                } else if ($header['typeflag'] == "2") {
                    if (!@symlink($header['link'], $header['filename'])) {
                        require_once 'Lunette/Package/Tar/Exception.php';
                        throw new Lunette_Package_Tar_Exception('Unable to extract symbolic link: ' . $header['filename']);
                    }
                } else {
                    if ($destFile = @fopen($header['filename'], "wb")) {
                        $n = floor($header['size'] / 512);
                        for ($i=0; $i<$n; $i++) {
                            $content = $this->_readBlock();
                            @fwrite($destFile, $content, 512);
                        }
                        if (($header['size'] % 512) != 0) {
                            $content = $this->_readBlock();
                            @fwrite($destFile, $content, ($header['size'] % 512));
                        }

                        @fclose($destFile);

                        @touch($header['filename'], $header['mtime']);
                        if ($header['mode'] & 0111) {
                            $mode = fileperms($header['filename']) | (~umask() & 0111);
                            @chmod($header['filename'], $mode);
                        }
                    } else {
                        require_once 'Lunette/Package/Tar/Exception.php';
                        throw new Lunette_Package_Tar_Exception('Cannot write to file: ' . $header['filename']);
                    }

                    clearstatcache();
                    if (filesize($header['filename']) != $header['size']) {
                        require_once 'Lunette/Package/Tar/Exception.php';
                        throw new Lunette_Package_Tar_Exception('Extracted file ' . $header['filename'] . 
                            ' does not have the correct file size (' . filesize($header['filename']) .
                            ') (' . $header['size'] . ' expected). Archive may be corrupted.');
                    }
                }
            } else {
                // we don't need to extract this file, skip it
                $this->_jumpBlock(ceil(($header['size'] / 512)));
            }

            if ($onlyList || $extractFile) {
                if ($fileDir = dirname($header['filename']) == $header['filename']) {
                    $fileDir = '';
                }
                if (substr($header['filename'], 0, 1) == '/' && $fileDir == '') {
                    $fileDir = '/';
                }

                $listDetail[] = $header;
                if (count($fileList) && count($listDetail) == count($fileList)) {
                    return $listDetail;
                }
            }
        }

        return $listDetail;
    }

    /**
     * Opens the stream
     * 
     * @throws Lunette_Package_Tar_Exception if a file access error occurs
     */
    private function _open()
    {
        $filename = $this->_name;

        // if we have a remote file
        if (preg_match('#^[a-z0-9]+://#', $this->_name)) {
            if (!$source = @fopen($this->_name, 'rb')) {
                require_once 'Lunette/Package/Tar/Exception.php';
                throw new Lunette_Package_Tar_Exception('Unable to read file: ' . $this->_name);
            }
            $name = tempnam(sys_get_temp_dir(), get_class($this));
            if (!$destination = @fopen($name, 'wb')) {
                require_once 'Lunette/Package/Tar/Exception.php';
                throw new Lunette_Package_Tar_Exception('Unable to write to file: ' . $name);
            }
            if ( @stream_copy_to_stream($source, $destination) === false ) {
                $error = error_get_last();
                require_once 'Lunette/Package/Tar/Exception.php';
                throw new Lunette_Package_Tar_Exception('Cannot make a local copy of the file: ' . $error['message']);
            }
            $filename = $name;
        }
        
        $this->_file = $this->_fopen($filename);
        if ($this->_file === false) {
            require_once 'Lunette/Package/Tar/Exception.php';
            throw new Lunette_Package_Tar_Exception('Unable to read file: ' . $filename);
        }

        return true;
    }

    /**
     * Closes the stream
     */
    private function _close()
    {
        if (is_resource($this->_file)) {
            $this->_fclose($this->_file);
            $this->_file = 0;
        }
        if ($this->_localName != '') {
            @unlink($this->_localName);
            $this->_localName = '';
        }
        return true;
    }

    /**
     * Tests if a file exists
     *
     * @param string filename
     * @return boolean
     */
    private function _exists($filename)
    {
        clearstatcache();
        return @is_file($filename);
    }

    /**
     * Reads the header info
     *
     * @param string $data
     * @return array 
     * @throws Lunette_Package_Tar_Exception if the block size is invalid
     */
    protected function _readHeader($data)
    {
        if (strlen($data) == 0) {
            return array('filename' => '');
        }
        if (strlen($data) != 512) {
            require_once 'Lunette/Package/Tar/Exception.php';
            throw new Lunette_Package_Tar_Exception('Invalid block size : '.strlen($data));
        }

        /* I CAN HAS CHECKSUM? */
        $checkSum = 0;
        for ($i=0; $i<148; $i++) {                   // First part of the header
            $checkSum += ord(substr($data, $i, 1));
        }
        for ($i=148; $i<156; $i++) {      // Replace checksum value with a space
            $checkSum += ord(' ');
        }
        for ($i=156; $i<512; $i++) {                  // Last part of the header
            $checkSum += ord(substr($data, $i, 1));
        }

        $data = unpack("a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/"
                         ."a8checksum/a1typeflag/a100link/a6magic/a2version/"
                         ."a32uname/a32gname/a8devmajor/a8devminor", $data);

        $header = array('checksum' => octdec(trim($data['checksum'])));
        if ($header['checksum'] != $checkSum) {
            $header['filename'] = '';
            // Look for last block (empty block)
            if (($checkSum == 256) && ($header['checksum'] == 0)) {
                return $header;
            }
            require_once 'Lunette/Package/Tar/Exception.php';
            throw new Lunette_Package_Tar_Exception('Invalid checksum for ' .
                $data['filename'] . ' (Got '.$checkSum.', expected ' .
                $header['checksum']);
        }

        $header['filename'] = trim($data['filename']);
        $this->_checkTraversal($header['filename']);
        $header['mode'] = octdec(trim($data['mode']));
        $header['uid'] = octdec(trim($data['uid']));
        $header['gid'] = octdec(trim($data['gid']));
        $header['size'] = octdec(trim($data['size']));
        $header['mtime'] = octdec(trim($data['mtime']));
        if (($header['typeflag'] = $data['typeflag']) == "5") {
            $header['size'] = 0;
        }
        $header['link'] = trim($data['link']);
        return $header;
    }

    /**
     * Reads a long header
     *
     * @param array $header
     * @return array
     */
    protected function _readLongHeader( array $header )
    {
        $filename = '';
        $content = '';
        $n = floor($header['size'] / 512);
        for ($i=0; $i<$n; $i++) {
            $content = $this->_readBlock();
            $filename .= $content;
        }
        if (($header['size'] % 512) != 0) {
            $content = $this->_readBlock();
            $filename .= $content;
        }
        $this->_checkTraversal($filename);

        $header = $this->_readHeader($this->_readBlock());
        $header['filename'] = $filename;

        return $header;
    }

    /**
     * Recursively create a directory
     *
     * @param string $directory directory to check
     * @return bool True if the directory exists or was created
     */
    private function _createDirectory($directory)
    {
        clearstatcache();
        if (@is_dir($directory) || $directory == '') {
            return true;
        }

        $parentDir = dirname($directory);

        if ($parentDir != $directory && $parentDir != '' && !$this->_createDirectory($parentDir)) {
             return false;
        }

        if (!@mkdir($directory, 0777)) {
            require_once 'Lunette/Package/Tar/Exception.php';
            throw new Lunette_Package_Tar_Exception("Unable to create directory: " . $directory);
        }

        return true;
    }

    /**
     * Tests if the current file should be extracted
     *
     * @param array $header
     * @param array $list
     * @return boolean True if the file should be extracted
     */
    private function _canExtractFile( array $header, array $list )
    {
        foreach( $list as $filename ) {
            if ( substr($filename, -1) == '/' &&
                    strlen($header['filename']) > strlen($filename) &&
                    substr($header['filename'], 0, strlen($filename)) == $filename ) {
                return true;
            } else if ( $filename == $header['filename'] ) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Look for a directory traversal in the filename
     *
     * @param string $file
     * @throws Lunette_Package_Tar_Exception if the filename contains a traversal
     */
    private function _checkTraversal($filename)
    {
        if (strpos($filename, '/../') !== false || strpos($filename, '../') === 0) {
            require_once 'Lunette/Package/Tar/Exception.php';
            throw new Lunette_Package_Tar_Exception('Directory traversal; will not be extracted: ' . $filename);
        }
    }

    /**
     * Translates the Windows path
     *
     * @param string $path
     * @param boolean $removeDriveLetter
     * @return string
     */
    private function _normalizePath($path, $removeDriveLetter = true)
    {
      if (substr(PHP_OS, 0, 3) == 'WIN') {
          if ($removeDriveLetter && ($position = strpos($path, ':')) !== false) {
              $path = substr($path, $position + 1);
          }
          if ( strpos($path, '\\') > 0 || substr($path, 0, 1) == '\\') {
              $path = strtr($path, '\\', '/');
          }
      }
      return $path;
    }
 
    /**
     * Reads the next block from the stream
     *
     * @return string
     */
    protected function _readBlock()
    {
        return is_resource($this->_file) ? $this->_fread($this->_file, 512) : null;
    }

    /**
     * Jumps a block in the stream
     *
     * @param int $length
     * @return boolean
     */
    protected function _jumpBlock($length=null)
    {
        if (is_resource($this->_file)) {
            if ($length === null) {
                $length = 1;
            }
            $this->_seekBlock($this->_file, $length, 512);
        }
        return true;
    }

    /**
     * Gets the file stream
     *
     * @param string $filename
     * @return resource 
     */
    protected function _fopen($filename)
    {
        return @fopen($filename, "rb");
    }

    /**
     * Closes the file stream
     *
     * @param resource $resource
     */
    protected function _fclose($resource)
    {
        @fclose($resource);
    }

    /**
     * Reads bytes from the resource
     *
     * @param resource $resource
     * @param int $bytes
     * @return string
     */
    protected function _fread($resource, $bytes)
    {
        return @fread($resource, $bytes);
    }

    /**
     * Seeks the next block
     *
     * @param resource $resource
     * @param int $length
     * @param int $bytes
     */
    protected function _seekBlock($resource, $length, $bytes)
    {
        @fseek($resource, ftell($resource) + ($length * $bytes));
    }
}
