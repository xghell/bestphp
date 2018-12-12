<?php


namespace Best\Filesystem;


class Filesystem
{
    /**
     * Parse the file content
     *
     * @param string $file  The file path
     * @param string $type  The file type, e.g., "ini", "php", "json"
     * @return array
     */
    public function parseFile($file, string $type = '')
    {
        if (is_array($file)) {
            $result = [];
            foreach ($file as $value) {
                $result = $this->parseFile($value) + $result;
            }
            return $result;
        }
        
        if ('' === trim($type)) {
            $type = pathinfo($file, PATHINFO_EXTENSION);
        }

        $parser = '\\Best\\Filesystem\\Parse\\' . ucfirst($type);

        if (!class_exists($parser)) {
            throw new \InvalidArgumentException("Parser($parser) not found");
        }
        
        $name = pathinfo($file, PATHINFO_FILENAME);
        
        return [$name => $parser::parse($file)];
    }

    /**
     * Parse all of the files in specified directory
     *
     * @param string $path
     * @return array
     * @throws \InvalidArgumentException
     */
    public function parseDir(string $path)
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException("The path($path) is not a directory");
        }

        $files = $this->scanDir($path);

        array_walk($files, function (&$file) use ($path) {
            $file = rtrim($path, '/') . '/' . $file;
        });
        
        return $this->parseFile($files);
    }

    /**
     * Get all Exclude '.', '..' files in specified directory,
     *
     * @param string $path
     * @return array
     */
    public function scanDir(string $path)
    {
        $files = scandir($path);
        return array_filter($files, function ($file) use ($path) {
            $file = rtrim($path, '/') . '/' . $file;
            return is_file($file);
        });
    }
}