<?php


namespace Jin\Log\Listener;


use Bat\FileSystemTool;
use Bat\FileTool;

/**
 * @info The FileLoggerListener is a simple logger listener which writes the log messages to a specified file.
 * When the file size get bigger than a certain threshold, the file is rotated (copied to an archive file,
 * and the original file is emptied so that we can log new messages into it again).
 *
 * Note: the rotation system is optional and active by default with a default size of 2M.
 * Also, the rotation system by default zips all archives (rotated files).
 * This behaviour can be changed in the configuration of the properties.
 *
 * About the rotation system:
 * the rotation is executed after the message is written, which means the maxFileSize is not a strict limit,
 * but rather an indication AFTER WHICH the FileLoggerListener performs the rotation.
 *
 *
 */
class FileLoggerListener implements LoggerListenerInterface
{

    /**
     * @info This property holds the path to the log file.
     * This class will attempt to create it if it does not exist.
     */
    protected $file;


    /**
     * @info This property holds whether the file rotation system should be used.
     * @type bool=true
     */
    protected $isFileRotationEnabled;

    /**
     * @info This property holds the maximum file size beyond which the rotation is triggered (only if the rotation
     * system is active).
     * The default value is 2M.
     * The syntax allowed here is defined in the XXX class.
     * @seeMethod XXX:method
     *
     *
     *
     */
    protected $maxFileSize;


    /**
     * @info This property holds the format for the rotated file(s).
     * The following tags are available:
     * - {fileName}: the file name
     * - {number}: an auto-incremented number
     * - {dateTime}: the date time, like this: 2019-01-16__17-04-40
     * - {extension}: the extension of the log file (non-zipped version)
     *
     * The default format is: {fileName}-{dateTime}.{extension}
     *
     * Note that if the file is zipped (see zipRotatedFiles property below),
     * the ".zip" extension is being added.
     *
     */
    protected $rotatedFileFormat;

    /**
     * @info This property holds whether the rotated files should be zipped.
     * If true, then the rotated files are zipped.
     * @type bool=true
     */
    protected $zipRotatedFiles;


    /**
     * @info Sets default values for the configurable properties.
     */
    public function __construct()
    {
        $this->file = "/tmp/jin_default_log_file.log";
        $this->isFileRotationEnabled = true;
        $this->maxFileSize = "2M";
        $this->rotatedFileFormat = '{fileName}-{dateTime}.{extension}';
        $this->zipRotatedFiles = true;

    }


    /**
     * @info Configure the properties using an array (useful for dynamically created services).
     * @param array $options (the keys are the configurable properties of this class, see the corresponding properties for more info)
     *      - file
     *      - isFileRotationEnabled
     *      - maxFileSize
     *      - rotatedFileFormat
     *      - zipRotatedFiles
     */
    public function configure(array $options)
    {
        if(array_key_exists("file", $options)){
            $this->file = $options['file'];
        }
        if(array_key_exists("isFileRotationEnabled", $options)){
            $this->isFileRotationEnabled = (bool)$options['isFileRotationEnabled'];
        }
        if(array_key_exists("maxFileSize", $options)){
            $this->maxFileSize = $options['maxFileSize'];
        }
        if(array_key_exists("rotatedFileFormat", $options)){
            $this->rotatedFileFormat = $options['rotatedFileFormat'];
        }
        if(array_key_exists("zipRotatedFiles", $options)){
            $this->zipRotatedFiles = (bool)$options['zipRotatedFiles'];
        }
    }

    /**
     * @info Writes the logger message to the file specified in the configuration,
     * and rotates the file when the file size gets too big.
     * See more in the class description.
     *
     * @implementation
     */
    public function listen($msg, $channel)
    {
        FileTool::append($msg . PHP_EOL, $this->file);
    }


}