<form class="ms-card-search" role="search" data-card-search-form data-search-item-label="{{ $searchItemLabel }}">
    <label for="{{ $searchId }}" class="visually-hidden">Search {{ $searchItemLabel }}</label>
    <div class="ms-search-control">
        <i class="feather-search" aria-hidden="true"></i>
        <input type="search"
            id="{{ $searchId }}"
            placeholder="{{ $searchPlaceholder }}"
            autocomplete="off"
            spellcheck="false"
            data-card-search-input>
        <span class="ms-search-shortcut" data-card-search-shortcut aria-hidden="true">
            Press <kbd>/</kbd> to search
        </span>
        <button type="button" class="ms-search-clear" data-card-search-clear hidden>
            <i class="feather-x" aria-hidden="true"></i>
            <span>Clear</span>
        </button>
    </div>
    <div class="ms-search-summary">
        <span data-card-search-status aria-live="polite">Showing {{ $searchCount }} {{ $searchItemLabel }}</span>
        <small>Results update as you type</small>
    </div>
</form>
