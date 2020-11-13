<?php

namespace Vdm\Bundle\HealthcheckBundle\Healthcheck;

class CheckerConfig
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * CheckerConfig constructor.
     * @param string $name
     * @param string $type
     * @param array $arguments
     */
    public function __construct(string $name, string $type, array $arguments = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->arguments = $arguments;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
}
