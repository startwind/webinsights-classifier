<?php

namespace Startwind\WebInsights\Aggregation\Exporter\HtmlExporter;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\Content\LanguageAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\Content\MetaKeywordAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\Domain\TopLevelDomainAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\General\GeneralOverviewAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\General\GeneralUpsellAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\Performance\WebsitePerformanceAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\Performance\WebsiteSizeAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\Persona\WebProsPersonaAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\System\Cms\CmsAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\System\Cms\CmsOverviewAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\System\EcommerceAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\System\Technology\ProgrammingLanguageAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\System\Technology\WebserverAggregator;
use Startwind\WebInsights\Aggregation\Exporter\FinishExporter;
use Startwind\WebInsights\Aggregation\UrlAwareAggregationResult;
use Startwind\WebInsights\Util\FilenameHelper;
use Startwind\WebInsights\Util\StorageHelper;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class HtmlExporter extends FinishExporter implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    const DEFAULT_THEME = 'webinsights';

    private array $defaultOptions = [
        'outputDirectory' => '_results/report/default/',
        'theme' => self::DEFAULT_THEME,
        'theme_options' => ['logo' => 'https://results.webinsights.info/assets/logo.png', 'title' => 'WebInsights: Website Classification Report']
    ];

    private string $headline;
    private string $intro;
    private string $outputDirectory;

    private string $theme;
    private array $themeOptions;

    public function __construct(array $options = [])
    {
        $options = array_merge($this->defaultOptions, $options);
        $this->outputDirectory = FilenameHelper::process($options['outputDirectory']);

        $this->theme = $options['theme'];
        $this->themeOptions = $options['theme_options'];

        if (!file_exists($this->outputDirectory)) {
            mkdir($this->outputDirectory, 0777, true);
        }
    }

    public function finish(int $numberOfProcessedWebsites): void
    {
        $loader = new FilesystemLoader([__DIR__ . '/themes/' . $this->theme, __DIR__ . '/themes/_default/']);

        $twig = new Environment($loader);

        $sections = [];

        $specials = [
            GeneralOverviewAggregator::class => ['template' => 'general_overview.html.twig', 'options' => []],
            GeneralUpsellAggregator::class => ['template' => 'general_upsell.html.twig', 'options' => []],

            WebProsPersonaAggregator::class => ['template' => 'general_persona.html.twig', 'options' => []],
            WebsitePerformanceAggregator::class => ['template' => 'section_bar_graph.html.twig', 'options' => []],
            WebsiteSizeAggregator::class => ['template' => 'section_bar_graph.html.twig', 'options' => []],

            LanguageAggregator::class => ['template' => 'table.html.twig', 'options' => ['limit' => 10, 'keyName' => 'Language']],
            CmsOverviewAggregator::class => ['template' => 'cms_overview.html.twig', 'options' => []],
            TopLevelDomainAggregator::class => ['template' => 'table.html.twig', 'options' => ['limit' => 10, 'keyName' => 'Top Level Domain']],
            WebserverAggregator::class => ['template' => 'image_list.html.twig', 'options' => ['limit' => 10, 'keyName' => 'Web Server']],
            CmsAggregator::class => ['template' => 'image_list.html.twig', 'options' => ['limit' => 10, 'keyName' => 'WordPress Plugin']],
            ProgrammingLanguageAggregator::class => ['template' => 'image_list.html.twig', 'options' => ['limit' => 5, 'keyName' => 'WordPress Plugin']],
            MetaKeywordAggregator::class => ['template' => 'table.html.twig', 'options' => ['limit' => 10, 'keyName' => 'Meta Keyword']],

            EcommerceAggregator::class => ['template' => 'image_list.html.twig', 'options' => ['limit' => 10, 'keyName' => 'E-Commerce System']]
        ];

        $themeSectionFile = __DIR__ . '/themes/' . $this->theme . '/sections.php';
        if (file_exists($themeSectionFile)) {
            $groups = include($themeSectionFile);
        } else {
            $groups = include(__DIR__ . '/themes/_default/sections.php');
        }

        $keys = [];

        foreach ($this->aggregationResults as $key => $aggregationResult) {
            if (array_key_exists($aggregationResult->getGenerator(), $specials)) {
                $special = $specials[$aggregationResult->getGenerator()];
                if (array_key_exists('limit', $special['options'])) {
                    $count = min($special['options']['limit'], count($aggregationResult->getResults()));
                } else {
                    $count = 0;
                }
                $sections[$aggregationResult->getGenerator()] = $twig->render(
                    '/sections/' . $special['template'], [
                        'aggregationResult' => $aggregationResult,
                        'key' => $key,
                        'count' => $count,
                        'options' => $special['options'],
                        'websiteCount' => $numberOfProcessedWebsites,
                        'export' => $this,
                        'filenames' => $this->getFilenames($aggregationResult, false)
                    ]
                );
            } else {
                if (!$aggregationResult->hasMultipleResults()) {
                    if ($aggregationResult->isVisualizable()) {
                        $keys[] = $key;
                        $sections[$aggregationResult->getGenerator()] = $twig->render('/sections/' . $aggregationResult->getVisualizationType() . '.html.twig',
                            [
                                'aggregationResult' => $aggregationResult,
                                'key' => $key,
                                'websiteCount' => $numberOfProcessedWebsites,
                                'options' => $aggregationResult->getVisualizationOptions(),
                                'filenames' => $this->getFilenames($aggregationResult, false)
                            ]);
                    } else {
                        $keys[] = $key;
                        $sections[$aggregationResult->getGenerator()] = $twig->render('/sections/default.html.twig', ['aggregationResult' => $aggregationResult, 'key' => $key, 'websiteCount' => $numberOfProcessedWebsites]);
                    }
                }
            }
        }

        $groupedSections = [];

        foreach ($groups as $subGroupName => $subGroup) {
            foreach ($subGroup as $groupName => $group) {
                foreach ($group as $element) {
                    if (array_key_exists($element, $sections)) {
                        $groupedSections[$subGroupName][$groupName][] = $sections[$element];
                    }
                }
            }
        }

        $groupSection = reset($groups);

        $html = $twig->render('index.html.twig', [
            'sections' => $sections,
            'groupedSections' => $groupedSections,
            'groups' => $groups,
            'active' => array_key_first($groupSection),
            'keys' => $keys,
            'websiteCount' => $numberOfProcessedWebsites,
            'theme_options' => $this->themeOptions
        ]);

        // $dir = '_results/report/' . time();
        $dir = $this->outputDirectory;

        $mainFile = $dir . 'index.html';
        file_put_contents($mainFile, $html);
        $this->logger->info('Successfully exported HTML report to ' . $mainFile . '.');

        foreach ($this->aggregationResults as $aggregationResult) {
            if ($aggregationResult instanceof UrlAwareAggregationResult) {
                $files = StorageHelper::store($aggregationResult, $this->outputDirectory);
                foreach ($files as $group => $file) {
                    $this->logger->info('Successfully exported URL file (' . $group . ') report to ' . $file . '.');
                }
            }
        }
    }

    private function getFilename(AggregationResult $aggregationResult, $key, $withDir = true): string
    {
        $fileName = md5($key . $aggregationResult->getGenerator());

        if ($withDir) {
            return $this->outputDirectory . $fileName;
        } else {
            return $fileName;
        }
    }

    private function getFilenames(AggregationResult $aggregationResult, bool $withDir = true): array
    {
        $filenames = [];
        if ($aggregationResult instanceof UrlAwareAggregationResult) {
            $keys = array_keys($aggregationResult->getUrls());

            foreach ($keys as $key) {
                $filenames[$key] = $this->getFilename($aggregationResult, $key, $withDir);
            }
        }

        return $filenames;
    }

}
