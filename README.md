# Assets Clone

This project is a tool for clone assets into S3 on Symfony project.

## Install
```
...
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/vortexgin/assetsclone"
        }
    ],
    "require": {
        "vortexgin/assetsclone": "^1.0",
    },
...
```

## Symfony Configuration
### Registering Bundles
```
public function registerBundles()
{
  ...
  new Vortexgin\AssetsBundle\VortexginAssetsBundle(),
  ...
}
```
### Configuration
```
...
parameters:
    s3.host: __YOUR_S3_HOST__
    s3.access_key: __YOUR_ACCESS_KEY__
    s3.secret_key: __YOUR_SECRET_KEY__
    s3.bucket_assets: '__YOUR_BUCKET_NAME__'
framework:
    templating:
        assets_base_url: __YOUR_BUCKET_URL__
```

## How to Use
``
php app/console assetic:clone
``
