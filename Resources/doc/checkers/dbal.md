# Doctrine DBAL checker

You need to have `doctrine dbal` installed. It is already done if you have installed the `symfony/orm-pack`.

You can configure multiple dbal checker. Each checker needs a reference to a `Doctrine\DBAL\Connection` service.

```yaml
vdm_healthcheck:
  liveness_checkers:
    my_db:
      type: dbal
      arguments:
        - '@doctrine.dbal.default_connection'
``` 

You can create different checkers to check the connection to different databases used by your application.
