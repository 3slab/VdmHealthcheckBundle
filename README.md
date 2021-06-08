# VdmHealthcheckBundle

[![Build Status](https://travis-ci.com/3slab/VdmHealthcheckBundle.svg?branch=master)](https://travis-ci.com/3slab/VdmHealthcheckBundle)

This bundle provides all the tooling to implement simple healthcheck functionnality.

## Installation

```shell script
composer require 3slab/vdm-healthcheck-bundle
```

And load the routes in `routing.yml` :

```yaml
vdm_healthcheck:
  resource: "@VdmHealthcheckBundle/Resources/config/routing.yml"
  prefix:   /
```

## Configuration

Put your configuration in `config/packages/vdm_healthcheck.yaml` file. This is the default :

```yaml
vdm_healthcheck:
  secret: ~
  liveness_path: /liveness
  liveness_checkers: {}
  readiness_path: /readiness
  readiness_checkers: {}
```

Parameter | Default | Description
--- | --- | ---
`vdm_healthcheck.secret` | `null` | if set, you need to provide the secret as a GET parameter `secret` or in the 
header `VDM-HEALTHCHECK-SECRET` to get the detailed result of the healthcheck in the response body.
`vdm_healthcheck.liveness_path` | `/liveness` | Change the path of the liveness endpoint.
`vdm_healthcheck.liveness_checkers` | `{}` | Configure a list of checker for the liveness endpoint. See below for 
a detailed explanation.
`vdm_healthcheck.readiness_path` | `/readiness` | Change the path of the readiness endpoint.
`vdm_healthcheck.readiness_checkers` | `{}` | Configure a list of checker for the readiness endpoint. See below for 
a detailed explanation.

`liveness_checkers` and `readiness_checkers` are hash map. The key is the name given to this check and the value is 
an hash map to configure the check.

```yaml
vdm_healthcheck:
  liveness_checkers:
    name_of_your_checker:
      type: <checker type>
      arguments: []
``` 

Each checker has a name, a type and a list of constructor arguments depending on its type. Arguments support parameters
surrounded by `%` or service id prefixed by `@`.

Exemple :

```
vdm_healthcheck:
  liveness_checkers:
    db:
      type: dbal
      arguments:
        - '@doctrine.dbal.default_connection'
```

## Checker's types

* [Doctrine DBAL](./Resources/doc/checkers/dbal.md)
* [Doctrine ODM](./Resources/doc/checkers/odm.md)
* [HTTP](./Resources/doc/checkers/http.md)

You can also [create your own checker](./Resources/doc/create_your_own_checker.md)