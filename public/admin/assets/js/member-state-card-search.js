(function () {
    'use strict';

    function normalise(value) {
        return value.toLocaleLowerCase().trim().replace(/\s+/g, ' ');
    }

    function initialiseCardSearch(root) {
        var form = root.querySelector('[data-card-search-form]');
        var input = root.querySelector('[data-card-search-input]');
        var clearButton = root.querySelector('[data-card-search-clear]');
        var shortcutHint = root.querySelector('[data-card-search-shortcut]');
        var status = root.querySelector('[data-card-search-status]');
        var emptyState = root.querySelector('[data-card-search-empty]');
        var cards = Array.prototype.slice.call(root.querySelectorAll('[data-search-item]'));

        if (!form || !input || !clearButton || !status || !emptyState || cards.length === 0) {
            return;
        }

        var itemLabel = form.getAttribute('data-search-item-label')
            || (cards.length === 1 ? 'item' : (cards.length === 4 ? 'dashboard items' : 'reporting sections'));

        function updateShortcutHint() {
            if (shortcutHint) {
                shortcutHint.hidden = input.value !== '' || document.activeElement === input;
            }
        }

        function filterCards() {
            var query = normalise(input.value);
            var visibleCount = 0;

            cards.forEach(function (card) {
                var searchableText = normalise(card.getAttribute('data-search-text') || card.textContent || '');
                var isMatch = query === '' || searchableText.indexOf(query) !== -1;

                card.hidden = !isMatch;
                card.setAttribute('aria-hidden', isMatch ? 'false' : 'true');

                if (isMatch) {
                    visibleCount += 1;
                }
            });

            clearButton.hidden = query === '';
            updateShortcutHint();
            emptyState.hidden = visibleCount !== 0;
            status.textContent = query === ''
                ? 'Showing ' + visibleCount + ' ' + itemLabel
                : visibleCount + ' ' + (visibleCount === 1 ? 'result' : 'results') + ' for “' + input.value.trim() + '”';
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();
        });

        input.addEventListener('input', filterCards);
        input.addEventListener('focus', updateShortcutHint);
        input.addEventListener('blur', updateShortcutHint);
        input.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && input.value !== '') {
                input.value = '';
                filterCards();
            }
        });

        clearButton.addEventListener('click', function () {
            input.value = '';
            filterCards();
            input.focus();
        });

        filterCards();
    }

    function isEditableTarget(target) {
        if (!target) {
            return false;
        }

        var tagName = target.tagName ? target.tagName.toLowerCase() : '';

        return target.isContentEditable
            || tagName === 'input'
            || tagName === 'textarea'
            || tagName === 'select';
    }

    function focusPageSearch(event) {
        if (event.key !== '/'
            || event.defaultPrevented
            || event.ctrlKey
            || event.metaKey
            || event.altKey
            || isEditableTarget(event.target)) {
            return;
        }

        var inputs = Array.prototype.slice.call(document.querySelectorAll('[data-card-search-input]'));
        var input = inputs.find(function (candidate) {
            return !candidate.disabled && candidate.offsetParent !== null;
        });

        if (!input) {
            return;
        }

        event.preventDefault();
        input.focus();
    }

    function start() {
        document.querySelectorAll('[data-card-search]').forEach(initialiseCardSearch);
        document.addEventListener('keydown', focusPageSearch);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
}());
