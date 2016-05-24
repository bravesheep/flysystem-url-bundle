# BravesheepFlysystemUrlBundle

A [Symfony](https://symfony.com/) bundle that creates flysystem services
based upon a url specified in the symfony configuration.

## Installation and configuration
Using [Composer](https://getcomposer.org/) add the bundle to your
dependencies using the require command: 
`composer require bravesheep/flysystem-url-bundle`.

## Add the bundle to your AppKernel
Add the bundle in your `app/AppKernel.php`. **Note**: in order for the
parameters defined by this bundle to be picked up by other bundles using
them you need to put it before those bundles. See below for more
information.

```php
public function registerBundles()
{
    return array(
        // ...
        new Bravesheep\FlysystemUrlBundle\BravesheepFlysystemUrlBundle(),
        // ...
    );
}
```

## Configure the bundle
For this bundle you need to configure which urls should be used and what
the service names should be for the generated services. An example 
configuration is shown below where the `media_fs_url` parameter (which
you can put in your `parameters.yml` for example) generates a
flysystem adapter service for you to use.

```yaml
bravesheep_flysystem_url:
    urls:
        # Generates media_fs_adapter.adapter service
        media:
            url: %media_fs_url%
            prefix: media_fs_adapter
```

## Accepted urls
Several url formats are accepted, a short overview of supported urls
is shown below:

* `local:///path/to/directory` or `file:///path/to/directory` for local
  filesystem storage. In case of local urls an absolute path is 
  required. Use for example the `kernel.root_dir` parameter: 
  `file://%kernel.root_dir%/../web/media`. The `lock` query parameter 
  may be used to indicate different locking behavior: 
  `local:///path/to/directory?lock=false`. Public file permissions may 
  be set using the `file_perm` parameter:
  `local:///path/to/directory?file_perm=0744`. Similarly the 
  `file_perm_private`, `dir_perm` and `dir_perm_private` parameters are
  available.
* `ftp://user:pass@host:port/path/to/dir` for FTP usage. Alternatively
  `ftps` scheme may be used for SSL secured FTP.
* `sftp://user:pass@host:port/path/to/dir?keyfile=path/to/keyfile` or
  alternatively the `ssh` scheme may be specified for the same
  behavior.
* `dropbox://access_token:app_secret@dropbox.com` for Dropbox access.
* `s3://key:secret@region/bucket` for Amazon AWS S3. Alternatively the
  `awss3` and `awss3v3` schemes are available. For the v2 version of the
  AWS API you may use `awss3v2` as a scheme.
* `null://` or just `null` may be specified for a testing storage
  adapter which does not store anything.
* `zip:///path/to/file.zip` may be used for local zip access.
* `webdav://user:pass@host:port/path/to/dir` may be used for WebDAV
  access. Alternatively `http://user:pass@host:port/path/to/dir` or
  `https://user:pass@host:port/path/to/dir` may be used as well.

## Extra variable encodings
By default this bundle does not just create a flysystem adapter service
but can also generate some extra parameters which you can directly use.

The bundle by default generates a parameter you can use in the 
OneupFlysystemBundle as a value for your adapter:

```yaml
oneup_flysystem:
    adapters:
        media_adapter: %media_fs_adapter.oneup_adapter_params%
```

By default the bundle also generates a public prefix url for some
services if it can determine that url automatically. You could use this
for example with the VichUploaderBundle:

```yaml
vich_uploader:
    mappings:
        example:
            upload_destination: media_fs
            uri_prefix: %media_fs_adapter.public_url_prefix%
```

If you don't need these parameters you can change them in the
configuration of this bundle:

```yaml
bravesheep_flysystem_url:
    urls:
        example:
            url: %example_param%
            prefix: example_fs_prefix
            encoders: [] # In this case no parameter encoders are used, only the service is generated
```
