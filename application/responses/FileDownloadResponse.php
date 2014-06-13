<?php

class FileDownloadResponse extends Response {
    public $status = 200;

    /**
     * @var AppResource
     */
    public $resource;
    public $path;
    public $fileName = null;
    public $contentType = 'application/octet-stream';

    public $isRange = false;
    public $rangeBeginning = null;
    public $rangeEnding = null;

    public function __construct(AppResource $resource, $path, $properties = array()) {
        parent::__construct($properties);

        $this->fileName = empty($this->fileName) ? 'file.' . BusinessRules::$singleton->mime_types[$this->contentType] : $this->fileName;

        $this->resource = $resource;
        $this->path = $path;
        $this->headers['Content-Description'] = 'File Transfer';
        $this->headers['Content-Type'] = $this->contentType;
        $this->headers['Content-Disposition'] = 'attachment; filename="' . $this->fileName . '"';
        $this->headers['Content-Transfer-Encoding'] = 'binary';
        $this->headers['Expires'] = '0';
        $this->headers['Cache-Control'] = 'must-revalidate';
        $this->headers['Pragma'] = 'public';
        $this->headers['Content-Length'] = filesize($this->path);
        $this->headers['Accept-Range'] = 'bytes';

        $fileSize = filesize($this->path);

        if($this->rangeEnding == -1) {
            $this->rangeEnding = $fileSize - 1;
        }

        if($this->isRange) {
            $this->status = 206;
            $this->headers['Content-Length'] = ($this->rangeEnding - $this->rangeBeginning) + 1;
            $this->headers['Content-Range'] = 'bytes ' . $this->rangeBeginning . '-' . ($this->rangeEnding) . '/' . $fileSize;
        }
    }

    public function render() {
        if(!$this->isRange) {
            readfile($this->path);
            return;
        }

        $filePointer = fopen($this->path, 'rb');

        fseek($filePointer, ($this->rangeBeginning));

        $contentLength = $this->rangeEnding == -1 ? (filesize($this->path) - $this->rangeBeginning) : (($this->rangeEnding - $this->rangeBeginning) + 1);

        echo fread($filePointer, $contentLength);
/*        $remainingShit = $contentLength;

        while ($remainingShit > 0) {
            $toRead = ($remainingShit <= 512 ? $remainingShit : 512);

            print fread($filePointer, $toRead);

            flush();

            $remainingShit -= $toRead;
        }
*/
    }
}