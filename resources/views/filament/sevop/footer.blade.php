@php
    $footerConfig = config('footer.copyright');
    $layoutConfig = config('footer.layout', []);

    $year = $footerConfig['show_year'] ? date('Y') : '';
    $prefixo = $footerConfig['prefixo'] ?? '';
    $author = $footerConfig['author'] ?? 'Desenvolvedor';
    $text = $footerConfig['text'] ?? '';

    $isFixed = $layoutConfig['fixed'] ?? true;
    $footerHeight = $layoutConfig['height'] ?? '60px';
    $zIndex = $layoutConfig['z_index'] ?? 40;
    $hasBlur = $layoutConfig['blur_effect'] ?? true;
    $hasShadow = $layoutConfig['shadow'] ?? true;
    $hasHover = $layoutConfig['hover_effect'] ?? true;
@endphp

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ajusta o padding-bottom dinamicamente baseado na altura real do rodapé
            function adjustBodyPadding() {
                const footer = document.querySelector('.fi-footer');
                if (footer && footer.dataset.fixed === 'true') {
                    const footerHeight = footer.offsetHeight;
                    document.body.style.paddingBottom = (footerHeight + 20) + 'px';
                }
            }

            // Ajusta no carregamento
            adjustBodyPadding();

            // Ajusta quando a janela é redimensionada
            window.addEventListener('resize', adjustBodyPadding);

            // Observa mudanças no tema
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'data-theme') {
                        setTimeout(adjustBodyPadding, 100);
                    }
                });
            });

            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['data-theme']
            });
        });
    </script>
@endpush

<footer class="fi-footer bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700"
        data-fixed="{{ $isFixed ? 'true' : 'false' }}"
        style="--footer-height: {{ $footerHeight }}; --footer-z-index: {{ $zIndex }};">
    <div class="fi-footer-content px-4 py-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-center">
            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                © {{ $year }} {{ $prefixo }} {{ $author }}. {{ $text }}
            </p>
        </div>
    </div>
</footer>

@push('styles')
    <style>
        :root {
            --footer-height: {{ $footerHeight }};
            --footer-z-index: {{ $zIndex }};
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-bottom: var(--footer-height);
        }

        .fi-footer[data-fixed="true"] {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            z-index: var(--footer-z-index) !important;

            @if($hasBlur)
                backdrop-filter: blur(8px) !important;
            @endif

            @if($hasShadow)
                box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1) !important;
            @endif

        transition: background-color 0.2s ease,
            border-color 0.2s ease,
            color 0.2s ease
                   @if($hasHover), transform 0.2s ease @endif !important;
        }

        @if($hasHover)
            .fi-footer[data-fixed="true"]:hover {
            transform: translateY(-1px);
        }
        @endif

         /* === PASSO 1: Layout Base === */

        .fi-layout {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .fi-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            /* Adiciona padding-bottom extra para garantir espaço */
            padding-bottom: 20px;
        }

        .fi-main-content {
            flex: 1;
            /* Garante que o conteúdo não fique sobreposto */
            margin-bottom: 20px;
        }

        /* === PASSO 2: Rodapé Fixo === */
        .fi-footer {
            /* Posicionamento fixo na parte inferior */
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;

            /* Z-index para ficar acima do conteúdo */
            z-index: 40 !important;

            /* Transições suaves para mudanças de tema */
            transition: background-color 0.2s ease,
            border-color 0.2s ease,
            color 0.2s ease,
            box-shadow 0.2s ease !important;

            /* Sombra sutil para destacar do conteúdo */
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1) !important;

            /* Remove margin-top que era usado no sticky footer */
            margin-top: 0 !important;
        }

        /* === PASSO 3: Temas (Claro/Escuro) === */
        [data-theme="light"] .fi-footer {
            background-color: rgba(249, 250, 251, 0.95) !important;
            border-color: rgb(229, 231, 235) !important;
            backdrop-filter: blur(8px) !important;
        }

        [data-theme="dark"] .fi-footer {
            background-color: rgba(17, 24, 39, 0.95) !important;
            border-color: rgb(55, 65, 81) !important;
            backdrop-filter: blur(8px) !important;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.3) !important;
        }

        [data-theme="light"] .fi-footer p {
            color: rgb(75, 85, 99) !important;
        }

        [data-theme="dark"] .fi-footer p {
            color: rgb(156, 163, 175) !important;
        }

        /* === PASSO 4: Responsividade === */
        @media (max-width: 640px) {
            body {
                padding-bottom: 70px; /* Mais espaço em mobile */
            }

            .fi-footer-content {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
                padding-top: 0.75rem !important;
                padding-bottom: 0.75rem !important;
            }

            .fi-footer p {
                font-size: 0.75rem !important;
                line-height: 1.2 !important;
            }
        }

        @media (min-width: 1024px) {
            .fi-footer-content {
                padding-left: 2rem !important;
                padding-right: 2rem !important;
            }
        }

        /* === PASSO 5: Ajustes para Sidebar === */
        /* Quando a sidebar está aberta em desktop */
        @media (min-width: 1024px) {
            .fi-sidebar-open .fi-footer {
                /* Ajusta para não sobrepor a sidebar */
                left: var(--sidebar-width, 280px) !important;
            }
        }

        /* === PASSO 6: Scroll Behavior === */
        .fi-main-content {
            /* Garante scroll suave */
            scroll-behavior: smooth;
            /* Adiciona espaço extra no final para melhor UX */
            padding-bottom: 40px !important;
        }

        /* === PASSO 7: Hover Effects === */
        .fi-footer:hover {
            transform: translateY(-1px);
            transition: transform 0.2s ease;
        }

        /* === PASSO 8: Acessibilidade === */
        @media (prefers-reduced-motion: reduce) {
            .fi-footer,
            .fi-footer * {
                transition: none !important;
                animation: none !important;
            }

            .fi-footer:hover {
                transform: none !important;
            }
        }

    </style>
@endpush
