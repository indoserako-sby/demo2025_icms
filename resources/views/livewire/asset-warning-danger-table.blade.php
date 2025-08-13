<div class="card">
    <div class="card-header">
        <h5 class="card-title">Assets in Warning or Danger Condition</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Asset</th>
                        <th>Condition</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paginatedAssets as $asset)
                        <tr>
                            <td>{{ $asset['name'] }}</td>
                            <td>
                                @if ($asset['condition'] === 'warning')
                                    <span class="badge bg-warning">Warning</span>
                                @else
                                    <span class="badge bg-danger">Danger</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('user.asset-information', ['asset_id' => $asset['id']]) }}"
                                    class="btn btn-primary btn-sm">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No assets found in warning or danger condition</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $paginatedAssets->links() }}
        </div>
    </div>
</div>
