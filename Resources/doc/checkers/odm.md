# Doctrine ODM checker

You need to have `doctrine odm` installed. It is already done if you have installed the `doctrine/mongodb-odm-bundle`.

You can configure multiple odm checker. Each checker needs a reference to a `MongoDB\Client` and `Doctrine\ODM\MongoDB\Configuration` service.

```yaml
vdm_healthcheck:
  liveness_checkers:
    my_db:
      type: odm
      arguments:
        - '@doctrine_mongodb.odm.default_connection'
        - '@doctrine_mongodb.odm.default_configuration'
``` 

You can create different checkers to check the connection to different databases used by your application.
