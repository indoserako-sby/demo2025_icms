<div>
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Data Monitoring</h3>
            <div class="card-tools">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search..." wire:model.lazy="search">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="5%"></th>
                            <th width="55%">Name</th>
                            <th width="40%">Condition</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($areas as $area)
                            <tr wire:click="expandArea({{ $area->id }})" style="cursor: pointer;">
                                <td>
                                    <button type="button" class="btn btn-sm btn-link" tabindex="-1">
                                        @if (in_array($area->id, $expandedAreas))
                                            <i class="fas fa-chevron-down"></i>
                                        @else
                                            <i class="fas fa-chevron-right"></i>
                                        @endif
                                    </button>
                                </td>
                                <td>
                                    <strong>{{ $area->name }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <span class="m-2"><i class="fas fa-check-circle text-success"></i>
                                            {{ $area->good_count }} Group</span>
                                        <span class="m-2"><i class="fas fa-exclamation-triangle text-warning"></i>
                                            {{ $area->warning_count }} Group</span>
                                        <span class="m-2"><i class="fas fa-times-circle text-danger"></i>
                                            {{ $area->danger_count }} Group</span>
                                    </div>
                                </td>
                            </tr>

                            @if (in_array($area->id, $expandedAreas))
                                @forelse($area->groups as $group)
                                    <tr class="bg-white" wire:click="expandGroup({{ $group->id }})"
                                        style="cursor: pointer;">
                                        <td style="padding-left: 30px;">
                                            <button class="btn btn-sm btn-link">
                                                @if (in_array($group->id, $expandedGroups))
                                                    <i class="fas fa-chevron-down"></i>
                                                @else
                                                    <i class="fas fa-chevron-right"></i>
                                                @endif
                                            </button>
                                        </td>
                                        <td style="padding-left: 30px;">
                                            <strong>{{ $group->name }}</strong>
                                        </td>
                                        <td>
                                            <div class="d-flex ">
                                                <span class="m-2"><i class="fas fa-check-circle text-success"></i>
                                                    {{ $group->good_count }} Assets</span>
                                                <span class="m-2"><i
                                                        class="fas fa-exclamation-triangle text-warning"></i>
                                                    {{ $group->warning_count }} Assets</span>
                                                <span class="m-2"><i class="fas fa-times-circle text-danger"></i>
                                                    {{ $group->danger_count }} Assets</span>
                                            </div>
                                        </td>
                                    </tr>

                                    @if (in_array($group->id, $expandedGroups))
                                        @forelse($group->assets as $asset)
                                            <tr wire:click="expandAsset({{ $asset->id }})" style="cursor: pointer;">
                                                <td style="padding-left: 45px;">
                                                    <button class="btn btn-sm btn-link">
                                                        @if (in_array($asset->id, $expandedAssets))
                                                            <i class="fas fa-chevron-down"></i>
                                                        @else
                                                            <i class="fas fa-chevron-right"></i>
                                                        @endif
                                                    </button>
                                                </td>
                                                <td style="padding-left: 45px;">
                                                    <strong>{{ $asset->name }}</strong>
                                                </td>
                                                <td>
                                                    <div class="d-flex ">
                                                        <span class="m-2"><i
                                                                class="fas fa-check-circle text-success"></i>
                                                            {{ $asset->good_count }} Parameter</span>
                                                        <span class="m-2"><i
                                                                class="fas fa-exclamation-triangle text-warning"></i>
                                                            {{ $asset->warning_count }} Parameter</span>
                                                        <span class="m-2"><i
                                                                class="fas fa-times-circle text-danger"></i>
                                                            {{ $asset->danger_count }} Parameter</span>
                                                    </div>
                                                </td>
                                            </tr>

                                            @if (in_array($asset->id, $expandedAssets))
                                                @forelse($asset->detailed_data as $data)
                                                    <tr>
                                                        <td style="padding-left: 60px;"></td>
                                                        <td style="padding-left: 60px;">
                                                            @if (strtoupper($data->machineParameter->name ?? '') === strtoupper($data->position->name ?? ''))
                                                                {{ strtoupper($data->datvar->name ?? 'N/A') }}
                                                            @else
                                                                {{ $data->machineParameter->name ?? 'N/A' }} -
                                                                {{ $data->position->name ?? 'N/A' }} -
                                                                {{ $data->datvar->name ?? 'N/A' }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex">
                                                                <span class="m-2">
                                                                    @if ($data->condition === 'good')
                                                                        <i class="fas fa-check-circle text-success"></i>
                                                                    @elseif($data->condition === 'warning')
                                                                        <i
                                                                            class="fas fa-exclamation-triangle text-warning"></i>
                                                                    @elseif($data->condition === 'danger')
                                                                        <i class="fas fa-times-circle text-danger"></i>
                                                                    @endif

                                                                </span>


                                                                <div class="m-2">Value:
                                                                    {{ is_null($data->value) ? 'N/a' : number_format($data->value, 2) }}
                                                                    {{ $data->datvar->unit }}</div>

                                                            </div>

                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center">No data found</td>
                                                    </tr>
                                                @endforelse
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">No assets found</td>
                                            </tr>
                                        @endforelse
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No groups found</td>
                                    </tr>
                                @endforelse
                            @endif
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No data found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
