# Create you own checker

We are going to create a checker which returns service up based on the value of a boolean parameter `checker.service.is_up`.

A custom checker needs 2 classes :

* A checker factory
* A checker implementation (instantiated by the factory)

The checker factory needs to extend `Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractCheckerFactory` :

```php
use Vdm\Bundle\HealthcheckBundle\Exception\HealthcheckFactoryInvalidConfigurationException;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractChecker;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractCheckerFactory;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckerConfig;

class ParameterCheckerFactory extends AbstractCheckerFactory
{
    public static function getType(): string
    {
        return 'parameter';
    }

    public function validate(CheckerConfig $config): void
    {
        if (count($config->getArguments()) === 1) {
            throw new HealthcheckFactoryInvalidConfigurationException('parameter checker needs an argument');
        }

        if (!is_bool($config->getArguments()[0])) {
            throw new HealthcheckFactoryInvalidConfigurationException('parameter checker argument is not a boolean');
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function instantiate(CheckerConfig $config): AbstractChecker
    {
        return new ParameterChecker(...$config->getArguments());
    }
}
```

The abstract class enforces the presence of 3 methods :

* `getType` : a static method returning the type of the checker that it instantiates (used in yaml checker configuration)
* `validate` : a method called just before instantiation to validate the configuration (provided in yaml checker configuration)
* `instantiate` : the method that instantiate the checker managed by this factory

This factory should be automatically registered. If not, you can do it manually in your `services.yaml` file : 

```
services:
  App\Checker\ParameterCheckerFactory:
    tags: ['vdm_healthcheck.checker_factory']
```

Then you need the checker implementation :

```php
use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractChecker;

class ParameterChecker extends AbstractChecker
{
    /**
     * @var bool
     */
    protected $value;

    /**
     * ParameterChecker constructor.
     * @param bool $value
     */
    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getErrorType(): string
    {
        return 'PARAMETER_IS_FALSE';
    }

    /**
     * {@inheritDoc}
     */
    public function serviceCheck(): bool
    {
        return $this->value === true;
    }
}
```

You can finally use your checker in the configuration : 

```
vdm_healthcheck:
  liveness_checkers:
    my_parameter:
      type: parameter
      arguments:
        - '%checker.service.is_up%'
```