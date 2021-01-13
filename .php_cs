<?php

/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/?version=2.13#configurator
 * you can change this configuration by importing this file.
 */

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@PhpCsFixer' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['align_double_arrow' => true, 'align_equals' => true],
        'braces' => false,
        'declare_strict_types' => true,
        'linebreak_after_opening_tag' => true,
        'method_argument_space' => ['ensure_fully_multiline' => true],
        'no_mixed_echo_print' => false,
        'no_multiline_whitespace_around_double_arrow' => false,
        'no_php4_constructor' => true,
        'no_superfluous_phpdoc_tags' => true,
        'phpdoc_summary' => false,
        'phpdoc_to_comment' => false,
        'phpdoc_types_order' => ['null_adjustment' => 'always_last'],
        'single_line_comment_style' => false,
        'no_unused_imports' => true,
        'phpdoc_var_without_name' => false,
        'fully_qualified_strict_types' => true,
        ])
    ->setFinder(PhpCsFixer\Finder::create()
        ->in(__DIR__)
        ->exclude('vendor')
    )
;
