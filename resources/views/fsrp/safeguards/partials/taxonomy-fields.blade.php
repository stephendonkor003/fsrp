<div class="col-md-6">
    <label class="form-label">FSRP Component</label>
    <select name="fsrp_component_id" class="form-select" data-fsrp-component>
        <option value="">N/A</option>
        @foreach ($fsrpComponents as $component)
            <option value="{{ $component->id }}">{{ $component->code }} - {{ $component->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-md-6">
    <label class="form-label">FSRP Subcomponent</label>
    <select name="fsrp_subcomponent_id" class="form-select" data-fsrp-subcomponent>
        <option value="">N/A</option>
        @foreach ($fsrpComponents as $component)
            @foreach ($component->subcomponents as $subcomponent)
                <option value="{{ $subcomponent->id }}" data-component-id="{{ $component->id }}">
                    {{ $subcomponent->code }} - {{ $subcomponent->name }}
                </option>
            @endforeach
        @endforeach
    </select>
</div>
