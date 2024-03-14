<?php

use Startwind\WebInsights\Aggregation\Aggregator\Content\LanguageAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\Content\MetaKeywordAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\Domain\TopLevelDomainAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\General\GeneralOverviewAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\General\GeneralUpsellAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\General\RawDataAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\Performance\WebsitePerformanceAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\Performance\WebsiteSizeAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\Persona\WebProsPersonaAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\System\Cms\CmsAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\System\Cms\CmsOverviewAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\System\Cms\WordPress\WordPressPluginAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\System\EcommerceAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\System\Hosting\IspAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\System\Plugin\PluginAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\System\Service\EmailService\EmailServiceAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\System\Technology\CDN\CDNAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\System\Technology\ProgrammingLanguageAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\System\Technology\SSLCertificate\SSLCertificateIssuerAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\System\Technology\WebserverAggregator;

return [
    'Summary' => [
        'General' => [
            GeneralOverviewAggregator::class
        ],
        'Personas' => [
            WebProsPersonaAggregator::class
        ],
        'Cross-selling' => [
            GeneralUpsellAggregator::class
        ],
    ],
    'Aggregation' => [
        'Content Management' => [
            CmsOverviewAggregator::class,
            CmsAggregator::class,
            WordPressPluginAggregator::class
        ],
        'E-Commerce' => [
            EcommerceAggregator::class
        ],
        'Performance' => [
            WebsitePerformanceAggregator::class,
            WebsiteSizeAggregator::class,
        ],
        'Content' => [
            LanguageAggregator::class,
            MetaKeywordAggregator::class,
            PluginAggregator::class,
        ],
        'Technology' => [
            ProgrammingLanguageAggregator::class,
            WebserverAggregator::class,
            EmailServiceAggregator::class,
        ],
        'Hosting' => [
            TopLevelDomainAggregator::class,
            IspAggregator::class,
            CDNAggregator::class,
            SSLCertificateIssuerAggregator::class
        ]
    ]
];
