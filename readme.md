# Customer classifier

## Install

```shell
git clone git@github.com:startwind/webinsights-classifier.git

cd classifier
composer install
```

## Run

```shell
php bin/classifier.php classify https://www.ionos.de
```

## Classifier

### E-commerce systems

- **ecommerce:system:magento**
- **ecommerce:system:woocommerce**
- **ecommerce:system:shopware**
- **ecommerce:system:bigcommerce**
- **ecommerce:system:prestashop**
- **ecommerce:system:shopify**

### Content management systems (CMS)

- **cms:system:wordpress**
- **cms:system:drupal**
- **cms:system:joomla**
- **cms:system:wix**
- **cms:system:squarespace**
- **cms:system:contao**
- **cms:system:sulu**

### WordPress plugins

- **wordpress:plugin**
- **wordpress:theme**

### Control panel

- **control_panel:system:plesk**

### Html frameworks

- **html:framework:nuxt**
- **html:framework:vue**
- **html:framework:jquery**
- **html:framework:boostrap**

### HTML content

- **html:content:language**

### HTML plugins

#### Monitoring plugins

- **html:plugin:monitoring:sentry'**
- **html:plugin:monitoring:new-relic'**
- **html:plugin:monitoring:site24x7'**
- **html:plugin:monitoring:appdynamics'**
- **html:plugin:monitoring:datadog'**

#### Google plugins

- **html:plugin:google:analytics**
- **html:plugin:google:tag-manager**
- **html:plugin:google:fonts**
- **html:plugin:google:maps**
- **html:plugin:google:ads**
- **html:plugin:google:firebase**
- **html:plugin:google:firebase:messaging**

#### Cookie consent plugins

- **html:plugin:cookie:cookie-bot**
- **html:plugin:cookie:cookiehub**

#### Tracking plugins

- **html:plugin:tracking:etracker**

### Browser features

- **browser:feature:service-worker**

### Technology

#### Programming language

- **tech:language:php**

#### Web server

- **tech:webserver:apache**
- **tech:webserver:nginx**
- **tech:webserver:litespeed**
- **tech:webserver:amazon-s3**

#### Reverse proxy

- **tech:reverse-proxy:varnish**

#### CDN

- **tech:cdn:cloudflare**
- **tech:cdn:akamai**
- **tech:cdn:imperva**

## Classifier ideas

- Find WordPress plugins automatically
- Find WordPress themes automatically


## Development ideas

- ZIP the result
- Set client via config file
- Filter duplicate URLs
