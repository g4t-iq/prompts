<?php

namespace Harmony\Prompts\Concerns;

use InvalidArgumentException;
use Harmony\Prompts\ConfirmPrompt;
use Harmony\Prompts\MultiSearchPrompt;
use Harmony\Prompts\MultiSelectPrompt;
use Harmony\Prompts\Note;
use Harmony\Prompts\PasswordPrompt;
use Harmony\Prompts\Progress;
use Harmony\Prompts\SearchPrompt;
use Harmony\Prompts\SelectPrompt;
use Harmony\Prompts\Spinner;
use Harmony\Prompts\SuggestPrompt;
use Harmony\Prompts\Table;
use Harmony\Prompts\TextPrompt;
use Harmony\Prompts\Themes\Default\ConfirmPromptRenderer;
use Harmony\Prompts\Themes\Default\MultiSearchPromptRenderer;
use Harmony\Prompts\Themes\Default\MultiSelectPromptRenderer;
use Harmony\Prompts\Themes\Default\NoteRenderer;
use Harmony\Prompts\Themes\Default\PasswordPromptRenderer;
use Harmony\Prompts\Themes\Default\ProgressRenderer;
use Harmony\Prompts\Themes\Default\SearchPromptRenderer;
use Harmony\Prompts\Themes\Default\SelectPromptRenderer;
use Harmony\Prompts\Themes\Default\SpinnerRenderer;
use Harmony\Prompts\Themes\Default\SuggestPromptRenderer;
use Harmony\Prompts\Themes\Default\TableRenderer;
use Harmony\Prompts\Themes\Default\TextPromptRenderer;

trait Themes
{
    /**
     * The name of the active theme.
     */
    protected static string $theme = 'default';

    /**
     * The available themes.
     *
     * @var array<string, array<class-string<\Harmony\Prompts\Prompt>, class-string<object&callable>>>
     */
    protected static array $themes = [
        'default' => [
            TextPrompt::class => TextPromptRenderer::class,
            PasswordPrompt::class => PasswordPromptRenderer::class,
            SelectPrompt::class => SelectPromptRenderer::class,
            MultiSelectPrompt::class => MultiSelectPromptRenderer::class,
            ConfirmPrompt::class => ConfirmPromptRenderer::class,
            SearchPrompt::class => SearchPromptRenderer::class,
            MultiSearchPrompt::class => MultiSearchPromptRenderer::class,
            SuggestPrompt::class => SuggestPromptRenderer::class,
            Spinner::class => SpinnerRenderer::class,
            Note::class => NoteRenderer::class,
            Table::class => TableRenderer::class,
            Progress::class => ProgressRenderer::class,
        ],
    ];

    /**
     * Get or set the active theme.
     *
     * @throws \InvalidArgumentException
     */
    public static function theme(string $name = null): string
    {
        if ($name === null) {
            return static::$theme;
        }

        if (! isset(static::$themes[$name])) {
            throw new InvalidArgumentException("Prompt theme [{$name}] not found.");
        }

        return static::$theme = $name;
    }

    /**
     * Add a new theme.
     *
     * @param  array<class-string<\Harmony\Prompts\Prompt>, class-string<object&callable>>  $renderers
     */
    public static function addTheme(string $name, array $renderers): void
    {
        if ($name === 'default') {
            throw new InvalidArgumentException('The default theme cannot be overridden.');
        }

        static::$themes[$name] = $renderers;
    }

    /**
     * Get the renderer for the current prompt.
     */
    protected function getRenderer(): callable
    {
        $class = get_class($this);

        return new (static::$themes[static::$theme][$class] ?? static::$themes['default'][$class])($this);
    }

    /**
     * Render the prompt using the active theme.
     */
    protected function renderTheme(): string
    {
        $renderer = $this->getRenderer();

        return $renderer($this);
    }
}
