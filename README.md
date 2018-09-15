# Bolt Redirect Extension

With this bolt extension you can configure paths to redirect. This extension has two features:

* Domain-Redirection
* Path-Redirection

## Domain-Redirection

In the configuration you can set the domain and ssl like this:

```
domain: domain.tld
ssl: true
```

If there is a request that does not match this configuration, there will be a redirect.

## Path-Redirection

You can setup path redirection by specify the routes like this:

```
redirects:
    "foo.html": "target/url"
    "bar.html": "another/target/url"
````

