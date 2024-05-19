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
            'website-builder:jimdo' => 'jimdo',
            'website-builder:wix' => 'wix',
            'website-builder:squarespace' => 'squarespace',
            'website-builder:webflow' => 'webflow',
            'website-builder:webnode' => 'Webnode',
            'website-builder:tilda' => 'Tilda',

            # Backup
            'backup' => ['backup'],
            'backup:acronis' => ['acronis'],
            'backup:jetpack' => ['jetpack'],
            'backup:r1soft' => ['R1Soft'],
            'backup:Cohesity' => ['Cohesity'],
            'backup:Veeam' => ['Veeam'],
            'backup:Bacula' => ['Bacula'],
            'backup:Dropsuite' => ['Dropsuite'],
            'backup:rewind' => ['rewind'],
            'backup:commvault' => ['COMMVAULT'],
            'backup:kaseya' => ['Kaseya'],
            'backup:datto' => ['datto'],
            'backup:storagecraft' => ['StorageCraft'],
            'backup:rubrik' => ['rubrik'],
            'backup:cohesity' => ['Cohesity'],
            'backup:spanning' => ['Spanning'],
            'backup:unittrends' => ['UNITRENDS'],
            'backup:carbonite' => ['CARBONITE'],
            'backup:veritas' => ['Veritas'],
            'backup:druva' => ['druva'],
            'backup:livedrive' => ['livedrive'],

            # E-Commerce
            'ecommerce' => ['online shop', 'onlineshop'],
            'ecommerce:woocommerce' => ['woocommerce'],
            'ecommerce:shopware' => ['shopware'],
            'ecommerce:magento' => ['magento'],
            'ecommerce:ecwid' => ['ecwid'],
            'ecommerce:opencart' => ['opencart'],
            'ecommerce:epages' => ['epages'],
            'ecommerce:gambio' => ['gambio'],
            'ecommerce:oscommerce' => ['oscommerce'],
            'ecommerce:caupo' => ['caupo'],
            'ecommerce:oxid' => ['oxid'],
            'ecommerce:prestashop' => ['PrestaShop'],
            'ecommerce:jtl' => ['JTL'],
            'ecommerce:avada' => ['avada'],
            'ecommerce:plentymarket' => ['plentymarket'],
            'ecommerce:whop' => ['whop'],
            'ecommerce:zen-cart' => ['Zen Cart'],
            'ecommerce:quick-cart' => ['Quick Cart'],
            'ecommerce:shopfiy' => ['Shopfiy'],
            'ecommerce:weebly' => ['Weebly'],
            'ecommerce:bigcommerce' => ['BigCommerce'],
            'ecommerce:square-online-store' => ['Square Online Store'],

            # Security
            'security:imunify360' => ['imunify360'],
            'security:sitelock' => 'sitelock',
            'security:bitninja' => 'BitNinja',
            'security:modsecurity' => 'ModSecurity',
            'security:monarx' => 'Monarx',
            'security:eset' => 'ESET',
            'security:Sophos' => 'Sophos',
            'security:trend-micro' => 'Trend Micro',
            'security:bitdefender' => 'Bitdefender',
            'security:botguard' => 'botguard',

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
            'ssl:zerossl' => ["ZeroSSL"],
            'ssl:certum' => ["Certum"],
            'ssl:wisekey' => ["WISeKey"],
            'ssl:actalis' => ["Actalis"],
            'ssl:swisssign' => ["SwissSign"],
            'ssl:buypass' => ["buypass"],
            'ssl:gogetssl' => ["GoGetSSL"],
            'ssl:certsign' => ["certSIGN"],
            'ssl:netlock' => ["NETLOCK"],

            # SEO
            'seo' => ['seo tools'],
            'seo:marketgoo' => ['marketgoo'],
            'seo:rankingcoach' => ['rankingCoach', 'ranking coach'],
            'seo:semrush' => ['semrush'],
            'seo:yoast' => ['yoast'],
            'seo:ahrefs' => ['ahrefs'],
            'seo:moz' => ['moz'],
            'seo:screamingfrog' => ['Screamingfrog'],
            'seo:majesticseo' => ['MajesticSEO'],
            'seo:spyfu' => ['SpyFu'],
            'seo:seoptimer' => ['SEOptimer'],
            'seo:brightedge' => ['BrightEdge'],
            'seo:boostability' => ['Boostability'],
            'seo:seoclarity' => ['seoClarity'],
            'seo:xovi' => ['XOVI'],
            'seo:letterdrop' => ['letterdrop'],
            'seo:clearscope' => ['clearscope'],
            'seo:serpstat' => ['Serpstat'],
            'seo:se-ranking' => ['SE Ranking'],
            'seo:seo-powersuite' => ['SEO PowerSuite'],
            'seo:rank-math' => ['Rank Math'],
            'seo:rankiq' => ['RankIQ'],
            'seo:seobility' => ['Seobility'],
            'seo:sitechecker' => ['Sitechecker'],
            'seo:seobase' => ['Seobase'],

            # Monitoring
            'monitoring' => 'Uptime Monitoring',
            'monitoring:360-monitoring' => '360 Monitoring',
            'monitoring:koality' => ['koality', 'Site Quality Monitoring'],
            'monitoring:nagios' => 'nagios',
            'monitoring:zabbix' => 'zabbix',
            'monitoring:icinga' => 'icinga',
            'monitoring:splunk' => 'splunk',
            'monitoring:appdynamics' => 'AppDynamics',
            'monitoring:new-relic' => 'New Relic',
            'monitoring:prometheus' => 'Prometheus',
            'monitoring:grafana' => 'Grafana',
            'monitoring:prtg-network-monitor' => 'PRTG Network Monitor',
            'monitoring:checkmk' => 'Checkmk',

            # Cloud
            'cloud' => ['cloud computing'],
            'cloud:aws' => ['aws', 'ec2'],
            'cloud:azure' => ['azure'],
            'cloud:google' => ['google cloud'],
            'cloud:alibaba' => ['alibaba cloud'],
            'cloud:oracle' => ['oracle cloud'],

            # Content Management Systems
            'cms' => ['content management system', 'cms'],
            'cms:wordpress' => ['wordpress'],
            'cms:joomla' => ['joomla'],
            'cms:drupal' => ['drupal'],
            'cms:typo3' => ['Typo3'],
            'cms:sitecore' => ['Sitecore'],
            'cms:sitejet' => ['sitejet'],

            # WordPress Tools
            'wordpress:wp-squared' => ['WP Squared', 'WPSquared'],
            'wordpress:wp-guardian' => ['WP Guardian', 'WPGuardian'],
            'wordpress:wp-toolkit' => ['WP toolkit', 'WordPress Toolkit', 'WPToolkit'],
            'wordpress:runcloud' => ['RunCloud'],
            'wordpress:serverpilot' => ['ServerPilot'],
            'wordpress:wp-cli' => ['WP-CLI'],
            'wordpress:wp-remote' => ['WP Remote'],
            'wordpress:infinitewp' => ['InfiniteWP'],
            'wordpress:managewp' => ['ManageWP'],
            'wordpress:mainwp' => ['MainWP'],
            'wordpress:wp-engine' => ['WP Engine'],
            'wordpress:stackk' => ['Stackk'],
            'wordpress:wpblazer' => ['WPBlazer'],
            'wordpress:commandwp' => ['CommandWP'],
            'wordpress:siterack' => ['Siterack'],
            'wordpress:wp-stagecoach' => ['WP Stagecoach'],
            'wordpress:prettywp' => ['PrettyWP'],

            # Storage
            'storage' => ['storage server', 'cloud-speicher', 'cloud storage'],
            'storage:nextcloud' => 'nextcloud',
            'storage:seafile' => 'Seafile',
            'storage:pydio-cells' => 'Pydio Cells',
            'storage:syncthing' => 'Syncthing',
            'storage:cozycloud' => 'CozyCloud',
            'storage:resillio-sync' => 'Resillio Sync',
            'storage:sparkleshare' => 'SparkleShare',
            'storage:freenas' => 'FreeNAS',
            'storage:onedrive' => 'OneDrive',
            'storage:sync_com' => 'sync.com',
            'storage:egnyte' => 'egnyte',
            'storage:pcloud' => 'pCloud',
            'storage:tresorit' => 'tresorit',
            'storage:storpool' => 'StorPool',


            # OS
            'os:cloudlinux' => ['cloudlinux', 'cloud linux'],
            'os:windows' => ['windows'],
            'os:almaLinux' => 'AlmaLinux',
            'os:rocky-linux' => 'Rocky Linux',
            'os:centos' => 'CentOS',
            'os:suse' => 'Suse',
            'os:debian' => 'Debian',
            'os:ubuntu' => 'Ubuntu',
            'os:redhat' => 'RedHat',
            'os:fedora' => 'Fedora',
            'os:freebsd' => 'FreeBSD',
            'os:openbsd' => 'OpenBSD',
            'os:oracle-linux' => 'Oracle Linux',


            # CPU
            'cpu:intel' => ['intel'],
            'cpu:amd' => ['amd'],
            'cpu:arm' => ['ARM'],
            'cpu:sparc' => ['SPARC'],
            'cpu:risc-v' => ['RISC-V'],
            'cpu:nvidia' => ['NVIDIA'],

            # Hosting
            'hosting:colocation' => ['colocation', 'co-location'],

            # Automation
            'automation:ansible' => ['ansible'],
            'automation:terraform' => ['terraform'],
            'automation:kubernetes' => ['kubernetes'],
            'automation:chef' => ['chef'],
            'automation:puppet' => ['puppet'],
            'automation:docker' => ['docker'],

            # CDN
            'cdn' => ['cdn'],
            'cdn:cloudflare' => ['cloudflare'],
            'cdn:akamai' => ['akamai'],
            'cdn:fastly' => ['Fastly'],
            'cdn:keycdn' => ['KeyCDN'],
            'cdn:google-cloud-cdn' => ['Google Cloud CDN'],
            'cdn:amazon-cloudFront' => ['Amazon CloudFront'],
            'cdn:microsoft-azure-cdn' => ['Microsoft Azure CDN'],
            'cdn:verizon-media-platform' => ['Verizon Media Platform'],

            #Databases
            'database:mysql' => 'mysql',
            'database:mssql' => ['mssql', 'Microsoft SQL'],
            'database:postgres' => 'postgres',
            'database:mongodb' => 'mongodb',
            'database:sql-lite' => 'SQLite',
            'database:mariadb' => 'MariaDB',
            'database:sql' => 'SQL Hosting',

            # Installer
            'installer:softaculous' => ['Softaculous'],
            'installer:installatron' => ['installatron'],
            'installer:bitnami' => ['Bitnami'],
            'installer:simplescripts' => ['SimpleScripts'],
            'installer:fantastico-de-luxe' => ['Fantastico De Luxe'],
            'installer:moodle' => ['Moodle'],
            'installer:cloudron' => ['Cloudron'],

            # Virtualization
            'virtualization:solusvm' => ['SolusVM'],
            'virtualization:virtuozzo' => ['Virtuozzo'],
            'virtualization:proxmox' => ['Proxmox'],
            'virtualization:vmware' => ['VMware'],
            'virtualization:openstack' => ['OpenStack'],
            'virtualization:hyper-v' => ['Hyper-V'],
            'virtualization:kvm' => ['KVM'],
            'virtualization:lxc' => ['LXC'],
            'virtualization:ovirt' => ['oVirt'],
            'virtualization:xenserver' => ['XenServer'],

            # Social Media Management
            'social-media:socialbee' => ['socialbee', 'social bee'],
            'social-media:hootsuite' => ['Hootsuite'],
            'social-media:zoho-social' => ['Zoho Social'],
            'social-media:social-pilot' => ['Social Pilot'],

            # Server
            'server:root' => 'root server',
            'server:dedicated' => 'dedicated server',
            'server:vps' => ['vps', 'vServer'],
            'server:managed-server' => 'managed server',

            # Features
            'features:domain-name-generator' => 'domain name generator',
            'features:reseller' => 'reseller hosting',
            'features:ssd' => 'ssd',
            
            'email' => ['cloud email'],
        ]
    ];
}
