@push('styles')
    <style>
        .corsec-dashboard {
            display: grid;
            gap: 1rem;
        }

        .corsec-hero {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            border: 1px solid rgba(15, 23, 42, 0.08);
            background: linear-gradient(135deg, #0f766e 0%, #0ea5e9 100%);
            color: #f8fafc;
        }

        .corsec-hero::before,
        .corsec-hero::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.15);
            pointer-events: none;
        }

        .corsec-hero::before {
            width: 280px;
            height: 280px;
            top: -140px;
            right: -100px;
        }

        .corsec-hero::after {
            width: 180px;
            height: 180px;
            bottom: -90px;
            left: -70px;
        }

        .corsec-hero__content {
            position: relative;
            z-index: 1;
            display: grid;
            gap: 1rem;
            padding: 1.25rem;
        }

        .corsec-hero__badge {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            width: fit-content;
            padding: .35rem .7rem;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.28);
            background: rgba(255, 255, 255, 0.15);
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .03em;
            text-transform: uppercase;
        }

        .corsec-hero h2 {
            margin: .35rem 0 0;
            font-size: clamp(1.15rem, 2vw, 1.6rem);
            line-height: 1.35;
            font-weight: 700;
            color: #ffffff;
        }

        .corsec-hero p {
            margin: .4rem 0 0;
            color: rgba(240, 249, 255, 0.92);
            max-width: 58ch;
        }

        .corsec-hero__clock {
            margin-top: .45rem;
            font-size: .82rem;
            font-weight: 500;
            color: rgba(236, 253, 245, 0.95);
        }

        .corsec-hero__summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
        }

        .corsec-summary-item {
            border-radius: .85rem;
            padding: .75rem .9rem;
            border: 1px solid rgba(255, 255, 255, 0.25);
            background: rgba(255, 255, 255, 0.1);
        }

        .corsec-summary-item__label {
            font-size: .73rem;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: rgba(224, 242, 254, 0.95);
            font-weight: 600;
        }

        .corsec-summary-item__value {
            margin-top: .2rem;
            font-size: 1.6rem;
            line-height: 1.15;
            font-weight: 700;
            color: #ffffff;
        }

        .corsec-health-ring {
            position: relative;
            width: 118px;
            height: 118px;
            border-radius: 999px;
            background: conic-gradient(#34d399 calc(var(--health, 0) * 1%), rgba(255, 255, 255, 0.26) 0);
            display: grid;
            place-items: center;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.2);
        }

        .corsec-health-ring::after {
            content: "";
            width: 88px;
            height: 88px;
            border-radius: 999px;
            background: rgba(15, 118, 110, 0.95);
            position: absolute;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.12);
        }

        .corsec-health-ring__content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: #ffffff;
        }

        .corsec-health-ring__value {
            font-size: 1.5rem;
            line-height: 1;
            font-weight: 700;
        }

        .corsec-health-ring__label {
            margin-top: .2rem;
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: rgba(224, 242, 254, 0.95);
        }

        .corsec-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: .6rem;
            align-items: center;
            justify-content: space-between;
        }

        .corsec-filters {
            display: inline-flex;
            flex-wrap: wrap;
            gap: .45rem;
            align-items: center;
        }

        .corsec-filter-btn {
            border: 1px solid #cbd5e1;
            border-radius: 999px;
            background: #f8fafc;
            color: #0f172a;
            font-size: .78rem;
            font-weight: 600;
            padding: .38rem .85rem;
            transition: all .18s ease;
        }

        .corsec-filter-btn:hover {
            border-color: #0ea5e9;
            color: #0369a1;
            transform: translateY(-1px);
        }

        .corsec-filter-btn.is-active {
            border-color: transparent;
            background: linear-gradient(135deg, #0f766e 0%, #0284c7 100%);
            color: #ffffff;
            box-shadow: 0 10px 24px rgba(2, 132, 199, 0.28);
        }

        .corsec-card-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: .9rem;
        }

        .corsec-card {
            border: 1px solid #e2e8f0;
            border-radius: .95rem;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            padding: 1rem;
            display: grid;
            gap: .85rem;
            animation: corsec-fade-up .42s ease both;
            animation-delay: var(--delay, 0ms);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .corsec-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 34px rgba(15, 23, 42, 0.11);
        }

        .corsec-card__head {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: .6rem;
        }

        .corsec-card__title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
        }

        .corsec-card__description {
            margin-top: .2rem;
            font-size: .82rem;
            color: #475569;
        }

        .corsec-card__badge {
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .03em;
            text-transform: uppercase;
            padding: .25rem .62rem;
            white-space: nowrap;
        }

        .corsec-card__badge.is-attention {
            background: #fef3c7;
            color: #92400e;
        }

        .corsec-card__badge.is-clear {
            background: #dcfce7;
            color: #166534;
        }

        .corsec-card__count {
            font-size: 2rem;
            line-height: 1;
            font-weight: 800;
            color: var(--accent, #0f172a);
        }

        .corsec-progress {
            width: 100%;
            height: .5rem;
            border-radius: 999px;
            background: #e2e8f0;
            overflow: hidden;
        }

        .corsec-progress__bar {
            width: 0;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, var(--accent, #0ea5e9) 0%, rgba(255, 255, 255, 0.7) 100%);
        }

        .corsec-breakdown {
            display: grid;
            gap: .72rem;
        }

        .corsec-breakdown-item__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            margin-bottom: .35rem;
        }

        .corsec-breakdown-item__title {
            color: #334155;
            font-size: .82rem;
            font-weight: 600;
        }

        .corsec-breakdown-item__value {
            color: #0f172a;
            font-size: .84rem;
            font-weight: 700;
        }

        .corsec-empty {
            border: 1px dashed #cbd5e1;
            border-radius: .85rem;
            padding: .9rem;
            text-align: center;
            font-weight: 500;
            color: #475569;
            background: #f8fafc;
        }

        @keyframes corsec-fade-up {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (min-width: 900px) {
            .corsec-hero__content {
                grid-template-columns: 1.3fr .7fr;
                align-items: center;
            }

            .corsec-card-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1280px) {
            .corsec-card-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .corsec-card {
                animation: none;
                transition: none;
            }

            .corsec-card:hover {
                transform: none;
            }
        }
    </style>
@endpush

<div class="corsec-dashboard" data-dashboard-root>
    <section class="corsec-hero">
        <div class="corsec-hero__content">
            <div>
                <span class="corsec-hero__badge">Dashboard Operasional Corsec</span>
                <p>
                    Pantau progres incoming letter, outgoing letter, meeting, dan work plan dalam satu layar.
                    @if ($totalOpen > 0)
                        Prioritas saat ini ada di <strong>{{ $dominant['title'] }}</strong> dengan
                        <strong>{{ $dominant['count'] }}</strong> item terbuka.
                    @else
                        Semua modul saat ini dalam kondisi aman, tidak ada antrian terbuka.
                    @endif
                </p>
                <div class="corsec-hero__clock" data-dashboard-clock>Memuat waktu...</div>
            </div>
            <div class="grid gap-3">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="corsec-health-ring" style="--health: {{ $healthScore }}">
                        <div class="corsec-health-ring__content">
                            <div class="corsec-health-ring__value" data-counter="{{ $healthScore }}">
                                {{ $healthScore }}</div>
                            <div class="corsec-health-ring__label">% Health</div>
                        </div>
                    </div>
                    <div class="corsec-hero__summary">
                        <div class="corsec-summary-item">
                            <div class="corsec-summary-item__label">Total Open</div>
                            <div class="corsec-summary-item__value" data-counter="{{ $totalOpen }}">
                                {{ $totalOpen }}
                            </div>
                        </div>
                        <div class="corsec-summary-item">
                            <div class="corsec-summary-item__label">Butuh Aksi</div>
                            <div class="corsec-summary-item__value" data-counter="{{ $attentionServices }}">
                                {{ $attentionServices }}</div>
                        </div>
                        <div class="corsec-summary-item">
                            <div class="corsec-summary-item__label">Aman</div>
                            <div class="corsec-summary-item__value" data-counter="{{ $clearServices }}">
                                {{ $clearServices }}</div>
                        </div>
                        <div class="corsec-summary-item">
                            <div class="corsec-summary-item__label">Total Modul</div>
                            <div class="corsec-summary-item__value" data-counter="{{ $serviceTotal }}">
                                {{ $serviceTotal }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-body corsec-toolbar">
            <div class="corsec-filters" role="tablist" aria-label="Filter dashboard cards">
                <button class="corsec-filter-btn is-active" type="button" data-filter-btn="all">
                    Semua ({{ $serviceTotal }})
                </button>
                <button class="corsec-filter-btn" type="button" data-filter-btn="attention">
                    Butuh Aksi ({{ $attentionServices }})
                </button>
                <button class="corsec-filter-btn" type="button" data-filter-btn="clear">
                    Aman ({{ $clearServices }})
                </button>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach ($cards as $card)
                    <a href="{{ $card['route'] }}" class="btn btn-sm btn-light">
                        {{ $card['action'] }}
                    </a>
                    @if (!empty($card['followup_route']))
                        <a href="{{ $card['followup_route'] }}" class="btn btn-sm btn-warning">
                            {{ $card['followup_action'] }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <section class="corsec-card-grid">
        @foreach ($cards as $card)
            @php
                $state = $card['count'] > 0 ? 'attention' : 'clear';
                $fill = $card['count'] > 0 ? max(8, (int) round(($card['count'] / $maxCount) * 100)) : 0;
            @endphp
            <article class="corsec-card" data-status-card data-state="{{ $state }}"
                style="--accent: {{ $card['accent'] }}; --delay: {{ $loop->index * 80 }}ms;">
                <div class="corsec-card__head">
                    <div>
                        <h3 class="corsec-card__title">{{ $card['title'] }}</h3>
                        <p class="corsec-card__description">{{ $card['description'] }}</p>
                    </div>
                    <span class="corsec-card__badge {{ $state === 'attention' ? 'is-attention' : 'is-clear' }}">
                        {{ $state === 'attention' ? 'Butuh Aksi' : 'Aman' }}
                    </span>
                </div>
                <div class="corsec-card__count" data-counter="{{ $card['count'] }}">{{ $card['count'] }}</div>
                <div>
                    <div class="corsec-progress">
                        <div class="corsec-progress__bar" style="width: {{ $fill }}%;"></div>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        {{ $fill }}% dari backlog tertinggi antar modul.
                    </div>
                </div>
                @if (!empty($card['followup_route']))
                    <div class="flex gap-2">
                        <a href="{{ $card['route'] }}" class="btn btn-sm btn-primary flex-1">
                            Lihat Semua
                        </a>
                        <a href="{{ $card['followup_route'] }}" class="btn btn-sm btn-warning flex-1">
                            {{ $card['followup_action'] }}
                        </a>
                    </div>
                @else
                    <a href="{{ $card['route'] }}" class="btn btn-sm btn-primary w-full">
                        Lihat Detail
                    </a>
                @endif
            </article>
        @endforeach
    </section>

    <div class="corsec-empty hidden" data-empty-state>
        Tidak ada kartu yang cocok dengan filter ini.
    </div>

    <section class="card">
        <div class="card-header">
            <h3 class="card-title">Komposisi Backlog</h3>
        </div>
        <div class="card-body corsec-breakdown">
            @foreach ($cards as $card)
                @php
                    $portion = $totalOpen > 0 ? (int) round(($card['count'] / $totalOpen) * 100) : 0;
                @endphp
                <div class="corsec-breakdown-item">
                    <div class="corsec-breakdown-item__head">
                        <div class="corsec-breakdown-item__title">{{ $card['title'] }}</div>
                        <div class="corsec-breakdown-item__value">{{ $card['count'] }} item ({{ $portion }}%)
                        </div>
                    </div>
                    <div class="corsec-progress">
                        <div class="corsec-progress__bar"
                            style="--accent: {{ $card['accent'] }}; width: {{ $portion }}%;">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const root = document.querySelector('[data-dashboard-root]');
            if (!root) {
                return;
            }

            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const formatter = new Intl.NumberFormat('id-ID');

            root.querySelectorAll('[data-counter]').forEach((el) => {
                const target = Number(el.getAttribute('data-counter') || 0);
                if (!Number.isFinite(target)) {
                    return;
                }

                if (reduceMotion || target === 0) {
                    el.textContent = formatter.format(target);
                    return;
                }

                const duration = Math.min(1300, 550 + (target * 35));
                const startedAt = performance.now();

                const animate = (now) => {
                    const progress = Math.min((now - startedAt) / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    const value = Math.round(target * eased);
                    el.textContent = formatter.format(value);

                    if (progress < 1) {
                        requestAnimationFrame(animate);
                    }
                };

                requestAnimationFrame(animate);
            });

            const buttons = Array.from(root.querySelectorAll('[data-filter-btn]'));
            const cards = Array.from(root.querySelectorAll('[data-status-card]'));
            const empty = root.querySelector('[data-empty-state]');

            const setFilter = (filterName) => {
                let visibleCards = 0;

                cards.forEach((card) => {
                    const show = filterName === 'all' || card.dataset.state === filterName;
                    card.classList.toggle('hidden', !show);
                    if (show) {
                        visibleCards++;
                    }
                });

                buttons.forEach((button) => {
                    button.classList.toggle('is-active', button.dataset.filterBtn === filterName);
                });

                if (empty) {
                    empty.classList.toggle('hidden', visibleCards > 0);
                }
            };

            buttons.forEach((button) => {
                button.addEventListener('click', function() {
                    setFilter(this.dataset.filterBtn || 'all');
                });
            });

            setFilter('all');

            const clockEl = root.querySelector('[data-dashboard-clock]');
            if (clockEl) {
                const clockFormatter = new Intl.DateTimeFormat('id-ID', {
                    weekday: 'long',
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    timeZoneName: 'short'
                });

                const renderClock = () => {
                    clockEl.textContent = clockFormatter.format(new Date());
                };

                renderClock();
                setInterval(renderClock, 60000);
            }
        });
    </script>
@endpush
