# HTTP Client checker

You need to have `symfony/http-client` installed.

The HTTP Client checker can have from 2 to 7 arguments based on the `info` array passed to the `request` call.

Minimal configuration :

```yaml
vdm_healthcheck:
  liveness_checkers:
    my_http:
      type: http
      arguments:
        - 'GET'
        - 'http://mon_url/to_check'
```

Complete configuration :

```yaml
vdm_healthcheck:
  liveness_checkers:
    my_http:
      type: http
      arguments:
        - 'GET'
        - 'http://mon_url/to_check'
        - ['Content-Type': 'text/plain'] # request header 
        - ['param1': 'value1'] # query param
        - 'body' # body
        - 5 # max redirect
        - 10 # timeout in seconds
```

For more detail, check the [http-client documentation](https://symfony.com/doc/current/http_client.html#making-requests)