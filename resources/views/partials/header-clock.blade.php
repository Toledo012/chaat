<div class="header-clock" aria-label="Fecha y hora locales">
    <time id="headerClockTime" class="header-clock-time" datetime="{{ now()->format('H:i:s') }}">{{ now()->format('H:i:s') }}</time>
    <time id="headerClockDate" class="header-clock-date" datetime="{{ now()->toDateString() }}">{{ now()->format('d/m/Y') }}</time>
</div>
