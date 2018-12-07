<?php


namespace Best\Filesystem;


class Filesystem
{
    /**
     * Parse the file to a array
     *
     * @param string $file  The file path
     * @param string $type  The file type, e.g., "ini", "php", "json"
     * @return mixed
     */
    public function parse(string $file, string $type = '')
    {
        if ('' === trim($type)) {
            $type = $this->getExtension();
        }

        $parser = 'Best\\File\\Parse\\' . ucfirst($type);

        if (!class_exists($parser)) {
            throw new \InvalidArgumentException("Parser($parser) not found");
        }

        return $parser::parse($file);
    }
}