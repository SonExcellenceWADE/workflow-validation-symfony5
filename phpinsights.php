<?php

declare(strict_types=1);

use NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff;
use SlevomatCodingStandard\Sniffs\Functions\FunctionLengthSniff;
use SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff;
use NunoMaduro\PhpInsights\Domain\Insights\Composer\ComposerMustBeValid;
use SlevomatCodingStandard\Sniffs\Classes\SuperfluousInterfaceNamingSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterNotSniff;
use SlevomatCodingStandard\Sniffs\Classes\SuperfluousAbstractClassNamingSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\DisallowYodaComparisonSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff;


return [
    'preset' => 'symfony',

    'ide' => 'phpstorm',

    'exclude' => [
        'phpinsights.php',
        'src/Kernel.php',
    ],

    'add' => [],

    'remove' => [
        DisallowYodaComparisonSniff::class,
        ComposerMustBeValid::class,
        SuperfluousAbstractClassNamingSniff::class,
        SuperfluousInterfaceNamingSniff::class,
        SpaceAfterNotSniff::class,
        DisallowMixedTypeHintSniff::class,
    ],

    'config' => [
        ForbiddenSetterSniff::class => [
            'exclude' => [
                'src/Entity/',
            ],
        ],
        ForbiddenNormalClasses::class => [
            'exclude' => [
                'src/Entity/',
            ],
        ],
        FunctionLengthSniff::class => [
            'maxLength' => 45,
        ],
        LineLengthSniff::class => [
            'lineLimit' => 120,
            'absoluteLineLimit' => 120,
            'ignoreComments' => false,

        ],
        ParameterTypeHintSniff::class => [
            'exclude' => [],
        ],
        ForbiddenSetterSniff::class => [
            'exclude' => [
                'src/Entity/',
            ],
        ],
        UnusedParameterSniff::class => [
            'exclude' => [
                'src/Security/WebAuthenticator',
                // 'src/Form',
            ],
        ],
        AssignmentInConditionSniff::class => [
            'exclude' => [
                'src/Security',
            ],
        ],

    ],

    'requirements' => [
        'min-quality' => 90,
        'min-complexity' => 90,
        'min-architecture' => 80,
        'min-style' => 90,
        'disable-security-check' => false,
    ],

    'threads' => null,
];
