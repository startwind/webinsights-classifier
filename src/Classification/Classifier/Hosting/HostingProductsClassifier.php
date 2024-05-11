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
            'website-builder:duda' => 'duda',

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
            'ecommerce:ecwid' => ['ecwid'],

            # Security
            'security:imunify360' => ['imunify360'],
            'security:sitelock' => 'sitelock',
            'security:ddos-mitigation' => 'ddos mitigation',

            # Control Panel
            'control-panel:cpanel' => ['cpanel'],
            'control-panel:plesk' => ['plesk'],
            'control-panel:directadmin' => ['directadmin', 'Direct Admin'],
            'control-panel:webmin' => ['Webmin'],
            'control-panel:ispconfig' => ['ISPConfig'],
            'control-panel:froxlor' => ['Froxlor'],
            'control-panel:liveconfig' => ['LiveConfig'],
            'control-panel:cloudpanel' => ['CloudPanel', 'cloud panel'],
            'control-panel:aapanel' => ['aapanel'],
            'control-panel:control-web-panel' => ['Control Web Panel'],
            'control-panel:ajenti' => ['Ajenti'],
            'control-panel:virtualmin' => ['Virtualmin'],
            'control-panel:interworx' => ['InterWorx'],
            'control-panel:vestacp' => ['VestaCP'],
            'control-panel:spanel' => ['spanel'],
            'control-panel:spinupwp' => ['SpinupWP'],
            'control-panel:cyberpanel' => ['Cyberpanel'],
            'control-panel:webuzo' => ['webuzo'],
            'control-panel:keyhelp' => ['KeyHelp'],
            'control-panel:enhance' => ['Enhance Panel'],

            # SSL
            'ssl' => ['ssl'],
            'ssl:lets-encrypt' => ["let's encrypt"],
            'ssl:comodo' => ["Comodo SSL"],
            'ssl:digicert' => ["DigiCert"],
            'ssl:sectigo' => ["Sectigo"],
            'ssl:globalsign' => ["GlobalSign"],
            'ssl:alphassl' => ["AlphaSSL"],

            # SEO
            'seo' => ['seo tools'],
            'seo:marketgoo' => ['marketgoo'],
            'seo:rankingcoach' => ['rankingCoach', 'ranking coach'],
            'seo:semrush' => ['semrush'],

            # Monitoring
            'monitoring' => 'Uptime Monitoring',
            'monitoring:360-monitoring' => '360 Monitoring',
            'monitoring:koality' => ['koality', 'Site Quality Monitoring'],

            # Cloud
            'cloud' => ['cloud computing'],
            'cloud:aws' => ['aws', 'ec2'],
            'cloud:azure' => ['azure'],

            # Content Management Systems
            'cms' => ['content management system', 'cms'],
            'cms:wordpress' => ['wordpress'],
            'cms:joomla' => ['joomla'],
            'cms:drupal' => ['drupal'],

            # WordPress Tools
            'wordpress:wp-squared' => ['WP Squared', 'WPSquared'],
            'wordpress:wp-guardian' => ['WP Guardian', 'WPGuardian'],
            'wordpress:wp-toolkit' => ['WP toolkit', 'WordPress Toolkit', 'WPToolkit'],

            # Storage
            'storage' => ['storage server', 'cloud-speicher', 'cloud storage'],
            'storage:nextcloud' => 'nextcloud',

            # OS
            'os:cloudlinux' => ['cloudlinux', 'cloud linux'],

            # CPU
            'cpu:intel' => ['intel'],
            'cpu:amd' => ['amd'],

            # Hosting
            'hosting:colocation' => ['colocation', 'co-location'],

            'server-root' => 'root server',
            'server-dedicated' => 'dedicated server',

            'domain-name-generator' => 'domain name generator',
            'sql' => 'SQL Hosting',
            'email' => ['cloud email'],
            'reseller' => 'reseller hosting',
            'vps' => ['vps', 'vServer'],
            'webhosting' => 'webhosting',
            'managed-server' => 'managed server',
            'cdn' => ['cdn'],
            'ssd' => 'ssd',
        ]
    ];
}
