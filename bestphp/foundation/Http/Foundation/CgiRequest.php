<?php


namespace Best\Http\Foundation;


class CgiRequest
{
    /**
     * @var array $_SERVER
     */
    private $server;

    /**
     * @var array $_COOKIE
     */
    private $cookie;

    /**
     * @var array $_GET
     */
    private $get;

    /**
     * @var array $_POST
     */
    private $post;

    /**
     * @var Stream  new Stream('php://input')
     */
    private $input;

    /**
     * @var UploadedFile[]  Originate from $_FILES
     */
    private $files;

    /**
     * Get $this->server
     *
     * @param string $name
     * @return array|mixed|null
     */
    public function server(string $name = '')
    {
        $name = strtoupper(trim($name));
        
        if ('' === $name) {
            return $this->server;
        }

        return $this->server[$name] ?? null;
    }

    /**
     * Set $this->server
     *
     * @param string|array $name
     * @param string|null $value
     * @return $this
     */
    public function withServer($name, $value = null)
    {
        if (is_array($name)) {
            $name = array_change_key_case($name, CASE_UPPER);
            $this->server = $name + ($this->server ?? []);
        } else {
            $name = strtoupper($name);
            $this->server[$name] = $value;
        }

        return $this;
    }

    /**
     * Get $this->cookie
     *
     * @param string $name
     * @return array|string|null
     */
    public function cookie(string $name = '')
    {
        $name = strtolower(trim($name));

        if ('' === trim($name)) {
            return $this->cookie;
        }

        return $this->cookie[$name] ?? null;
    }

    /**
     * Set $this->cookie
     *
     * @param string|array $name
     * @param string|null $value
     * @return $this
     */
    public function withCookie($name, $value = null)
    {
        if (is_array($name)) {
            $name = array_change_key_case($name, CASE_LOWER);
            $this->cookie = $name + ($this->cookie ?? []);
        } else {
            $name = strtolower($name);
            $this->cookie[$name] = $value;
        }

        return $this;
    }
    
    /**
     * Get $this->get
     *
     * @param string $name
     * @return array|string
     */
    public function get(string $name = '')
    {
        $name = strtolower(trim($name));

        if ('' === trim($name)) {
            return $this->get;
        }

        return $this->get[$name];
    }

    /**
     * Set $this->get
     *
     * @param array|string $name
     * @param string|null $value
     * @return $this
     */
    public function withGet($name, $value = null)
    {
        if (is_array($name)) {
            $name = array_change_key_case($name);
            $this->get = $name + ($this->get ?? []);
        } else {
            $name = strtolower($name);
            $this->get[$name] = $value;
        }

        return $this;
    }

    /**
     * Get the $this->post
     *
     * @param string $name
     * @return array|string|null
     */
    public function post(string $name = '')
    {
        $name = strtolower(trim($name));

        if ('' === trim($name)) {
            return $this->post;
        }

        return $this->post ?? null;
    }

    /**
     * Set the $this->post
     *
     * @param array|string $name
     * @param string|null $value
     * @return $this
     */
    public function withPost($name, $value = null)
    {
        if (is_array($name)) {
            $name = array_change_key_case($name);
            $this->post = $name + ($this->post ?? []);
        } else {
            $name = strtolower($name);
            $this->post[$name] = $value;
        }

        return $this;
    }

    /**
     * Get the data form all of $this->post and $this->get
     *
     * @param string $name
     * @return array|string|null
     */
    public function param(string $name = '')
    {
        $name = strtolower(trim($name));
        $param = $this->get() + $this->post();

        if ('' === $name) {
            return $param;
        }

        return $param[$name] ?? null;
    }

    /**
     * Get the $this->input
     *
     * @return Stream
     */
    public function input()
    {
        return $this->input;
    }

    /**
     * @return $this
     */
    public function withInput()
    {
        $this->input = (new Stream())->open('php://input', 'r');
        return $this;
    }

    /**
     * Get the $this->files
     *
     * @param string $name
     * @return UploadedFile|UploadedFile[]|null
     */
    public function files(string $name = '')
    {
        $name = strtolower(trim($name));

        if ('' === trim($name)) {
            return $this->files;
        }

        return $this->files[$name] ?? null;
    }

    /**
     * Set the $this->files
     *
     * @param string|array $name
     * @param UploadedFile|null $value
     * @return $this
     */
    public function withFiles($name, $value = null)
    {
        if (is_array($name)) {
            $name = array_change_key_case($name);
            $this->files = $name + ($this->files ?? []);
        } else {
            $name = strtolower($name);
            $this->files[$name] = $value;
        }

        return $this;
    }

    /**
     * Format the uploaded files.
     *
     * @param $uploadedFiles   $_FILE or The meta data of files that have the same structure of $_FILE
     * @return UploadedFile[]
     */
    protected function uploadedFileFactory($uploadedFiles)
    {
        $files = [];
        foreach ($uploadedFiles as $id => $info) {
            if (count($info) === count($info, 1)) {
                $files[$id] = $info;
            } else {
                foreach ($info as $key => $value) {
                    foreach ($value as $k => $v) {
                        $files[$id][$k][$key] = $v;
                    }
                }
            }
        }

        foreach ($files as $id => $info) {
            if (count($info) === count($info, 1)) {
                $files[$id] = new UploadedFile($info);
            } else {
                foreach ($info as $key => $value) {
                    $files[$id][$key] = new UploadedFile($value);
                }
            }
        }

        return $files;
    }
}