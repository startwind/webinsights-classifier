<?php

namespace Startwind\WebInsights\Classification\Classifier\Hosting;

use Startwind\WebInsights\Classification\Classifier\PatternAwareClassifier;

class HostingProductsClassifier extends PatternAwareClassifier
{
    protected bool $treeStructure = true;

    public const TAG_PREFIX = 'company:hosting:products:';

    protected array $keywords = [
        self::SOURCE_HTML => [
            # Website Builder
            'website-builder' => ['website builder', 'homepage baukasten', 'homepage-baukasten'],
            'website-builder:sitejet' => 'Sitejet',
            'website-builder:elementor' => 'Elementor',

            # Backup
            'backup' => ['backup'],
            'backup:acronis' => ['acronis'],
            'backup:jetpack' => ['jetpack'],
            'backup:codeguard' => ['codeguard'],

            # E-Commerce
            'ecommerce' => ['online shop', 'onlineshop'],
            'ecommerce:woocommerce' => ['woocommerce'],
            'ecommerce:shopware' => ['shopware'],
            'ecommerce:magento' => ['magento'],

            # Security
            'security:imunify360' => ['imunify360'],
            'security:sitelock' => 'sitelock',

            # Control Panel
            'control-panel:cpanel' => ['cpanel'],
            'control-panel:plesk' => ['plesk'],
            'control-panel:directadmin' => ['directadmin', 'Direct Admin'],
            'control-panel:Webmin' => ['Webmin'],
            'control-panel:ispconfig' => ['ISPConfig'],
            'control-panel:froxlor' => ['Froxlor'],
            'control-panel:liveconfig' => ['LiveConfig'],
            'control-panel:cloudpanel' => ['CloudPanel', 'cloud panel'],

            # SSL
            'ssl' => ['ssl'],
            'ssl:lets-encrypt' => ["let's encrypt"],

            # SEO
            'seo' => ['seo tools'],
            'seo:marketgoo' => ['marketgoo'],
            'seo:rankingcoach' => ['rankingCoach'],

            # Monitoring
            'monitoring:360-monitoring' => '360 Monitoring',
            'monitoring:koality' => ['koality', 'Site Quality Monitoring'],

            # Cloud
            'cloud' => ['cloud computing'],
            'cloud:aws' => ['aws', 'ec2'],
            'cloud:azure' => ['azure'],

            'server-root' => 'root server',
            'server-dedicated' => 'dedicated server',

            'wordpress' => 'wordpress',
            'domain-name-generator' => 'domain name generator',
            'sql' => 'SQL Hosting',
            'storage' => ['storage server', 'cloud-speicher'],
            'email' => ['cloud email'],
            'reseller' => 'reseller hosting',

            'datacenter' => ['datacenter', 'rechenzentrum'],


            'nextcloud' => 'nextcloud',

            'vps' => ['vps', 'vServer'],
            'webhosting' => 'webhosting',
            'managed-server' => 'managed server',
            'cdn' => ['cdn'],
            'ssd' => 'ssd',


            # WebPros
            'webpros' => 'WebPros',
            'whmcs' => 'whmcs',
            'solus' => 'solus',
            'wp-toolkit' => ['WP toolkit', 'WordPress Toolkit'],

            'wp-guardian' => 'WP Guardian',
            'wp-squared' => 'WP Squared', 'WPSquared',
        ]
    ];
}
