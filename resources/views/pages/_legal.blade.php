@push('styles')
<style>
.bh-legal-hero {
  padding: var(--space-12) 0 var(--space-6);
  background-color: var(--color-surface);
  border-bottom: 1px solid var(--color-border);
}

.bh-legal-hero .bh-section-kicker {
  display: block;
  margin-bottom: var(--space-2);
}

.bh-legal-body {
  padding: var(--space-8) 0 var(--space-12);
  background-color: var(--color-bg);
}

.bh-legal-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: var(--space-8);
  align-items: start;
}

.bh-legal-toc {
  position: sticky;
  top: 100px;
  padding: var(--space-5);
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--border-radius);
  max-height: calc(100vh - 140px);
  overflow-y: auto;
}

.bh-legal-toc-title {
  font-family: var(--font-meta);
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--color-text-muted);
  margin-bottom: var(--space-3);
  padding-bottom: var(--space-2);
  border-bottom: 1px solid var(--color-border-subtle);
}

.bh-legal-toc-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.bh-legal-toc-link {
  display: block;
  padding: var(--space-2) var(--space-3);
  border-radius: calc(var(--border-radius) - 4px);
  font-family: var(--font-body);
  font-size: 0.875rem;
  color: var(--color-text-muted);
  text-decoration: none;
  transition: var(--transition-base);
  line-height: 1.4;
}

.bh-legal-toc-link:hover {
  background-color: var(--color-surface-subtle);
  color: var(--color-text);
}

.bh-legal-toc-link.active {
  background-color: var(--color-primary);
  color: #FFFFFF;
  font-weight: 600;
}

.bh-legal-content {
  max-width: 760px;
}

.bh-legal-meta {
  font-family: var(--font-meta);
  font-size: 0.8125rem;
  color: var(--color-text-muted);
  margin-bottom: var(--space-6);
  padding-bottom: var(--space-4);
  border-bottom: 1px solid var(--color-border);
}

.bh-legal-section {
  margin-bottom: var(--space-8);
  scroll-margin-top: 100px;
}

.bh-legal-section-title {
  font-family: var(--font-heading);
  font-size: 1.375rem;
  font-weight: 700;
  color: var(--color-primary);
  margin-bottom: var(--space-3);
  display: flex;
  align-items: baseline;
  gap: var(--space-2);
}

.bh-legal-section-title .bh-legal-section-number {
  font-family: var(--font-meta);
  font-size: 0.875rem;
  color: var(--color-accent);
  font-weight: 700;
  flex-shrink: 0;
}

.bh-legal-section p {
  font-family: var(--font-body);
  font-size: 1rem;
  line-height: 1.75;
  color: var(--color-text);
  margin-bottom: var(--space-3);
}

.bh-legal-section ul {
  margin: 0 0 var(--space-3) 0;
  padding-left: var(--space-5);
}

.bh-legal-section li {
  font-family: var(--font-body);
  font-size: 1rem;
  line-height: 1.7;
  color: var(--color-text-muted);
  margin-bottom: var(--space-2);
}

@media (max-width: 991.98px) {
  .bh-legal-layout {
    grid-template-columns: 1fr;
  }

  .bh-legal-toc {
    position: static;
    max-height: none;
  }
}
</style>
@endpush