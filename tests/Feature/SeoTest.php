<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_commercial_pages_have_unique_metadata_and_one_h1(): void
    {
        $routes = ['landing-pages', 'paginas-web', 'tiendas-en-linea', 'paginas-web-merida', 'portafolio', 'precios', 'contacto'];
        $titles = [];
        $descriptions = [];

        foreach ($routes as $routeName) {
            $html = $this->get(route($routeName))->assertOk()->getContent();

            preg_match('/<title>(.*?)<\/title>/s', $html, $titleMatch);
            preg_match('/<meta name="description" content="([^"]+)"/', $html, $descriptionMatch);

            $this->assertSame(1, substr_count($html, '<h1'));
            $this->assertStringContainsString('<link rel="canonical" href="'.route($routeName).'">', $html);
            $this->assertStringContainsString('application/ld+json', $html);
            $this->assertLessThanOrEqual(65, mb_strlen($titleMatch[1] ?? ''));
            $this->assertGreaterThanOrEqual(120, mb_strlen($descriptionMatch[1] ?? ''));
            $this->assertLessThanOrEqual(165, mb_strlen($descriptionMatch[1] ?? ''));
            $titles[] = $titleMatch[1] ?? '';
            $descriptions[] = $descriptionMatch[1] ?? '';
        }

        $this->assertCount(count($titles), array_unique($titles));
        $this->assertCount(count($descriptions), array_unique($descriptions));
    }

    public function test_professional_pages_alias_redirects_permanently(): void
    {
        $this->get('/paginas-web-profesionales')
            ->assertStatus(301)
            ->assertRedirect('/paginas-web');
    }

    public function test_sitemap_only_contains_canonical_commercial_pages(): void
    {
        $response = $this->get(route('sitemap'))->assertOk();

        foreach (['home', 'landing-pages', 'paginas-web', 'tiendas-en-linea', 'paginas-web-merida', 'portafolio', 'precios', 'contacto'] as $routeName) {
            $response->assertSee(route($routeName), false);
        }

        $response->assertDontSee(route('privacy'), false)
            ->assertDontSee('/paginas-web-profesionales', false);
    }

    public function test_robots_blocks_private_and_transactional_areas(): void
    {
        $this->get(route('robots'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /admin/', false)
            ->assertSee('Disallow: /contratar/', false)
            ->assertSee('Sitemap: '.route('sitemap'), false);
    }

    public function test_checkout_and_legal_pages_are_not_indexable(): void
    {
        $this->get(route('privacy'))->assertOk()->assertSee('content="noindex,follow"', false);
        $this->get(route('terms'))->assertOk()->assertSee('content="noindex,follow"', false);
        $this->get(route('checkout.show', 'landing-page'))->assertOk()->assertSee('content="noindex,nofollow"', false);
    }
}
