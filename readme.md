<p align="center">
<img src="docs/logo-color.png" width="300" alt="WebInsights logo">
</p>

# WebInsights Classifier

The WebInsights classifier by [Startwind.io](https://startwind.io) is an open source toolkit that helps classifying
websites and process the created data.

The classifier consists of four tools that can be used independently of each other but unfold their full potential used
together.

## WebInsights PRO

All functionality that is needed to retrieve websites, aggregate data and create reports is included in this toolkit. We
know that not all users like to manage those technical tools on their own.

This is why we created a hosted version that can be fully used inside a browser and created beautiful reports. The lite
account is of course free so you can have a look without risks.

- [Visit WebInsights PRO](https://pro.webinsights.info)

## Contribution

Like every open source tool we love our community. So if you think you can help with your ideas, your programming skills
or just with the wish to make our documentation better, please [contact us](mailto:webinsights.startwind.io).

- [Visit our issue tracker](https://github.com/startwind/webinsights-classifier/issues)

## Installation

The requirements for the classifier are pretty small. We only need a running current version of PHP (>8.2) and git.

```shell
git clone git@github.com:startwind/webinsights-classifier.git

cd webinsights-classifier
composer install
```

## Run

```shell
php bin/classifier.php classify https://www.ionos.de
```

