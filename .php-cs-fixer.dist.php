<?php

declare(strict_types=1);

$config = new PhpCsFixer\Config();
$config->setRules(
	[
		'@PSR1' => true,
		'@PSR2' => true,
		'align_multiline_comment' => true,
		'array_indentation' => true,
		'array_syntax' => [
			'syntax' => 'short',
		],
		'binary_operator_spaces' => [
			'default' => 'single_space',
		],
		'blank_line_after_namespace' => true,
		'blank_line_after_opening_tag' => true,
		'braces' => [
			'position_after_anonymous_constructs' => 'same',
			'position_after_control_structures' => 'same',
			'position_after_functions_and_oop_constructs' => 'same',
		],
		'braces_position' => [
			'functions_opening_brace' => 'same_line',
			'classes_opening_brace' => 'same_line'
		],
		'elseif' => true,
		'encoding' => true,
		'full_opening_tag' => true,
		'function_declaration' => [
			'closure_function_spacing' => 'one',
		],
		'indentation_type' => true,
		'line_ending' => true,
		'list_syntax' => [
			'syntax' => 'short',
		],
		'lowercase_keywords' => true,
		'method_argument_space' => [],
		'no_closing_tag' => true,
		'no_spaces_after_function_name' => true,
		'no_spaces_inside_parenthesis' => true,
		'no_trailing_whitespace' => true,
		'no_trailing_whitespace_in_comment' => true,
		'no_unused_imports' => true,
		'single_blank_line_at_eof' => true,
		'single_class_element_per_statement' => true,
		'single_import_per_statement' => true,
		'single_line_after_imports' => true,
		'switch_case_space' => true,
		'visibility_required' => [
			'elements' => ['property', 'method', 'const']
		],
	])
	->setIndent("\t")
	->setLineEnding("\n")
	->getFinder()
	->notPath('build')
	->notPath('l10n')
	->notPath('gitpod')
	->notPath('src')
	->notPath('vendor')
	->in(__DIR__);
return $config;
