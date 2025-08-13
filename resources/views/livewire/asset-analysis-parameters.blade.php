<div>
    @if ($assetId)
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="5%">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model.live="selectAll"
                                    id="selectAllParameters">
                                <label class="form-check-label" for="selectAllParameters"></label>
                            </div>
                        </th>
                        <th width="30%">Parameter</th>
                        <th width="20%">Value</th>
                        <th width="20%">Warning Limit</th>
                        <th width="20%">Danger Limit</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parameters as $parameter)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input parameter-checkbox" type="checkbox"
                                        value="{{ $parameter->id }}" wire:model.live="selectedParameters"
                                        id="param-{{ $parameter->id }}">
                                    <label class="form-check-label w-100" for="param-{{ $parameter->id }}"
                                        style="cursor:pointer;">&nbsp;</label>
                                </div>
                            </td>
                            <td>
                                <label class="w-100 mb-0" for="param-{{ $parameter->id }}" style="cursor:pointer;">
                                    @if (isset($parameter->machineParameter) && isset($parameter->position) && isset($parameter->datvar))
                                        @if (strtoupper($parameter->machineParameter->name) === strtoupper($parameter->position->name))
                                            {{ $parameter->datvar->name }}
                                        @else
                                            {{ $parameter->machineParameter->name ?? 'N/A' }} -
                                            {{ $parameter->position->name ?? 'N/A' }} -
                                            {{ $parameter->datvar->name ?? 'N/A' }}
                                        @endif
                                    @else
                                        Parameter #{{ $parameter->id }}
                                    @endif
                                </label>
                            </td>
                            <td>
                                <label class="w-100 mb-0" for="param-{{ $parameter->id }}" style="cursor:pointer;">
                                    @if (!is_null($parameter->value))
                                        {{ number_format($parameter->value, 2) }}
                                        {{ $parameter->datvar->unit ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </label>
                            </td>
                            <td>
                                <label class="w-100 mb-0" for="param-{{ $parameter->id }}" style="cursor:pointer;">
                                    {{ $parameter->warning_limit ?? 'N/A' }} {{ $parameter->datvar->unit ?? 'N/A' }}
                                </label>
                            </td>
                            <td>
                                <label class="w-100 mb-0" for="param-{{ $parameter->id }}" style="cursor:pointer;">
                                    {{ $parameter->danger_limit ?? 'N/A' }} {{ $parameter->datvar->unit ?? 'N/A' }}
                                </label>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-icon btn-outline-primary"
                                    wire:click="openEditModal({{ $parameter->id }})" title="Edit Parameter Limits">
                                    <i class="ti ti-pencil"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-3">No parameters found for this asset.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-5">
            <i class="ti ti-clipboard-list mb-3" style="font-size: 2rem;"></i>
            <h6>Select an asset to view its parameters</h6>
        </div>
    @endif
</div>
