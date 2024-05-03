<?php

namespace Startwind\WebInsights\Classification\Classifier\Hosting;

use Startwind\WebInsights\Classification\Classifier\PatternAwareClassifier;

class HostingProductsClassifier extends PatternAwareClassifier
{
    public const TAG_PREFIX = 'company:hosting:products:';

    protected array $keywords = [
        self::SOURCE_HTML => [
            'vps' => ['vps', 'vServer'],
            'webhosting' => 'webhosting',
            'managed-server' => 'managed server',
            'cdn' => ['cdn'],
            'website-builder' => ['website builder', 'homepage baukasten', 'homepage-baukasten'],
            'ssd' => 'ssd',
            'backup' => ['backup', 'acronis', 'jetbackup', 'codeguard'],
            'security' => ['imunify360'],
            'server-root' => 'root server',
            'server-dedicated' => 'dedicated server',
            'money-back' => ['money back', 'geld-zurück', 'geld zurück'],
            'eco-friendly' => ['ökostrom', 'oekostrom'],
            'ssl' => ['ssl', "let's encrypt"],
            'wordpress' => 'wordpress',
            'cloud' => ['cloud computing', 'aws', 'azure'],
            'domain-name-generator' => 'domain name generator',
            'sql' => 'SQL Hosting',
            'storage' => ['storage server', 'cloud-speicher'],
            'ecommerce' => ['woocommerce', 'shopware', 'magento', 'online shop', 'onlineshop'],
            'seo' => ['seo tools'],
            'email' => ['cloud email'],
            'reseller' => 'reseller hosting',
            'control-panel' => ['cpanel', 'plesk', 'directadmin'],
            'aws' => ['aws', 'ec2'],
            'ripe' => 'ripe-ncc',
            'denic' => 'denic',

            # Tools
            'direct-admin' => ['directAdmin', 'Direct Admin'],
            'imunify360' => ['imunify360'],
            'acronis' => ['acronis'],
            'rankingcoach' => 'rankingCoach',
            'marketgoo' => 'marketgoo',
            'nextcloud' => 'nextcloud',
            'sitelock' => 'sitelock',
            'codeguard' => 'codeguard',


            # WebPros
            'webpros' => 'WebPros',
            'whmcs' => 'whmcs',
            'plesk' => 'plesk',
            'cpanel' => 'cpanel',
            'solus' => 'solus',
            'wp-toolkit' => ['WP toolkit', 'WordPress Toolkit'],
            '360-monitoring' => '360 Monitoring',
            'koality' => ['koality', 'Site Quality Monitoring'],
            'wp-guardian' => 'WP Guardian',
            'wp-squared' => 'WP Squared', 'WPSquared'
        ]
    ];
}
