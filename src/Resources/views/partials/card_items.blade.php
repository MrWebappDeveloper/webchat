@foreach($items as $item)
    <div class="cart-item display-flex content-between flex-reverse padding-x-2 padding-y-4 margin-bottom-1 bordering align-center" data-card-id="{{ $item->id }}">
        <div class="display-flex flex-dir-column text-align-right align-center pos-relative">
            <span>{{ $item->name }}</span>
        </div>
        <div class="pos-relative cursor-pointer padding-top-2">
            <i class="i-trash-danger i-simple i-size-2 text-color-danger padding-right-1 del-card-btn" data-card-id="{{ $item->id }}"></i>
            <i class="i-simple i-modify i-size-2 padding-right-2 text-color-primary update-card-btn" data-card-id="{{ $item->id }}"></i>
            <i class="i-simple i-send i-size-2 padding-right-1 text-color-primary send-card-btn" data-card-id="{{ $item->id }}"></i>
        </div>
    </div>
@endforeach
