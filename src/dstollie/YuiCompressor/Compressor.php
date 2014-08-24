<?php

namespace dstollie\YuiCompressor;

class Compressor
{

    // absolute path to YUI jar file.
    private $jarPath;
    private $tempFilesDir;
    private $options = array('type' => 'js',
        'linebreak' => false,
        'verbose' => false,
        'nomunge' => false,
        'semi' => false,
        'nooptimize' => false,
        'outfile' => false);
    private $files = array();
    private $fileContents = '';
    private $compressionOutput;

    // construct with a path to the YUI jar and a path to a place to put temporary files
    /**
     * @param $jarPath string The location of the yui compressor jar file
     * @param null $tempFilesDir The location where the temporarily file can be placed. If this variable is null, php .ini's temp dir will be used.
     * @param array $options Set the YUI Compressor options
     */
    function __construct($jarPath, $tempFilesDir = null, $options = array())
    {
        $this->jarPath = $jarPath;
        // if the temp directory is not geven, fallback to php's temporary file directory
        $this->tempFilesDir = !$tempFilesDir ? $tempFilesDir : sys_get_temp_dir();

        foreach ($options as $option => $value)
        {
            $this->setOption($option, $value);
        }
    }

    /**
     * Set one of the YUI compressor options
     * @param $option string The option which has to be set
     * @param $value string The value for the given option
     */
    function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }

    /**
     * add one file to the stack to be compressed
     * @param $file string A single file (absolute paths) which have to be compressed
     */
    function addFile($file)
    {
        array_push($this->files, $file);
    }

    /**
     * add multiple files to the stack to be compressed
     * @param $files array An array with files (absolute paths) which have to be compressed
     */
    function addFiles($files)
    {
        $this->files = array_merge($this->files, $files);
    }

    // add a strong to be compressed
    /**
     * @param $string
     */
    function addString($string)
    {
        $this->fileContents .= ' ' . $string;
    }

    /**
     * Get the output of the last executed compression
     * @return mixed
     */
    public function getCompressionOutput()
    {
        return $this->compressionOutput;
    }

    /**
     * Generates the command and executes it. If the compression succeeded this method will return a true. When an error occurs before the compression an Exception of the class CompressorException will be thrown.
     * When yui compressor returns an error there will no longer be thow an Exception but instead a false will be returned, the error will be saved in the compressionOutput variable. When the compression has been
     * executed, the output of it will be stored in the compressionOutput variable. This variable can publicly be accessed with the getCompressionOutput() method.
     * @param null $files string|array A single file or an array with files (absolute paths) which have to be compressed.
     * @param null $outfile string the location of the output file. If this variable is null than the compressed content will be returned as text
     * @return bool|string
     */
    function compress($files = null, $outfile = null)
    {
        // clear the compression output because a new compression is going to be executed.
        $this->compressionOutput = null;

        if(is_string($files)) {
            $this->addFile($files);
        } else if(is_array($files)){
            $this->addFiles($files);
        }

        // if the files property is empty than compression is already finished
        if(!$this->files) {
            return true;
        }

        if($outfile) {
            $this->setOption('outfile', $outfile);
        }

        $this->__validate();

        $combinedFile = $this->__toSingleFile();

        // start with basic command
        $cmd = "java -Xmx32m -jar " . escapeshellarg($this->jarPath) . ' ' . escapeshellarg($combinedFile) . " --charset UTF-8";

        // set the file type
        $cmd .= " --type " . (strtolower($this->options['type']) == "css" ? "css" : "js");

        if($this->options['outfile']) {
            $cmd .= ' -o ' . $this->options['outfile'];
        }

        // and add options as needed
        if ($this->options['linebreak'] && intval($this->options['linebreak']) > 0) {
            $cmd .= ' --line-break ' . intval($this->options['linebreak']);
        }

        if ($this->options['verbose']) {
            $cmd .= " -v";
        }

        if ($this->options['nomunge']) {
            $cmd .= ' --nomunge';
        }

        if ($this->options['semi']) {
            $cmd .= ' --preserve-semi';
        }

        if ($this->options['nooptimize']) {
            $cmd .= ' --disable-optimizations';
        }

        // execute the command
        exec($cmd . ' 2>&1', $rawOutput, $returnVar);

        // add line breaks to show errors in an intelligible manner
        $flattenedOutput = implode("\n", $rawOutput);

        // clean up (remove temp file)
        unlink($combinedFile);

        $this->compressionOutput = $flattenedOutput;

        // if the return var is not exactly 0 than compressing could not be done
        if($returnVar !== 0) {
            return false;
        }
        return true;
    }

    /**
     * Validates a few important properties before the compression can happen
     */
    private function __validate() {
        // checking if the temporary file directory is accessible and writable
        if($this->tempFilesDir) {
            if(!file_exists($this->tempFilesDir)) {
                throw new CompressorException('The temp file directory: ' . $this->tempFilesDir . ', is not accessible');
            } else if(!is_writable($this->tempFilesDir)) {
                throw new CompressorException('The temp file directory: ' , $this->tempFilesDir . ', is not writable');
            }
        }

        // checking if the jar file is accessible
        if(!file_exists($this->jarPath)) {
            throw new CompressorException('The jar file could not be fond at ' . $this->jarPath);
        }

        // looping trough all the added files
        foreach ($this->files as $file) {
            //getting the content of each file and add the content to a string
            if(!file_exists($file)) {
                throw new CompressorException('The file ' . $file . ' could not be found.');
            }
        }
    }

    private function __toSingleFile() {
        foreach ($this->files as $file) {
            //getting the content of each file and add the content to a string which contains the content of all files to compress
            $this->fileContents .= file_get_contents($file);
        }

        // create single file from all input
        $file = tempnam($this->tempFilesDir, sha1($this->fileContents));
        if(!$handle = fopen($file, 'w')) {
            throw new CompressorException('Could not create the temporary file: ' . $file);
        }
        fwrite($handle, $this->fileContents);
        fclose($handle);

        return $file;
    }
}

?>