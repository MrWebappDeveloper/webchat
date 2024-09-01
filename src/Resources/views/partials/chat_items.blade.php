@foreach($items as $item)
    <div class="chat-item display-flex content-between flex-reverse padding-2 bordering-bottom" style="position: relative">
        <div class="glass open-chat-btn" data-chat-id="{{ $item->id }}"></div>
        <div class="display-flex flex-dir-column text-align-right padding-right-5 pos-relative">
            <i class="@if($item->owner->isOnline) fa-solid text-color-success @else fa-regular @endif fa-circle pos-absolute" style="right: 0; top: 12px;"></i>
            <span class="padding-y-2 padding-right-1">{{ $item->owner->name }}</span>
            <span class="text-sm padding-right-1">{{ $item->lastTxtMessage }}</span>
        </div>
        <div class="pos-relative padding-top-2">
            @if($count = $item->unseenMessagesCount(\Modules\Webchat\Entities\ChatMessage::adminRoleName()))
                <span class="pos-absolute back-red text-color-light padding-x-2 broder-rounded" style="bottom: 0px;">{{ $count }}</span>
            @else
                @if($item->hasAnyMessage)
                    <span class="pos-absolute padding-x-2 @if($item->lastMessageStatus == \Modules\Webchat\Entities\ChatMessage::SENT_MESSAGE_STATUS_CODE) i-check @else i-check-double @endif i-simple i-size-2" style="bottom: 0px;"></span>
                @endif
            @endif
            @if($item->hasAnyMessage)
                <span class="text-sm padding-top-5">{{ $item->lastMessageTime }}</span>
            @endif
        </div>
    </div>
@endforeach
