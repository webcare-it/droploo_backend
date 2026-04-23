<div class="table-responsive mt-3">
    <table class="table align-middle mb-0">
        <thead class="table-light">
        <tr>
            <th width="5%">SL</th>
            <th width="10%">Name</th>
            <th>Priority</th>
            <th width="10%">Category Name</th>
            <th width="10%">W.Price</th>
            <th width="10%">D.Price</th>
            <th width="10%">R.Price</th>
            <th width="10%">Status</th>
            <th width="10%" class="text-center">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($products as $product)
            <tr>
                <td>{{ $loop->index+1 }}</td>
                <td>
                    <img src="{{ asset('/product/images/'.$product->image) }}" height="50" width="50" />
                    {{ $product->name }}
                </td>
                <td>{{ $product->priority ?? 'No priority found' }}</td>
                <td>{{ $product->category->name ?? 'No category name found' }}</td>
                <td>{{ $product->wholesale_price ?? 'Not Found' }} Tk.</td>
                <td>{{ $product->discount_price }} Tk.</td>
                <td>{{ $product->regular_price }} Tk.</td>
                <td>
                    @if($product->status == 0)
                        <span class="badge rounded-pill bg-danger">Inactive</span>
                    @else
                        <span class="badge rounded-pill bg-primary">Active</span>
                    @endif
                </td>
                <td>
                    @if ($product->is_variable != 1)
                        <a href="{{ route('products.edit', ['id' => $product->id, 'slug' => $product->slug]) }}" class="badge rounded-pill bg-info">
                            <i class="bx bx-edit-alt" style="font-size: 20px; color: rgb(244, 247, 248);"></i>
                        </a>
                    @else
                        <a href="{{ route('variable.products.edit', ['id' => $product->id, 'slug' => $product->slug]) }}" class="badge rounded-pill bg-info">
                            <i class="bx bx-edit-alt" style="font-size: 20px; color: rgb(244, 247, 248);"></i>
                        </a>
                    @endif
                    {{-- Duplicate --}}
                    <a href="{{ route('products.duplicate', ['id' => $product->id]) }}" class="badge rounded-pill bg-secondary" title="Duplicate Product">
                        <i class="bx bx-copy-alt" style="font-size: 20px; color: white;"></i>
                    </a>
                    @if($product->status == 1)
                        <a href="{{ route('products.active', ['id' => $product->id]) }}" class="badge rounded-pill bg-success">
                            <i class="bx bx-up-arrow-alt" style="font-size: 20px; color: rgb(239, 241, 241);"></i>
                        </a>
                    @else
                        <a href="{{ route('products.inactive', ['id' => $product->id]) }}" class="badge rounded-pill bg-warning">
                            <i class="bx bx-down-arrow-alt" style="font-size: 20px; color: rgb(230, 59, 59);"></i>
                        </a>
                    @endif
                    <a href="{{ route('products.delete', ['id' => $product->id]) }}" onclick="return confirm('Are you sure delete this product ?')" class="badge rounded-pill bg-danger">
                        <i class="bx bx-trash-alt" style="font-size: 20px; color: rgb(243, 237, 237);"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
