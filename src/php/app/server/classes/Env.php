<?php
namespace app\server\classes;
/**
*  https://dev.to/fadymr/php-create-your-own-php-dotenv-3k2i 2023. 08.16
*/

/**
* Env class represents a simple way to load environment variables from a .env file.
* 
* This class has a path property that stores the directory where the .env file can be located.
* It also has a load method that reads the .env file and sets the environment variables using putenv, $_ENV, and $_SERVER arrays.
* It ignores the lines that start with # or are empty. It also checks if the environment variable is already set before overriding it.
* 
* @package app\server\classes
*/
class Env
{
    /**
     * The directory where the .env file can be located.
     *
     * @var string
     */
    protected $path;

    /**
    * Constructor for the Env class.
    * 
    * This method sets the path property to the directory where the .env file can be located.
    * It also checks if the file exists and throws an exception if it does not.
    * 
    * @param string $path The directory where the .env file can be located.
    * @throws \InvalidArgumentException If the file does not exist.
    */
    public function __construct(string $path,bool $dev=false)
    {
        $path=$dev?$path.'.local':$path.'.production';
        if(!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('%s does not exist', $path));
        }
        $this->path = $path;
    }

    /**
    * Load the environment variables from the .env file.
    * 
    * This method reads the .env file line by line and parses the name and value of each environment variable.
    * It ignores the lines that start with # or are empty.
    * It also checks if the environment variable is already set before overriding it.
    * It sets the environment variables using putenv, $_ENV, and $_SERVER arrays.
    * It also checks if the file is readable and throws an exception if it is not.
    * 
    * @throws \RuntimeException If the file is not readable.
    */
    public function load() :void
    {
        if (!is_readable($this->path)) {
            throw new \RuntimeException(sprintf('%s file is not readable', $this->path));
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {

            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

