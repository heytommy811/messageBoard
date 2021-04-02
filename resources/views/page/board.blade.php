<div class="board-page">
    <ul class="board-menu-buttons">
        <li class="member">メンバー</li>
        @if ($auth_id == 2 || 4 <= $auth_id)
        <li class="create_message">伝言作成</li>
        @endif
        @if ($auth_id == 3 || 4 <= $auth_id)
        <li class="share">共有</li>
        @endif
        @if ($auth_id == 5)
        <li class="manage_board">管理</li>
        @endif
    </ul>
    <input type="hidden" name="dgb_id" value="{{$dgb_id}}"/>
    @if (0 < count($message_list))
        <div class="message-list">
            <div class="icon-title">
                <h2>伝言</h2>
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="512px" height="512px" viewBox="0 0 512 512" style="width: 32px; height: 32px; opacity: 1;" xml:space="preserve">
                    <g>
                        <path d="M449.891,87.953c-3.766-8.906-10.031-16.438-17.922-21.781c-7.891-5.328-17.5-8.469-27.719-8.469h-42.656
                            v-7.359h-61.828c0.281-2,0.438-4.063,0.438-6.141C300.203,19.828,280.375,0,256,0s-44.203,19.828-44.203,44.203
                            c0,2.078,0.156,4.141,0.438,6.141h-61.828v7.359H107.75c-6.813,0-13.359,1.391-19.281,3.906
                            c-8.906,3.766-16.453,10.031-21.797,17.922c-5.328,7.906-8.469,17.5-8.469,27.719v355.219c0,6.781,1.391,13.344,3.906,19.281
                            c3.766,8.906,10.031,16.438,17.922,21.781c7.906,5.344,17.5,8.469,27.719,8.469h296.5c6.797,0,13.359-1.375,19.281-3.906
                            c8.922-3.75,16.453-10.031,21.797-17.922c5.328-7.891,8.469-17.5,8.469-27.703V107.25
                            C453.797,100.438,452.422,93.891,449.891,87.953z M256,27.797c9.047,0,16.406,7.359,16.406,16.406c0,2.172-0.438,4.234-1.203,6.141
                            h-30.391c-0.781-1.906-1.219-3.969-1.219-6.141C239.594,35.156,246.969,27.797,256,27.797z M424.328,462.469
                            c0,2.813-0.563,5.406-1.578,7.797c-1.5,3.578-4.063,6.672-7.281,8.859c-3.219,2.156-7,3.406-11.219,3.406h-296.5
                            c-2.813,0-5.422-0.563-7.813-1.563c-3.594-1.516-6.672-4.094-8.844-7.297c-2.156-3.219-3.406-7-3.422-11.203V107.25
                            c0-2.813,0.563-5.422,1.578-7.813c1.516-3.594,4.078-6.688,7.281-8.844c3.219-2.156,7-3.406,11.219-3.422h42.656v6.141
                            c0,11.531,9.344,20.875,20.891,20.875h169.422c11.531,0,20.875-9.344,20.875-20.875v-6.141h42.656c2.813,0,5.422,0.563,7.813,1.578
                            c3.578,1.5,6.672,4.063,8.844,7.281s3.422,7,3.422,11.219V462.469z" style="fill: rgb(0, 204, 153);"></path>
                        <rect x="156.141" y="170.672" width="31.625" height="31.625" style="fill: rgb(0, 204, 153);"></rect>
                        <rect x="225.516" y="170.672" width="130.359" height="31.625" style="fill: rgb(0, 204, 153);"></rect>
                        <rect x="156.141" y="264.125" width="31.625" height="31.625" style="fill: rgb(0, 204, 153);"></rect>
                        <rect x="225.516" y="264.125" width="130.359" height="31.625" style="fill: rgb(0, 204, 153);"></rect>
                        <rect x="156.141" y="357.594" width="31.625" height="31.625" style="fill: rgb(0, 204, 153);"></rect>
                        <rect x="225.516" y="357.594" width="130.359" height="31.625" style="fill: rgb(0, 204, 153);"></rect>
                    </g>
                </svg>
            </div>
            <ul>
                @foreach ($message_list as $message)
                    <li>
                        <input type="hidden" name="dgn_id" value="{{$message['dgn_id']}}"/>
                        <div class="message-title-line"><div class="message-title text-overflow-ellipsis">{{$message['title']}}</div></div>
                        <div class="message-detail-line"><div class="message-last-update">{{$message['last_update_dt']}}</div><div class="message-last-detail">{{$message['message']}}</div></div>
                        <ul class="message-options">
                            <li class="message-option"><span>コメント </span><span>{{$message['count_comment']}}件</span></li>
                            <li class="message-option"><span>いいね </span><span>{{$message['count_iine']}} 件</span></li>
                        </ul>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
    <div class="error-message show">伝言はありません。</div>
    @endif
</div>
<div id="share" title="共有URL" style="display: none;">
    <div class="share-dialog">
        <input type="text" readOnly="true" value="" class="share-dialog-text"/>
    </div>

    <ul class="horizon dialog-button-ul">
        <li class="message"><p class="share-description">※URLの有効期限は24時間です。</p>
        <li class="button"><button id="share_copy" class="button bt1 dialog-button reaction">コピー</button></li>
    </ul>
</div>